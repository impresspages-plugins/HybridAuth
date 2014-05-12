<?php
/**
 * Created by PhpStorm.
 * User: Marijus
 * Date: 5/12/14
 * Time: 2:20 PM
 */

namespace Plugin\HybridAuth;


class Service {

    /**
     * Random password generator by Cygnus X1 (stackoverflow.com)
     * @param int $chars
     * @return string
     */

    public static function generatePassword($numAlpha=16,$numNonAlpha=2)
    {
        $listAlpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $listNonAlpha = ',;:!?.$/*-+&@_+;./*&?$-!,';
        $password = str_shuffle(
            substr(str_shuffle($listAlpha),0,$numAlpha) .
            substr(str_shuffle($listNonAlpha),0,$numNonAlpha)
        );
        return $password;
    }


    public static function createOauthUser($userOauthProvider, $userOauthUid, $ipUid){

        $id = ipDb()->insert('hybridauth_users', array(
                'oauth_provider' => $userOauthProvider,
                'oauth_uid' => $userOauthUid,
                'ip_uid' => $ipUid)
        );
        return $id;
    }

    public static function userExists($userOauthProvider, $userOauthUid){

        $ipUid = ipDb()->selectValue('hybridauth_users', 'ip_uid', array(
                'oauth_provider' => $userOauthProvider,
                'oauth_uid' => $userOauthUid)
        );

        return $ipUid;
    }

} 