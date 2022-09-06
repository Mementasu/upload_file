<?php
// 言語、内部エンコーディングを指定
mb_language("japanese");
mb_internal_encoding("UTF-8");

//前の画面のURLを取得
$motourl = $_SERVER['HTTP_REFERER'];

session_start();
$id = $_SESSION["user_id"];

//前の画面のURLがapply.phpじゃなかったらはじく
if (
    //さくら鯖
    $motourl !="https://web-network.sakura.ne.jp/games2021/collabo/team-b/apply.php"//||
    //ローカル
    //$motourl != "http://localhost/%E4%BC%81%E6%A5%AD%E9%80%A3%E6%90%BA/apply.php"
    ) {

    header("Location: login.php");
    exit();
}


//トップ画面に戻す
if (isset($_POST['go-top'])) {
    header("Location: level_select.php");
    // セッション破壊
    session_destroy();
}

// 共通ヘッダの読み込み
include(dirname(__FILE__) . "/header.php");

?>

<body id="apply_confirm">
    <div class="frame">
        <form class="form" action="apply_confirm.php" method=POST>
            <div class="applyTop"><img src="img/applyTop.png"></div>
            <div class="box">
                <img class="bg" src="img/apply/bgBox.png">
                <h2>応募が完了しました</h2>
                <div class="texts">
                    <p class="text">応募完了メールをご登録された</p>
                    <p class="text2">メールアドレスに送信しました。</p>
                    <p class="text3">ご確認ください。</p>
                </div>
            </div>

            <div class="btn-apply-center">
                <!-- 応募ボタン -->
                <input class="btn-top" type="submit" name="go-top" value="トップに戻る">
            </div>


        </form>
    </div>
</body>