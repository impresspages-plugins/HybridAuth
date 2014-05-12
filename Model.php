<?php
namespace Plugin\HybridAuth;


class Model {

    public function __construct()
    {
        require_once(ipFile("Plugin/HybridAuth/lib/hybridauth/Hybrid/Auth.php"));
    }

    public static function getSettings($serviceName)
    {

        switch ($serviceName) {
            case 'Google':
                $keys = array("id" => ipGetOption('HybridAuth.googleClientId'), "secret" => ipGetOption("HybridAuth.googleSecret"));
                break;
            case 'Facebook':
                $keys = array("id" => ipGetOption('HybridAuth.facebookApiKey'), "secret" => ipGetOption("HybridAuth.facebookSecret"));
                break;
            case 'GitHub':
                $keys = array("id" => ipGetOption('HybridAuth.githubId'), "secret" => ipGetOption("HybridAuth.githubSecret"));
                break;
            default:
                $keys = null;
                break;
        }

        return $keys;

    }

    public static function authenticate($serviceName)
    {

        if (ipRequest()->getRequest('auth')) {

            try {

                $serviceSettings = array(
                    "enabled" => true,
                    "keys" => self::getSettings($serviceName),
                );

                $authUrl = ipActionUrl(array('pa' => 'HybridAuth.callback', 'handshake' => '1'));

                $config = array(
                    "base_url" => $authUrl,
                    'providers' => array($serviceName => $serviceSettings),
                    'debug_mode' => true,
                    "debug_file" => "c:/wamp/www/dzebug.txt",

                );
                // create an instance for Hybridauth with the configuration file path as parameter
                $hybridauth = new \Hybrid_Auth($config);

                $service = $hybridauth->authenticate($serviceName);

                // get the user profile
                $service_user_profile = $service->getUserProfile();


//                echo "Ohai there! U are connected with: <b>{$service->id}</b><br />";
//                echo "As: <b>{$service_user_profile->displayName}</b><br />";
//                echo "And your provider user identifier is: <b>{$service_user_profile->identifier}</b><br />";

                // debug the user profile
//                print_r($service_user_profile);

//                 exp of using the twitter social api: Returns settings for the authenticating user.
//                $account_settings = $service->api()->get('account/settings.json');

                // print recived settings
//                echo "Your account settings on " . $serviceName . " : " . print_r($account_settings, true);


            } catch (Exception $e) {
                // Display the recived error,
                // to know more please refer to Exceptions handling section on the userguide
                switch ($e->getCode()) {
                    case 0 :
                        $errMsg = "Unspecified error.";
                        break;
                    case 1 :
                        $errMsg = "Hybriauth configuration error.";
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

            $oauthId = $service_user_profile->identifier;

            return $oauthId;
        } else {
            return false;
        }
    }

    public static function authorize($userOauthProvider, $userOauthUid){

        $ipUid = \Plugin\HybridAuth\Service::userExists($userOauthProvider, $userOauthUid);

        if (!$ipUid){

            // Create IP user
            try{
                // Random user name
                $userName = 'ha_'.\Plugin\HybridAuth\Service::generatePassword(14, 0);
                $password = \Plugin\HybridAuth\Service::generatePassword();

                $userEmail = $userName.'@example.com';
                $ipUid = \Plugin\User\Service::add($userName, $userEmail, $password);

            }catch (\Exception $e){
                ipLog()->error('Error adding IP user via HybridAuth plugin. User: `{userName}`, e-mail: `{email}`.',
                    array('userName' => $userName, 'exception' => $e));
                return false;
            }

            \Plugin\HybridAuth\Service::createOauthUser($userOauthProvider, $userOauthUid, $ipUid);
            $loggedInUid = \Plugin\User\Service::login($ipUid);

        }else{

            $loggedInUid = \Plugin\User\Service::login($ipUid);
        }

        return $loggedInUid;

    }

    public static function serviceSelectForm(){
        $form = new \Ip\Form();

        $form->addField(new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'useFacebook',
                'label' => 'Facebook',
                'checked' => 1
            )
        ));

        $form->addField(new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'useGoogleplus',
                'label' => 'GooglePlus',
                'checked' => 1
            )
        ));

        $form->addField(new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'useGithub',
                'label' => 'GitHub',
                'checked' => 1
            )
        ));

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
            "debug_mode" => false,
            "debug_file" => "c:/wamp/www/dzebug.txt",
        );
        // create an instance for Hybridauth with the configuration file path as parameter
        $hybridauth = new \Hybrid_Auth($config);
        $providers = $hybridauth->getProviders();
        $isConnected = $hybridauth->isConnectedWith($serviceName);

        return $isConnected;
    }

} 