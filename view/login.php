<div class="ipHybridauthContainer clearfix">
    <div class="ipHybridauth">
        <?php
        $imgDir = ipFileUrl('Plugin/HybridAuth/assets/img/');

        foreach ($allServiceNames as $serviceName){
            if ($use[$serviceName]){
                echo '<div class="_button"><a href="' . ipRouteUrl('HybridAuth_login', array('service' => $serviceName)) . '" class="ips'.$serviceName.
                    '"><img src="'.$imgDir.strtolower($serviceName).'.svg" height="30" alt="Login via '.$serviceName.'"></a></div>';
            }
        }


        ?>

    </div>
</div>