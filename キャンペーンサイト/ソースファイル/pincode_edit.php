<?php
// //DB情報読み込み(他のHPファイル読み込み)
require_once('api/config.php');

$id = $_GET["id"];
$accessFlg2 = false;

ini_set('session.gc_maxlifetime', 180); // セッションの有効時間(秒)
session_start();

//アクセスフラグがfalseなら(URLを打ち込んで直接アクセスしたら)
if(!$_SESSION["accessFlg"]){
    //リダイレクト
    header('Location: login.php');
    exit();
}
//変更コードを入力して次へボタンを押したら
if (isset($_POST['pincode-edit'])) {

//打ち込まれたPINコードを取得
    $pinCode = filter_var($_POST['pinCode'], FILTER_SANITIZE_STRING);
    try {
        // DBへ接続
        $pdo = new PDO($dsn, $db_user, $db_password);

        // DBのpinCodeと一致してるか確認
        $sql = "SELECT id FROM `2021_team-b_user` WHERE id=:id AND pinCode = :pinCode AND pincode_create_time > now()-interval 24 hour";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':pinCode', $pinCode, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);

        $stmt->execute();
        $row_count = $stmt->rowCount();

        //一致したら
        if ($row_count == 1) {
            //アクセスフラグをtrue
            $accessFlg2 = true;
            $_SESSION["accessFlg2"] = $accessFlg2;

            // 合致したユーザー情報を取得
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $name = $user_data["name"];

            header('Location: pw-reset.php?id='.$id);        
            exit;
        } else {
            $alertMsg = "入力された変更コードが違うか入力されていません。もう一度正しく入力してください。";
             echo "<script type='text/javascript'>alert('" . $alertMsg . "');</script>";
        }
    } catch (PDOException $e) {
        var_dump($e->getMessage());
    }

    //再送ボタンを押したら
} else if (isset($_POST['pw-edit-again'])) {
    try {

        //セッションからメアドと名前を持ってくる
        $email = $_SESSION["email"];
        $name = $_SESSION["name"];

        //6桁のピンコード
        $pinCode = str_pad(mt_rand(0, 999999), 6, 0, STR_PAD_LEFT);

        $pdo = new PDO($dsn, $db_user, $db_password);

        //pinCodeの値と作成時間を更新
        $sql2 = "UPDATE `2021_team-b_user` SET pinCode=:pinCode, pincode_create_time=now() WHERE id=:id";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt2->bindValue(':pinCode', $pinCode, PDO::PARAM_STR);
        $stmt2->execute();

        //メールの送信
        $dn = $_SERVER["HTTP_HOST"];
        //送信先
        $to = $email;
        //タイトル
        $subject = "パスワードの変更について";
        //本文
        $body = "<html><body>";
        $body .= $name . "様";
        $body .= "<p>変更コード </p>";
        $body .= $pinCode;

        $body .= "</body></html>";

        //メールのプロパティ
        $headers = implode("\r\n", [
            "From: test@test.com",
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8"
        ]);

        //メールを送信する
        mail($to, $subject, $body, $headers);
        $p = "入力されたメールアドレス宛に変更コードを送信しました。";
        $input_box = "■変更コード";
        //pinCode入力画面に移動

        header('Location: pincode_edit.php?id=' . $id);
        exit();
    } catch (PDOException $e) {
        var_dump($e->getMessage());
    }
    //接続を閉じる
    $pdo = null;

}



//共通ヘッダの読み込み    
include(dirname(__FILE__) . '/header.php');
?>

<body id="pincode">
    <div class=frame>
        <form class="form" action="pincode_edit.php?id=<?php echo $id ?>" method=POST>
            <div class="header">
                <p><img src="img/pw-logo.png"></p>
            </div>

            <div class="box">
                <img class="bg" src="img/etc/box.png">
                <p class="box_text">入力されたメールアドレス宛にコードを送信しました。</p>
                <p class="box_text2">以下に<span>変更コード</span>を入力してください。</p>
            </div>

            <!-- 入力欄 -->
            <div class="form-group">
                <!-- ピンコード -->
                <label class="input_box">■コードを入力</label><br>
                <input class="pinCode" type="pinCode" name="pinCode" size="30" maxlength="50">
            </div>

            <div class="sita">
                <!-- 次へボタン -->
                <div class="btn-pincode-center">
                    <input class="btn-pincode-edit" type="submit" name="pincode-edit" value="次へ">
                </div>

                <!-- 再送ボタン -->
                <div class="btn-pincode-center-again">
                    <input class="btn-pincode-edit-again" type="submit" name="pw-edit-again" value="コードを再送">
                </div>
            </div>

            <p class="top"><a href="login.php">ログインページに戻る</a></p>

        </form>
    </div>
</body>

</html>