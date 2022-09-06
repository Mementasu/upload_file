<?php

// 言語、内部エンコーディングを指定
mb_language("japanese");
mb_internal_encoding("UTF-8");

// TODO ログインされていない場合は強制的にログインページにリダイレクト
// if (!isset($_SESSION["user_id"])) {
//     header("location: login.php");
//     exit();
// }

// TODO ログインしているユーザーID取得
session_start();
$userId = $_SESSION["user_id"];

// DB情報取得
require_once("api/config.php");

// 共通ヘッダの読み込み
include(dirname(__FILE__) . "/header.php");


// 登録ボタンが押された後の処理
if (isset($_POST["regist_edit"])) {

    // $errors = array();
    $error = 0; // 000000

    
    // ユーザーネームの文字列チェック
    $user_name = filter_var($_POST["user_name"], FILTER_SANITIZE_STRING);
    if ($user_name == false) {
        $error += 1 << 0;
    }


    // メールアドレスが変更されている場合は、入力チェックを行う
    if (strcmp($_POST["email"], $_POST["default_email"]) != 0) {

        // メールアドレスのフォーマットチェック
        $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
        if ($email == false) {
            $error += 1 << 1;
        }

        // メールアドレスのダブルチェック
        $email_verify = filter_var($_POST["email_verify"], FILTER_VALIDATE_EMAIL);
        if (strcmp($email, $email_verify) != 0) {
            $error += 1 << 2;
        }
        
    } else {
        // メールアドレスのフォーマットチェック
        $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
        if ($email == false) {
            $error += 1 << 1;
        }

        // メールアドレスのダブルチェック
        $email_verify = filter_var($_POST["email_verify"], FILTER_VALIDATE_EMAIL);
        if (strcmp($email, $email_verify) != 0) {
            $error += 1 << 2;
        }
    }

    // 名前の文字列チェック
    $name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
    if ($name == false) {
        $error += 1 << 3;
    }

    // 郵便番号の文字列チェック
    $street_number = filter_var($_POST["street_number"], FILTER_SANITIZE_STRING);
    if ($street_number == false) {
        $error += 1 << 4;
    }

    // 都道府県の入力チェック
    $street_address1 = filter_var($_POST["street_address1"], FILTER_SANITIZE_STRING);
    if ($street_address1 == false) {
        $error += 1 << 5;
    }

    // 住所の入力チェック
    $street_address2 = filter_var($_POST["street_address2"], FILTER_SANITIZE_STRING);
    if ($street_address2 == false) {
        $error += 1 << 6;
        // $errors["street_address2"] = "住所２を入力してください";
    }

    $street_address3 = filter_var($_POST["street_address3"], FILTER_SANITIZE_STRING);

    // 電話番号の入力チェック
    $phone_number = filter_var($_POST["phone_number"], FILTER_SANITIZE_STRING);
    if ($phone_number == false) {
        $error += 1 << 7;
        // $errors["phone_number"] = "電話番号を入力してください";
    }



    if ($error != 0) {
        header("Location: regist_edit.php?error=" . $error, true, 307);
        exit;
    }

    if ($error == 0) {
    ?>
<body id="preview">
    <div class="frame">
        <div class="img-preview">
            <img src="img/regist_preview.png">
        </div>

        <form class="form" action="preview.php" method="POST">
            <div class="preview">

                <p class="text">以下の内容を確認の上、登録ボタンを押下してください。修正する場合は、修正ボタンを押下してください。</p>

                <p class="content">
                    ■ユーザーネーム</br>
                    <?php echo $user_name ?></br>
                    <input type="hidden" name="user_name" value=<?php echo $user_name ?>>
                    ■メールアドレス</br>
                    <?php echo $email ?></br>
                    <input type="hidden" name="email" value=<?php echo $email ?>>
                    ■宛名</br>
                    <?php echo $name ?></br>
                    <input type="hidden" name="name" value=<?php echo $name ?>>
                    ■郵便番号（ハイフンなし）</br>
                    <?php echo $street_number ?></br>
                    <input type="hidden" name="street_number" value=<?php echo $street_number ?>>
                    ■住所1</br>
                    <?php echo $street_address1 ?></br>
                    <input type="hidden" name="street_address1" value=<?php echo $street_address1 ?>>
                    ■住所2</br>
                    <?php echo $street_address2 ?></br>
                    <input type="hidden" name="street_address2" value=<?php echo $street_address2 ?>>
                    ■住所3</br>
                    <?php echo $street_address3 ?></br>
                    <input type="hidden" name="street_address3" value=<?php echo $street_address3 ?>>
                    ■電話番号（ハイフンなし）</br>
                    <?php echo $phone_number ?>
                    <input type="hidden" name="phone_number" value=<?php echo $phone_number ?>>
                </p>
            </div>

            <p class="btn-preview">
                <input class="btn-preview-edit" type="submit" name="preview-edit" value="修正する" formaction="regist_edit.php" formmethod="POST" />
                <!-- <input class="btn-preview-decision" type="submit" name="preview-decision" value="登録する" formaction="lelvel_select.php" formmethod="POST"/> -->
                <input class="btn-preview-decision" type="submit" name="preview-decision" value="登録する">
            </p>

        </form>

        <div class="preview_footer">
            <p class="title">【個人情報について】</p>
            <p class="text">個人情報は、
                プレゼント配送及び、<br>
                個人の特定ができない統計情報として
                使用させていただきます。</p>

            <a href="campaign_top.php">トップに戻る</a>
        </div>
    </div>
</body>

</html>
<?php
    }

}

else if (isset($_POST["preview-decision"])) {

            try {
                //　DBへ接続
                $pdo = new PDO($dsn, $db_user, $db_password);
                // //　私用されたメールアドレスがすでに登録されていないかチェック(仮登録はのぞく)
                // $sql = "SELECT id FROM `2021_team-b_user` WHERE email = :email AND status != 0";
                // $stm1 = $pdo->prepare($sql);
                // $stm1->bindValue(':email', $email, PDO::PARAM_STR);
                // $stm1->execute();
                // $row_count = $stm1->rowCount();
    
                //　現在時刻に基づいたユニークなIDを生成し256bitにハッシュ化する
                // $url_token = hash('sha256', uniqid(rand(), 1));
    
                // 仮登録
                $sql2 = "UPDATE `2021_team-b_user` SET user_name=:user_name, email=:email, name=:name, street_number=:street_number, street_address1=:street_address1, street_address2=:street_address2, street_address3=:street_address3, phone_number=:phone_number WHERE id=:id";
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->bindValue(':id', $userId, PDO::PARAM_INT);
                $stmt2->bindValue(':user_name', $_POST["user_name"], PDO::PARAM_STR);
                $stmt2->bindValue(':email', $_POST["email"], PDO::PARAM_STR);
                $stmt2->bindValue(':name', $_POST["name"], PDO::PARAM_STR);
                $stmt2->bindValue(':street_number', $_POST["street_number"], PDO::PARAM_STR);
                $stmt2->bindValue(':street_address1', $_POST["street_address1"], PDO::PARAM_STR);
                $stmt2->bindValue(':street_address2', $_POST["street_address2"], PDO::PARAM_STR);
                $stmt2->bindValue(':street_address3', $_POST["street_address3"], PDO::PARAM_STR);
                $stmt2->bindValue(':phone_number', $_POST["phone_number"], PDO::PARAM_STR);
                $stmt2->execute();

                ?>

<body id="preview">
    <div class="frame">
        <div class="img-preview">
            <img src="img/regist_preview.png">
        </div>

        <form class="form" action="level_select.php" method="POST">
            <div class="preview-decision">
                <p class="text-decision">登録が完了しました。</p>
            </div>

            <p class="btn-preview">
                <input class="btn-preview-edit" type="submit" name="trans_level_select" value="難易度選択画面へ">
            </p>
        </form>
    </div>
            
</body>

</html>



<?php
            // exit; 
            } 
            catch (PDOException $e) {
                var_dump($e->getMessage());
            }
    
            // 接続を閉じる
            $pdo = null;

}


?>

