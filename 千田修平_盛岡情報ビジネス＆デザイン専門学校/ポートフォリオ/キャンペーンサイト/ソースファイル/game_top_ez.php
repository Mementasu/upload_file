<?php
//共通ヘッダの読み込み    
include(dirname(__FILE__) . '/header.php');

ini_set('session.gc_divisor', 1); // 有効期限が切れたら必ずセッションを削除する
// ini_set('session.gc_maxlifetime', 10); // セッションの有効時間(秒)

ini_set('session.gc_maxlifetime', 1000); // セッションの有効時間(秒)
// セッションを使うことを宣言
session_start();


//　ログインされていない場合は強制的にログインpageにリダイレクト
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    session_destroy();

    exit();
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>マスクポリス
    </title>
    <style type="text/css">
        body {
            background: #000000;
            padding: 0px;
            margin: 0px;
        }
    </style>

    <script type="text/javascript" charset="UTF-8" src="./js/phaser.js"></script>
    <script type="text/javascript" charset="UTF-8" src="./js/common.js"></script>
    <script type="text/javascript" charset="UTF-8" src="./js/title_scene.js"></script>
    <script type="text/javascript" charset="UTF-8" src="./js/result_scene.js"></script>
    <script type="text/javascript" charset="UTF-8" src="./js/main_scene_ez.js"></script>
    <script type="text/javascript" charset="UTF-8" src="./js/game.js"></script>
</head>

</html>