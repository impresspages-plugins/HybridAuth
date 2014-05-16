<?php

namespace Plugin\HybridAuth;

class Event
{
    public static function ipBeforeController()
    {
        ipAddJs('assets/hybridauth.js');
        ipAddCss('assets/hybridauth.css');
    }

    public static function ipUserLogout(){
        Model::logoutHybridAuth();
    }
}
