<?php
//さくらサーバ（本番環境）
const PRODUCTION_CONFIG = array(
    // $dsn = 'mysql:dbname=web-network_js;host=mysql57.web-network.sakura.ne.jp';
    // $db_user = 'web-network';
    // $db_password = 'morijyobi-0200021';
    'host' => 'mysql57.web-network.sakura.ne.jp',
    'user' => 'web-network',
    'password' => 'morijyobi-0200021',
    'database' => 'web-network_js',
);


// 開発用サーバ情報(XAMPP)
const DEVELOPMENT_CONFIG = array(
    // $dsn = 'mysql:dbname=web-network_js;host=127.0.0.1:3308';
    // $db_user = 'root';
    // $db_password = 'Hagoromokomachi_1018_1212';
    'host' => '127.0.0.1:3308',
    'user' => 'root',
    'password' => 'Hagoromokomachi_1018_1212',
    'database' => 'web-network_js',
);
?>
