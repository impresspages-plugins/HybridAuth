<?php

namespace Plugin\HybridAuth;

class Event
{
    public static function ipBeforeController()
    {
        ipAddJs('Widget/HybridAuth/assets/hybridauth.js');
        ipAddCss('Widget/HybridAuth/assets/hybridauth.css');
    }
}
