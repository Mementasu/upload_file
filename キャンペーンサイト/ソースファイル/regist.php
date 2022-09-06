<?php
//言語、内部エンコーディングを指定
mb_language("japanese");
mb_internal_encoding("UTF-8");
// 登録ボタンが押されたかチェック
//登録ボタンが押された後の処理
if (isset($_POST['regist'])) {

    //初回アクセス時の値設定
    // フォームデータの取得とフィルタリング
    $user_name = filter_var($_POST['user_name'], FILTER_SANITIZE_STRING);
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $email_verify  = filter_var($_POST['email_verify'], FILTER_VALIDATE_EMAIL);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    $password_verify  = filter_var($_POST['password_verify'], FILTER_SANITIZE_STRING);
    $street_number = filter_var($_POST['street_number'], FILTER_SANITIZE_STRING);
    $street_address1 = filter_var($_POST['street_address1'], FILTER_SANITIZE_STRING);
    $street_address2 = filter_var($_POST['street_address2'], FILTER_SANITIZE_STRING);
    $street_address3 = filter_var($_POST['street_address3'], FILTER_SANITIZE_STRING);
    $phone_number = filter_var($_POST['phone_number'], FILTER_SANITIZE_STRING);

    // エラーチェック
    $errors = array();
    if ($user_name == false) {
        $errors['user_name'] = "ユーザーネームを入力してください";
    }
    if ($email == false) {
        $errors['email'] = "正しいメールアドレスを入力してください";
    }
    if (strcmp($email, $email_verify) != 0) {
        $errors['email_not_equal'] = "入力された二つのメールアドレスが違います";
    }
    if (strcmp($password, $password_verify) != 0) {
        $errors['password_not_equal'] = "入力された二つのパスワードが違います";
    }
    if ($name == false) {
        $errors['name'] = "名前を入力してください";
    }
    if ($street_number == false) {
        $errors['street_number'] = "郵便番号を入力してください";
    }
    if ($street_address1 == false) {
        $errors['street_address1'] = "都道府県を入力してください";
    }
    if ($street_address2 == false) {
        $errors['street_address2'] = "住所を入力してください";
    }
    if ($phone_number == false) {
        $errors['phone_number'] = "電話番号を入力してください";
    }

    // エラーチェック
    if (0 < count($errors)) {
        // エラーメッセージを連結してアラート表示
        $alertMsg = "";
        foreach ($errors as $key => $value) {
            $alertMsg = $alertMsg . $value . '\n';
        }
        echo "<script type='text/javascript'>alert('" . $alertMsg . "');</script>";
    } else {
        //　パスワードを複合不可な256bitのハッシュに変換
        $password_sha256 = hash('sha256', $password);

        //　DB情報読み込み(他のPHPファイル読み込み)
        require_once('config.php');

        try {
            //　DBへ接続
            $pdo = new PDO($dsn, $db_user, $db_password);
            //　使用されたメールアドレスがすでに登録されていないかチェック(仮登録はのぞく)
            $sql = "SELECT id FROM `2021_team-b_user` WHERE email = :email AND status != 0";
            $stm1 = $pdo->prepare($sql);
            $stm1->bindValue(':email', $email, PDO::PARAM_STR);
            $stm1->execute();
            $row_count = $stm1->rowCount();

            if ($row_count == 0) {
                //　現在時刻に基づいたユニークなIDを生成し256bitにハッシュ化する
                $url_token = hash('sha256', uniqid(rand(), 1));

                // 仮登録
                $sql2 = "INSERT INTO `2021_team-b_user` (user_name,email, password, name, street_number, street_address1, street_address2, street_address3,phone_number, url_token, token_create_time, status) VALUES (:user_name, :email, :password, :name, :street_number, :street_address1, :street_address2,:street_address3,:phone_number, :url_token, now(), 0)";
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->bindValue(':user_name', $user_name, PDO::PARAM_STR);
                $stmt2->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt2->bindValue(':password', $password_sha256, PDO::PARAM_STR);
                $stmt2->bindValue(':name', $name, PDO::PARAM_STR);
                $stmt2->bindValue(':street_number', $street_number, PDO::PARAM_STR);
                $stmt2->bindValue(':street_address1', $street_address1, PDO::PARAM_STR);
                $stmt2->bindValue(':street_address2', $street_address2, PDO::PARAM_STR);
                $stmt2->bindValue(':street_address3', $street_address3, PDO::PARAM_STR);
                $stmt2->bindValue(':phone_number', $phone_number, PDO::PARAM_STR);
                $stmt2->bindValue(':url_token', $url_token, PDO::PARAM_STR);
                $stmt2->execute();




                // メールの送信
                //$url = "http://localhost/企業連携/login.php?url_token=" . $url_token;
                $url = "https://web-network.sakura.ne.jp/games2021/collabo/team-b/login.php?url_token=" . $url_token;
                // $qr_code = "https://api.qrserver.com/v1/create-qr-code/?data=" . $url . "&size=100x100";

                //送信先
                $to = $email;
                //タイトル
                $subject = "仮登録完了のお知らせ";
                //本文
                $body = "<html><body>";
                $body .= $name . "様";
                $body .= "<p>PC DEP0 aプレゼントキャンペーンにご参加いただきありがとうございます。</p>";
                $body .= "<p>以下のURLをクリックしてユーザー登録を完了させて下さい</p>";
                $body .= $url;
                
                $body .= "<p>====================================================</p>";
                $body .= "<p>※このメールに心当たりのない場合ははお手数をおかけしますがメールの削除をお願いいたします。</p>";
                $body .= "<p>====================================================</p>";
                // $body .= "<p><img src='" . $qr_code . "' alt='QRコード' /></p>";
                $body .= "</body></html>";
                //メールのプロパティ
                $headers = implode("\r\n", [
                    "From: test@test.com",
                    "MIME-Version: 1.0",
                    "Content-Type: text/html; charset=UTF-8"
                ]);

                //メールを送信する


                mail($to, $subject, $body, $headers);
                //仮登録完了画面に遷移
                header("Location: regist_confirm.php");

                exit;
            } else {
                var_dump("このメールアドレスは既に登録されています");
            }
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }

        // 接続を閉じる
        $pdo = null;
    }
} else {
    $user_name = "";
    $email = "";
    $email_verify = "";
    $password = "";
    $password_verify = "";
    $name = "";
    $street_number = "";
    $street_address1 = "";
    $street_address2 = "";
    $street_address3 = "";
    $phone_number = "";
}
// 共通ヘッダの読み込み
include(dirname(__FILE__) . '/header.php');
?>

<body id="regist">
    <div class=frame>
        <img class="img" src='img/regist_logo.png'></img>
        <form class="form" action="regist.php" method="POST">


            <p class="text">以下の内容を入力後登録ボタンを押してください</p>
            <p class="hissu">※全て必須項目です</p>

            <p>■ユーザーネーム</br>
                <input type="text" name="user_name" value="<?php echo $user_name ?>" placeholder="盛ジョビマン" autocomplete="user-name">
            </p>

            <p>■メールアドレス</br>
                <input type="email" name="email" value="<?php echo $email ?>" size="30" maxlength="50" placeholder="test@example.jp" autocomplete="email"><br>
                もう一度メールアドレスを入力して下さい<br>
                <input type="email" name="email_verify" value="<?php echo $email_verify ?>" size="30" maxlength="50" oncopy="return false" onpaste="return false" oncontextmenu="return false">
            </p>

            <p>■パスワード</br>
                <input type="password" name="password" pattern=".{8,}" value="<?php echo $password ?>" size="30" maxlength="50" placeholder="最低8文字必要です"></br>
                もう一度パスワードを入力して下さい</br>
                <input type="password" name="password_verify" value="<?php echo $password_verify ?>" size="30" maxlength="50" oncopy="return false" onpaste="return false" oncontextmenu="return false">
            </p></br>
            <p class="text2">プレゼントの郵送先を入力して下さい</p>



            <p>■宛名</br>
                <input type="text" name="name" value="<?php echo $name ?>" placeholder="盛ジョビ男" autocomplete="name">
            </p>

            <p>■郵便番号(ハイフンなし)</br>
                <input type="text" name="street_number" pattern="[0-9]{7,7}" placeholder="0000000" value="<?php echo $street_number ?>" autocomplete="postal-code">
            </p>

            <p>■住所１</br>
                <input type="text" name="street_address1" value="<?php echo $street_address1 ?>" placeholder="都道府県" autocomplete="address-level1">
            </p>

            <p class="street">■住所２</br>
                <input type="text" name="street_address2" value="<?php echo $street_address2 ?>" placeholder="市区町村" autocomplete="address-level2">
            </p>

            <p class="street">■住所３</br>
                <input type="text" name="street_address3" value="<?php echo $street_address3 ?>" placeholder="マンション・アパート名">
            </p>

            <p>■電話番号(ハイフンなし)</br>
                <input type="number" name="phone_number" value="<?php echo $phone_number ?>" placeholder="12345678910" autocomplete="tel">
            </p>

            <div class="btn-regist-center">
                <input class="btn-regist" type="submit" name="regist" value="登録する">
            </div>
        </form>

        <div class="regist__footer">
            <p class="title">【個人情報について】</p>
            <p class="text">個人情報は、プレゼント配送及び、</br>
                個人の特定が出来ない統計情報として使用させて頂きます。</p>

            <a href="login.php">ログインページにもどる</a>
        </div>
    </div>
</body>

</html>