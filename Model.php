<?php
namespace Plugin\HybridAuth;


use Aura\Router\Exception;

require_once(ipFile("Plugin/HybridAuth/lib/hybridauth/Hybrid/Auth.php"));

class Model {


    public static function getAllServiceNames(){
        $serviceNames = array('Facebook', 'Google', 'GitHub');
        return $serviceNames;
    }

    public static function getSettings($serviceName)
    {

        $keys = array("id" => ipGetOption('HybridAuth.'.$serviceName.'Id'), "secret" => ipGetOption("HybridAuth.".$serviceName."Secret"));

        return $keys;

    }


    public static function processLogin($data, $serviceName = null){

        $config   = ipFile('Plugin/HybridAuth/lib/hybridauth/config.php');
        require_once( "Plugin/HybridAuth/lib/hybridauth/Hybrid/Auth.php" );


        $data['error'] = false;


        $data['isUserConnected'] = \Plugin\HybridAuth\Model::isUserConnected($serviceName);

        if ($serviceName){
            try{
                $service_user_profile = \Plugin\Hybridauth\Model::authenticate($serviceName);
            }catch(\Exception $e){
                $data['error'] = $e;
            }

            if ($service_user_profile->identifier){
                $authorized = \Plugin\HybridAuth\Model::authorize($serviceName, $service_user_profile);

                if ($authorized){
                    $data['serviceName'] = $serviceName;
                }else{
                    $data['error'] = 'Failed to authorize '.$serviceName;
                }

            }else{
                $data['error'] = 'Failed to authenticate with '.$serviceName;
            }
        }

        return $data;
    }


    public static function isIpUserLoggedIn(){

        return ipUser()->loggedIn();

    }

    private static function getServiceConfig($serviceName){

        $serviceSettings = array(
            "enabled" => true,
            "keys" => self::getSettings($serviceName),
        );

        $authUrl = ipActionUrl(array('pa' => 'HybridAuth.callback'));

        $config = array(
            "base_url" => $authUrl,
            'providers' => array($serviceName => $serviceSettings),
        );

        return $config;
    }

    public static function authenticate($serviceName)
    {

        if ($serviceName) {

            try {
                $config = Model::getServiceConfig($serviceName);

                // create an instance for Hybridauth with the configuration file path as parameter
                $hybridauth = new \Hybrid_Auth($config);

                $service = $hybridauth->authenticate($serviceName);

                // get the user profile
                $service_user_profile = $service->getUserProfile();


            } catch (Exception $e) {
                // Display the recived error,
                // to know more please refer to Exceptions handling section on the userguide
                switch ($e->getCode()) {
                    case 0 :
                        $errMsg = "Unspecified error.";
                        break;
                    case 1 :
                        $errMsg = "Hybridauth configuration error.";
                        break;
                    case 2 :
                        $errMsg = "Provider not properly configured.";
                        break;
                    case 3 :
                        $errMsg = "Unknown or disabled provider.";
                        break;
                    case 4 :
                        $errMsg = "Missing provider application credentials.";
                        break;
                    case 5 :
                        $errMsg = "Authentification failed. "
                            . "The user has canceled the authentication or the provider refused the connection.";
                        break;
                    case 6 :
                        $errMsg = "User profile request failed. Most likely the user is not connected "
                            . "to the provider and he should authenticate again.";
                        $service->logout();
                        break;
                    case 7 :
                        $errMsg = "User not connected to the provider.";
                        $service->logout();
                        break;
                    case 8 :
                        $errMsg = "Provider does not support this feature.";
                        break;
                }

                ipLog()->error(__($errMsg, 'HybridAuth') . ' ' . $e->getMessage(), 'HybridAuth');

                // well, basically your should not display this to the end user, just give him a hint and move on..
//                echo "<br /><br /><b>Original error message:</b> " . $e->getMessage();
            }

            return $service_user_profile;
        } else {
            return false;
        }
    }

    /**
     * Authorize user logged in via OAuth to login as ImpressPages user
     * @param $userOauthProvider
     * @param $serviceUserProfile
     * @return int $loggedInUid|bool Returns logged in uid if authorized or false if not authorized.
     */
    public static function authorize($userOauthProvider, $serviceUserProfile){

        $userOauthUid = $serviceUserProfile->identifier;

        if ($userOauthUid){
            $ipUid = \Plugin\HybridAuth\Service::userExists($userOauthProvider, $userOauthUid);

            if (!$ipUid){

                // Create IP user
                try{

                    // Get user name from profile
                    // Random user name
                    $userName = 'ha_'.\Plugin\HybridAuth\Service::generatePassword(14, 0);
                    $password = \Plugin\HybridAuth\Service::generatePassword();

                    if (isset($serviceUserProfile->email) && (filter_var($serviceUserProfile->email, FILTER_VALIDATE_EMAIL))){

                        $email = $serviceUserProfile->email;

                        if (!Service::emailExists($email)){

                            $ipUid = \Plugin\User\Service::add($userName, $email, $password);

                        }else{
                            throw new \Ip\Exception("User with this e-mail is already registered");
                        }

                    }

                }catch (\Exception $e){
                    ipLog()->error('Error adding IP user via HybridAuth plugin. User: `{userName}`, e-mail: `{email}`.',
                        array('userName' => $userName, 'exception' => $e));
                    return false;
                }

                // Record user profile data on first login

                if (\Plugin\HybridAuth\Service::mapOauthUser($userOauthProvider, $userOauthUid, $ipUid, $serviceUserProfile)){
                    $loggedInUid = \Plugin\User\Service::login($ipUid);
                }else{
                    return false;
                }

            }else{

                $loggedInUid = \Plugin\User\Service::login($ipUid);
            }

            return $loggedInUid;

        }else{
            return false;
        }


    }

    public static function logoutHybridAuth(){

        $services = self::getAllServiceNames();

        foreach ($services as $service){
            $config = self::getServiceConfig($service);
            $ha = new \Hybrid_Auth($config);
            $ha->logoutAllProviders();
        }
    }

    public static function createUserName(){

    }

    public static function serviceSelectForm(){
        $form = new \Ip\Form();


        $serviceNames = self::getAllServiceNames();

        foreach ($serviceNames as $serviceName){

            $form->addField(new \Ip\Form\Field\Checkbox(
                array(
                    'name' => 'use'.ucfirst(strtolower($serviceName)),
                    'label' => $serviceName,
                    'checked' => 1
                )
            ));

        }

        return  $form;

    }



    public static function isUserConnected($serviceName)
    {
        $serviceSettings = array(
            "enabled" => true,
            "keys" => self::getSettings($serviceName),
        );

        $authUrl = ipActionUrl(array('pa' => 'HybridAuth.callback', 'handshake' => '1'));

        $config = array(
            "base_url" => $authUrl,
            'providers' => array($serviceName => $serviceSettings),
            "debug_mode" => false
        );
        // create an instance for Hybridauth with the configuration file path as parameter
        $hybridauth = new \Hybrid_Auth($config);
        $providers = $hybridauth->getProviders();
        $isConnected = $hybridauth->isConnectedWith($serviceName);

        return $isConnected;
    }

} 