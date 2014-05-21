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

        $data = Service::processLogin(
            array(), $service
        );

        if ($data['error']){
            $renderedHtml = ipView('view/error.php', $data)->render();
            return $renderedHtml;
        }elseif ($data['firstLogin']){
            $renderedHtml = ipView('view/firstLogin.php', $data)->render();
            return $renderedHtml;
        }else{
            // Show the page, if already logged in
            return new \Ip\Response\Redirect(ipHomeUrl());
        }
    }
} 