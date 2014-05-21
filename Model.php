<?php
namespace Plugin\HybridAuth;


use Aura\Router\Exception;

require_once(ipFile("Plugin/HybridAuth/lib/hybridauth/Hybrid/Auth.php"));

class Model {


    public static function getAllServiceNames(){
        $serviceNames = array('Facebook', 'Google', 'GitHub');
        return $serviceNames;
    }

    public static function getServiceKeys($serviceName)
    {
        $keys = array("id" => ipGetOption('HybridAuth.'.$serviceName.'Id'), "secret" => ipGetOption("HybridAuth.".$serviceName."Secret"));
        return $keys;
    }

    public static function isIpUserLoggedIn(){
        return ipUser()->loggedIn();
    }

    private static function getServiceSettings($serviceName){

        $settings = array(
            "enabled" => true,
            "keys" => self::getServiceKeys($serviceName),
        );

        $scope = self::getScope($serviceName);
        if ($scope){
            $settings['scope'] = $scope;
        }

        return $settings;
    }

    public static function getServiceConfig($serviceName){

        $serviceSettings = self::getServiceSettings($serviceName);
        $authUrl = ipActionUrl(array('pa' => 'HybridAuth.callback'));

        $config = array(
            "base_url" => $authUrl,
            'providers' => array($serviceName => $serviceSettings),
        );

        return $config;
    }

    private static function getScope($serviceName){
        switch ($serviceName){
            case 'Facebook':
                $scope = "email, user_about_me, user_birthday, user_hometown, user_website";
                break;
            case 'Google':
                $scope = "https://www.googleapis.com/auth/userinfo.profile ". // optional
                "https://www.googleapis.com/auth/userinfo.email";
                break;
            case 'GitHub':
                $scope = false;
                break;
            default:
                $scope = false;
                break;
        }

        return $scope;
    }


    public static function getAuthErrorMessage($e){

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
                break;
            case 7 :
                $errMsg = "User not connected to the provider.";
                break;
            case 8 :
                $errMsg = "Provider does not support this feature.";
                break;
            default:
                $errMsg = '';
            break;
        }

        return $errMsg;
    }

    public static function generateUserName($profile){

        $name = 'ha_'.\Plugin\HybridAuth\Service::generatePassword(14, 0);

        return $name;

    }

    public static function logoutHybridAuth(){

        $services = self::getAllServiceNames();

        foreach ($services as $service){
            $config = self::getServiceConfig($service);
            $ha = new \Hybrid_Auth($config);
            $ha->logoutAllProviders();
        }
    }

    public static function getActiveServices($widgetData, $allServiceNames){

        $activeServices = array();

        foreach ($allServiceNames  as $serviceName){

            $serviceVarName = ucfirst(strtolower($serviceName));

            if (isset($widgetData['use'][$serviceVarName ])){
                $use = $widgetData['use'][$serviceVarName ];
            }else{
                $use = false;
            }
            $activeServices[$serviceName] = $use;
        }

        return $activeServices;
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
        $serviceSettings = self::getServiceSettings($serviceName);

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