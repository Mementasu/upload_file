<?php
// 言語、内部エンコーディングを指定
mb_language("japanese");
mb_internal_encoding("UTF-8");

//前の画面のURLを取得
$motourl = $_SERVER['HTTP_REFERER'];

//前の画面のURLがregist.phpじゃなかったらはじく
// if (
//     //さくら鯖
//     $motourl !="https://web-network.sakura.ne.jp/games2021/collabo/team-b/regist.php"//||
//     //ローカル
//     //$motourl != "http://localhost/%E4%BC%81%E6%A5%AD%E9%80%A3%E6%90%BA/apply.php"
//     ) {

//     header("Location: login.php");
//     exit();
// }

//トップ画面に戻す
if (isset($_POST['go-top'])) {
    header("Location: campaign_top.php");
    // セッション破壊
    session_destroy();
}

// 共通ヘッダの読み込み
include(dirname(__FILE__) . "/header.php");

?>

<body id="regist_confirm">
    <div class="frame">

        <form class="form" action="apply_confirm.php" method=POST>
            <div class="registTop"><img src="img/regist_logo.png"></div>

            <div class="box"> <img class="bg" src="img/etc/box.png"></img> </div>

            <div class="main">
                <img class="animal" src="img/campaign_top/animal.png"></img>
                <h2 class="ok">仮登録が完了しました</h2>
                <div class="texts">
                    <p class="text">仮登録完了メールを送信しました。</p>
                    <p class="text2">ご確認ください。</p>
                </div>
            </div>

            <div class="btn-regist-confirm-center">
                <input class="btn-top" type="submit" name="go-top" value="トップに戻る">
            </div>

        </form>

    </div>

</body>