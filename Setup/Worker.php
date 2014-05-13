<?php

namespace Plugin\HybridAuth\Setup;

class Worker extends \Ip\SetupWorker{

    public function activate()
    {


      $sql =
          "CREATE TABLE IF NOT EXISTS ".ipTable('hybridauth_users')."
           (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `oauth_provider` varchar(16) NOT NULL,
                `oauth_uid` text NOT NULL,
                `ip_uid` int(10) UNSIGNED NOT NULL,
                `profile` text NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

      ipDb()->execute($sql);
    }

    public function deactivate()
    {
        $sql =
            "DROP TABLE IF EXISTS ".ipTable('hybridauth_users');

        ipDb()->execute($sql);
    }

    public function remove()
    {

    }


}