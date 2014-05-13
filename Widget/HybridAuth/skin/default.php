<div class="ipHybridauthContainer">
    <div class="ipHybridauth">
    <?php
    $imgDir = ipFileUrl('Plugin/HybridAuth/Widget/HybridAuth/assets/img/');
    if ($isUserConnected) {
        echo '<div class="info">User is connected via ';
        echo $serviceName;
        echo '</div>';
    }

    if ($useFacebook){
        echo '<div class="_button"><a href="?auth=Facebook"><button><img src="'.$imgDir.'fb.svg" height="30" alt="Login via Facebook"></button></a></div>';
    }

    if ($useGoogleplus){
        echo '<div class="_button"><a href="?auth=Google"><button><img src="'.$imgDir.'googleplus.svg" height="30" alt="Login via Google"></button></a></div>';
    }

    if ($useGithub){
        echo '<div class="_button"><a href="?auth=GitHub"><button><img src="'.$imgDir.'github.png" height="30" alt="Login via GitHub"></button></a></div>';
    }

    ?>

    </div>
</div>