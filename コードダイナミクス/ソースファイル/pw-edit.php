<?php
//共通ヘッダの読み込み    
include(dirname(__FILE__) . '/header.php');

// // //DB情報読み込み(他のHPファイル読み込み)
require_once('api/config.php');

require('db/user.php'); // クラスの読み込み

$p = "送信ボタンを押すと入力いただいた</br>メールアドレス宛に<span>変更コード</span>を送信します。<br\>ご確認をお願い致します。";
$input_box = "■メールアドレス";
$pinCode;
$isMailSend = false;
$accessFlg = false;

// ①メールアドレスを入力してメール送信押下後
if (isset($_POST['pw-edit'])) {


    $isMailSend = false;

    //フォームデータの取得
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    ini_set('session.gc_maxlifetime', 120); // セッションの有効時間(秒)
    session_start();

    //セッションにメールを登録
    $_SESSION["email"] = $email;

    //エラーチェック
    $errors = array();
    if (!isset($email)) {
        $errors['email'] = "メールアドレスを入力してください";
    }

    // エラーメッセージを連結してアラート表示
    if (0 < count($errors)) {
        $alertMsg = "";
        foreach ($errors as $key => $value) {
            $alertMsg = $alertMsg . $value . '\n';
        }
        echo "<script type = 'tect/javascript' > alert('" . $alertMSd . "');</script>";
    } else {
        //
        //  入力問題なし
        //
        try {

            // DBへ接続
            $pdo = new PDO($dsn, $db_user, $db_password);

            // 本登録したユーザーの中からメールアドレスが一致したユーザー情報を取得
            $sql = "SELECT id,name FROM `2021_team-b_user` WHERE email = :email  AND status = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $row_count = $stmt->rowCount();


            //件数が１件のみかチェック
            $isErrorMail = $row_count != 1;
            if (!$isErrorMail) {
                
                //アクセスフラグをtrue
                $accessFlg = true;
    
                //　現在時刻に基づいたユニークなIDを生成し256bitにハッシュ化する
                $url_token = hash('sha256', uniqid(rand(), 1));

                // 合致したユーザー情報を取得
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

                //ユーザーid取得
                $id = $user_data["id"];

                $name = $user_data["name"];

                $_SESSION["name"] = $name;

                //セッションでフラグを管理
                $_SESSION["accessFlg"] = $accessFlg;
                
                //6桁のピンコード
                $pinCode = str_pad(mt_rand(0, 999999), 6, 0, STR_PAD_LEFT);

                //pinCodeの値と作成時間を更新
                $user = new User(); // Userクラスのインスタンス生成
                $data = array("pinCode" => $pinCode, "pincode_create_time" => "now()");
   
                $data = $user->updateData($id, $data);

                //メールの送信
                $dn = $_SERVER["HTTP_HOST"];
                //送信先
                $to = $email;
                //タイトル
                $subject = "パスワードの変更について";
                //本文
                $body = "<html><body>";
                $body .= $name . "様";
                $body .= "以下の数字がパスワード変更コードになります";
                $body .= "<p>※パスワード変更コードの有効期限は1時間です。</p>";
                $body .= $pinCode;

                $body .= "<p>====================================================</p>";
                $body .= "<p>※このメールに心当たりのない場合ははお手数をおかけしますがメールの削除をお願いいたします。</p>";
                $body .= "<p>====================================================</p>";

                $body .= "</body></html>";

                //メールのプロパティ
                $headers = implode("\r\n", [
                    "From: test@test.com",
                    "MIME-Version: 1.0",
                    "Content-Type: text/html; charset=UTF-8"
                ]);

                //メールを送信する
                mail($to, $subject, $body, $headers);
                header('Location: pincode_edit.php?id=' . $id);
                
                exit();
            }else
            {
                echo("登録されたメールアドレスが見つかりませんでした。<br/>");
                echo("もう一度ご入力ください。");
            }
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
        //接続を閉じる
        $pdo = null;
    }
} else if (isset($_POST['pw-edit-back'])) {
    header('Location: login.php');
}
//共通ヘッダの読み込み    
include(dirname(__FILE__) . '/header.php');
?>

<body id="pw-edit">
    <div class=frame>
        <form class="form" action="pw-edit.php" method="POST">
            <div class="header">
                <p><img src="img/pw-logo.png"></p>
            </div>            <div class="box">
                <img class="bg" src="img/etc/box.png">
                <p class="p">送信ボタンを押すとご登録されているメールアドレス宛に<span>変更コード</span>を送信します。</br>ご確認をお願い致します。</p>
            </div>            <!-- 入力欄 -->
            <div class="form-group">
                <!-- メール -->
                <label class="input_box"><?php echo $input_box ?></label><br>
                <input class="mail" type="email" name="email" size="30" maxlength="80" autocomplete="email">
            </div>

            <div class="sita">
                <!-- 送信ボタン -->
                <div class="btn-pw-center">
                    <input class="btn-pw-edit" type="submit" name="pw-edit" value="送信">
                    <!-- 戻るボタン -->
                    <input class="btn-pw-edit-back" type="submit" name="pw-edit-back" value="戻る">
                </div>
            </div>
        </form>
    </div>
</body>
</html>