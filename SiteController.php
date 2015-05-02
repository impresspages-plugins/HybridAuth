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
        if (!isset($_SESSION['User_redirectAfterLogin'])) {
            $_SESSION['User_redirectAfterLogin'] = $_SERVER['HTTP_REFERER'];
        }

        $data = Service::processLogin(
            array(),
            $service
        );

        if ($data['error']) {
            $renderedHtml = ipView('view/error.php', $data)->render();
            return $renderedHtml;
        } else {
            $redirect = ipHomeUrl();
            if (isset($_SESSION['User_redirectAfterLogin'])) {
                $redirect = $_SESSION['User_redirectAfterLogin'];
                unset($_SESSION['User_redirectAfterLogin']);
            }
            // Show the page, if already logged in
            return new \Ip\Response\Redirect($redirect);
        }
    }
}
