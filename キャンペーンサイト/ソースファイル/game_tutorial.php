<?php 
// セッションを使うことを宣言
session_start();
   //　ログインされていない場合は強制的にログインpageにリダイレクト
   if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
} 
//共通ヘッダの読み込み    
include(dirname(__FILE__) . '/header.php');
// //DB情報読み込み(他のHPファイル読み込み)
require_once('api/config.php');
?>

<body id="tutorial">
    <div class="frame">
        <div class="images">
            <img class="bg" src="img/etc/tu_bg.png">
            <div class="btn-backtop">
            <a href="level_select.php">
            <button type="button" class="btn-back">もどる</button>
            </div>
        </a>
        </div>

    

        
        

        <!-- 
           
            <img class="senro"src="img/tutorial/senro.png">

            <img class="densya2"src="img/tutorial/densya_gyaku.png">
            <img class="senro2"src="img/tutorial/senro.png">
         -->
    </div>
</body>