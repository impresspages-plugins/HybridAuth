<?php

// Put this into Controller.php

namespace Plugin\HybridAuth\Widget\HybridAuth;

class Controller extends \Ip\WidgetController
{
    public function generateHtml($revisionId, $widgetId, $data, $skin)
    {


        $config   = ipFile('Plugin/HybridAuth/lib/hybridauth/config.php');
        require_once( "Plugin/HybridAuth/lib/hybridauth/Hybrid/Auth.php" );

        if (isset( $data['loginService'])){
            $listId = $data['loginService'];
            $form = Model::showForm($listId);
            $formHtml = $form->render();
            $data['formHtml'] = $formHtml;

        }else{
            $data['formHtml'] = '';
        }

        $serviceName = ipRequest()->getRequest('auth');

        $data['isUserConnected'] = \Plugin\HybridAuth\Model::isUserConnected($serviceName);

        if ($serviceName){

           $oauthId = \Plugin\Hybridauth\Model::authenticate($serviceName);

           if ($oauthId){
               $authorized = \Plugin\HybridAuth\Model::authorize($serviceName, $oauthId);
               $data['serviceName'] = $serviceName;
           }

        }

        $requestStatus = ipRequest()->getRequest('handshake');
        if ($requestStatus ){
           $isConnected = \Plugin\Hybridauth\Model::isUserConnected($requestStatus);
        }


        return parent::generateHtml($revisionId, $widgetId, $data, $skin);
    }

    public function adminHtmlSnippet()
    {
        $form = \Plugin\HybridAuth\Model::serviceSelectForm();

        $variables = array(
            'form' => $form
        );

        return ipView('snippet/popup.php', $variables)->render();
    }

}
