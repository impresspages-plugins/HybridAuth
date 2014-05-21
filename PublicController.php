<?php
/**
 * Created by PhpStorm.
 * User: Marijus
 * Date: 5/9/14
 * Time: 1:54 PM
 */

namespace Plugin\HybridAuth;


class PublicController extends \Ip\Controller
{
    public function callback(){

        require_once(ipFile("Plugin/HybridAuth/lib/hybridauth/Hybrid/Auth.php" ));
        require_once(ipFile("Plugin/HybridAuth/lib/hybridauth/Hybrid/Endpoint.php" ));

        try{
            \Hybrid_Endpoint::process();
        }catch (\Exception $e){
            $data['error'] = $e;
            $renderedHtml = ipView('view/error.php', $data)->render();
            return $renderedHtml;
        }


    }

}