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


        if (isset($data['useFacebook'])){
            $settings['useFacebook'] = $data['useFacebook'];
        }else{
            $settings['useFacebook'] = false;
        }

        if (isset($data['useGoogle'])){
            $settings['useGoogle'] = $data['useGoogle'];
        }else{
            $settings['useGoogle'] = false;
        }

        if (isset($data['useGithub'])){
            $settings['useGithub'] = $data['useGithub'];
        }else{
            $settings['useGithub'] = false;
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
