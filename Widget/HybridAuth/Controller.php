<?php

// Put this into Controller.php

namespace Plugin\HybridAuth\Widget\HybridAuth;

use Ip\Exception;

class Controller extends \Ip\WidgetController
{
    public function generateHtml($revisionId, $widgetId, $data, $skin)
    {

        $settings['error'] = false;
        $settings['isUserConnected'] = false;


        $allServiceNames = \Plugin\HybridAuth\Model::getAllServiceNames();

        $settings['allServiceNames'] = $allServiceNames;

        foreach ($allServiceNames  as $serviceName){

            $serviceVarName = ucfirst(strtolower($serviceName));

            if (isset($data['use'][$serviceVarName ])){
                $use = $data['use'][$serviceVarName ];
            }else{
                $use = false;
            }
            $settings['use'][$serviceName] = $use;
        }

        $data['settings'] = $settings;

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
