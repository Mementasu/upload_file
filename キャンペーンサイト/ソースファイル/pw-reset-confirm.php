<?php
// 言語、内部エンコーディングを指定
mb_language("japanese");
mb_internal_encoding("UTF-8");

// //前の画面のURLを取得
// $motourl = $_SERVER['HTTP_REFERER'];

session_start();

//ログインしてなければはじく
if (!isset($_GET["id"]) || !$_SESSION["accessFlg3"]) {

    header("Location: login.php");
    // セッション破壊
    session_destroy();
    exit();
} else {
    //トップ画面に戻す
    if (isset($_POST['go-top'])) {
        $_SESSION["accessFlg"] = false; 
        $_SESSION["accessFlg2"] = false; 
        $_SESSION["accessFlg3"] = false; 
        header("Location: campaign_top.php");
        // セッション破壊
        session_destroy();
    }
}
// 共通ヘッダの読み込み
include(dirname(__FILE__) . "/header.php");

?>

<body id="pw-reset-confirm">
    <div class="frame">
        <form class="form" action="pw-reset-confirm.php" method=POST>
            <div class="header"><img src="img/pw-logo.png"></div>

            <div class="box">
                <img class="bg" src="img/etc/box.png">
                <p class="text">変更が完了しました。</p>
            </div>

            <div class="btn-reset-center">
                <!-- ログイン画面遷移ボタン -->
                <input class="btn-top" type="submit" name="go-top" value="ログイン画面">
            </div>

            <div class="footer">
                <a class="gotop" href="campaign_top.php">トップにもどる</a>
            </div>
        </form>
    </div>
</body>