<div class="ipHybridauthContainer">
    <div class="ipHybridauth">
        <?php
        $imgDir = ipFileUrl('Plugin/HybridAuth/assets/img/');

        if ($error){
            echo '<div class="error">'.$error.'</div>';
        }

        if ($isUserConnected) {
            echo '<div class="info">User is connected via ';
            echo $serviceName;
            echo '</div>';
        }

        if ($useFacebook){
            echo '<div class="_button "><a href="' . ipRouteUrl('HybridAuth_login', array('service' => 'Facebook')) . '" class="ipsFacebook"><img src="'.$imgDir.'fb.svg" height="30" alt="Login via Facebook"></a></div>';
        }

        if ($useGoogle){
            echo '<div class="_button"><a href="?auth=Google"><img src="'.$imgDir.'google.svg" height="30" alt="Login via Google"></a></div>';
        }

        if ($useGithub){
            echo '<div class="_button"><a href="?auth=GitHub"><img src="'.$imgDir.'github.png" height="30" alt="Login via GitHub"></a></div>';
        }

        ?>

    </div>
</div>