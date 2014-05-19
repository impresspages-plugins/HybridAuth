<?php
/**
 * Created by PhpStorm.
 * User: Marijus
 * Date: 5/16/14
 * Time: 2:21 PM
 */

namespace Plugin\HybridAuth;

class Slot{

    public static function HybridAuth_login($settings){

        // If user is not connected - show a form
        if (!Model::isIpUserLoggedIn()){
            return ipView('view/login.php', $settings)->render();
        }

    }
}