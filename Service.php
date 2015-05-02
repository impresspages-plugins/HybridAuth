<?php
/**
 * Created by PhpStorm.
 * User: Marijus
 * Date: 5/12/14
 * Time: 2:20 PM
 */

namespace Plugin\HybridAuth;


class Service {

    /**
     * Random password generator by Cygnus X1 (stackoverflow.com)
     * @param int $chars
     * @return string
     */

    public static function generatePassword($numAlpha=16,$numNonAlpha=2)
    {
        $listAlpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $listNonAlpha = ',;:!?.$/*-+&@_+;./*&?$-!,';
        $password = str_shuffle(
            substr(str_shuffle($listAlpha),0,$numAlpha) .
            substr(str_shuffle($listNonAlpha),0,$numNonAlpha)
        );
        return $password;
    }

    /**
     * Maps OAuth user with ImpressPages user ID. Records profile data in JSON format.
     * @param $userOauthProvider
     * @param $userOauthUid
     * @param $ipUid
     * @param null $profile
     * @return mixed
     */
    public static function mapOauthUser($userOauthProvider, $userOauthUid, $ipUid, $profile = null){
        $id = ipDb()->insert('hybridauth_users', array(
                'oauth_provider' => $userOauthProvider,
                'oauth_uid' => $userOauthUid,
                'ip_uid' => $ipUid,
                'profile' => json_encode($profile))
        );
        return $id;
    }

    /**
     *
     */

    public static function userExists($userOauthProvider, $userOauthUid){

        $ipUid = ipDb()->selectValue('hybridauth_users', 'ip_uid', array(
                'oauth_provider' => $userOauthProvider,
                'oauth_uid' => $userOauthUid)
        );

        return $ipUid;
    }

    /**
     * Returns associative array of profile data that user gave permission to access during authentication
     *
     * @param null $ip_uid
     * @return array
     */
    public static function getUserProfile($ip_uid = null)
    {
        if ($ip_uid === null) {
            $ip_uid = ipUser()->userId();
        }
        $results = ipDb()->selectAll('hybridauth_users', array('oauth_provider', 'profile'), array('ip_uid' => $ip_uid));

        $data = array();
        foreach ($results as $r) {
            $data[$r['oauth_provider']] = json_decode($r['profile'], true);
        }

        return $data;
    }

    public static function emailExists($email){

        $user = ipDb()->selectRow('user', array('id', 'email'), array('email' => $email));
        return $user;
    }

    public static function processLogin($data, $serviceName = null){

        require_once( ipFile("Plugin/HybridAuth/lib/hybridauth/Hybrid/Auth.php") );

        $data['error'] = false;

        $data['isUserConnected'] = \Plugin\HybridAuth\Model::isUserConnected($serviceName);

        if ($serviceName){

            try{
                $service_user_profile = \Plugin\Hybridauth\Service::authenticate($serviceName);
            }catch(\Exception $e){
                $data['error'] = $e;
                return $data;
            }

            if ($service_user_profile->identifier){

                $authorized = \Plugin\HybridAuth\Service::authorize($serviceName, $service_user_profile);

                if ($authorized['error']){
                    return $authorized; // Return error
                }else{
                    $data['serviceName'] = $serviceName;

                }

            }else{
                $data['error'] = __('Failed to authenticate with ', 'HybridAuth').$serviceName;
                return $data;
            }
        }

        return $data;
    }


    /**
     * Authorize user logged in via OAuth to login as ImpressPages user
     * @param $userOauthProvider
     * @param $serviceUserProfile
     * @return int $loggedInUid|bool Returns logged in uid if authorized or false if not authorized.
     */
    public static function authorize($userOauthProvider, $serviceUserProfile){

        $data['error'] = false;

        $userOauthUid = $serviceUserProfile->identifier;

        if ($userOauthUid){
            $ipUid = \Plugin\HybridAuth\Service::userExists($userOauthProvider, $userOauthUid);

            if (!$ipUid){

                $data = self::createIpUser($serviceUserProfile);
                if ($data['error']){
                    return $data;
                }else{
                    // No errors
                    // Record user profile data on first login
                    $ipUid = $data['ipUid'];

                    if (\Plugin\HybridAuth\Service::mapOauthUser($userOauthProvider, $userOauthUid, $ipUid, $serviceUserProfile)){

                        \Plugin\User\Service::login($ipUid);
                        $data['ipUid']= $ipUid;
                        return $data;
                    }else{
                        $data['error'] = __('Cannot map OAuth user to ImpressPages user.', 'HybridAuth');
                        return $data;
                    }

                }


            }else{
                \Plugin\User\Service::login($ipUid);
                $data['ipUid']= $ipUid;
                return $data;
            }
        }else{
            return false;
        }

    }


    public static function authenticate($serviceName)
    {

        if ($serviceName) {

            try {
                $config = \Plugin\HybridAuth\Model::getServiceConfig($serviceName);
                // create an instance for Hybridauth with the configuration file path as parameter
                $hybridauth = new \Hybrid_Auth($config);
                $service = $hybridauth->authenticate($serviceName);
                // get the user profile
                $service_user_profile = $service->getUserProfile();

            } catch (Exception $e) {
                // Display the received error,
                // to know more please refer to Exceptions handling section on the userguide

                $errMsg = \Plugin\HybridAuth\Model::getAuthErrorMessage($e);

                if (($e->getCode()==6) && ($e->getCode()==7)){
                    $service->logout();
                }

                ipLog()->error(__($errMsg, 'HybridAuth'), 'HybridAuth');
                return false;

            }

            return $service_user_profile;
        } else {
            return false;
        }
    }
    private static function createIpUser($serviceUserProfile){

        $data['error'] = false;
        // Create IP user
        try{

            // Get user name from profile
            // Random user name
            $userName = \Plugin\HybridAuth\Model::generateUserName($serviceUserProfile);
            $password = \Plugin\HybridAuth\Service::generatePassword();

            if (isset($serviceUserProfile->email) && (filter_var($serviceUserProfile->email, FILTER_VALIDATE_EMAIL))){

                $email = $serviceUserProfile->email;

                $user = Service::emailExists($email);

                // if user with such email doesn't exist, we'll create one
                if (!$user) {
                    $ipUid = \Plugin\User\Service::add($userName, $email, $password);
                } else {
                    if (ipGetOption('HybridAuth.mergeAccounts')) {
                        $ipUid = $user['id'];
                    } else {
                        $data['error'] = __('User with this e-mail is already registered.', 'HybridAuth');
                        $data['isUserConnected'] = false;
                        return $data;
                    }
                }

            } else {
                $data['error'] = __('Error adding user. E-mail is not valid.', 'HybridAuth');
                $data['isUserConnected'] = false;
                return $data;
            }

        } catch (\Exception $e){
            $data['error'] = __('Error adding user: ', 'HybridAuth').$e->getMessage();
            $data['isUserConnected'] = false;
            return $data;
        }

        $data['ipUid'] = $ipUid;
        return $data;
    }
}
