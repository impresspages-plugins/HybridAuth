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

        \Hybrid_Endpoint::process();

    }

    public function testLogin(){

//        $userList = \Plugin\User\Service::getAll();

        $userOauthProvider = 'Google';
        $userOauthUid = 12345;



    }




}