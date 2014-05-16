<?php
/**
 * Created by PhpStorm.
 * User: Marijus
 * Date: 5/16/14
 * Time: 4:04 PM
 */

namespace Plugin\HybridAuth;


class SiteController
{
    public function login($service)
    {
        $data = Model::processLogin(
            array(), $service
        );

        if (!$data['isUserConnected']){
            $renderedHtml = ipView('view/login.php', $data)->render();
            return $renderedHtml;
        }else{
            // Show the page, if already logged in
            return new \Ip\Response\Redirect(ipHomeUrl());
        }
    }
} 