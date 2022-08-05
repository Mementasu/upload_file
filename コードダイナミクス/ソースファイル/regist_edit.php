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

// 確認画面からのリダイレクト(入力エラーが発生した場合)
$error = 0;
if (isset($_GET["error"])) {
    $error = $_GET["error"];

    // 初期値設定
    $user_name = $_POST["user_name"];
    $email = $_POST["email"];
    $email_verify =  $_POST["email_verify"];
    $name = $_POST["name"];
    $street_number = $_POST["street_number"];
    $street_address1 = $_POST["street_address1"];
    $street_address2 = $_POST["street_address2"];
    $street_address3 = $_POST["street_address3"];
    $phone_number = $_POST["phone_number"];
} else {
    //
    // 初回表示
    //

    require_once("api/config.php");
    try {
        // DBへ接続
        $pdo = new PDO($dsn, $db_user, $db_password);

        // ユーザー情報取得
        $sql = "SELECT user_name, email, name, street_number, street_address1, street_address2, street_address3, phone_number FROM `2021_team-b_user` WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":id", $userId, PDO::PARAM_INT);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        var_dump($e->getMessage());
    }

    $pdo = null;

    // 初期値設定
    $user_name = $userData["user_name"];
    $email = $userData["email"];
    $email_verify = "";
    $name = $userData["name"];
    $street_number = $userData["street_number"];
    $street_address1 = $userData["street_address1"];
    $street_address2 = $userData["street_address2"];
    $street_address3 = $userData["street_address3"];
    $phone_number = $userData["phone_number"];
}

// 共通ヘッダの読み込み
include(dirname(__FILE__) . "/header.php");
?>

<body id="regist_edit">
    <div class="frame">
        <p class="img-regist_edit">
            <img src="img/regist_edit.png">
        </p>
        <form class="form" action="preview.php" method="POST">
            <p class="text">以下の内容を入力後登録ボタンを押してください</p>
            <p class="hissu">※全て必須項目です</p>

            <?php
            if (0 != ($error & 1 << 0)) echo '<p class="error">※ユーザーネームを入力して下さい</p>';
            ?>

            <p>■ユーザーネーム</br>
                <input type="text" name="user_name" placeholder="盛ジョビマン" value="<?php echo $user_name ?>">
            </p>

            <?php
            if (0 != ($error & 1 << 1)) echo '<p class="error">※正しいメールアドレスを入力して下さい</p>';
            if (0 != ($error & 1 << 2)) echo '<p class="error">※二つのメールアドレスが違います</p>';
            ?>


            <p>■メールアドレス</br>
                <input type="email" name="email" placeholder="test@example.jp" value="<?php echo $email ?>" placeholder="" size="30" maxlength="50"><br>
                もう一度メールアドレスを入力してください</br>
                <input type="email" name="email_verify" value="<?php echo $email_verify ?>" size="30" maxlength="50" oncopy="return false" onpaste="return false" oncontextmenu="return false">
                <input type="hidden" name="default_email" value="<?php echo $email ?>"><br>
            </p>
            <br>

            <p class="text2">プレゼントの郵送先を入力してください</p>

            <?php
            if (0 != ($error & 1 << 3)) echo '<p class="error">※宛名を入力して下さい</p>';
            ?>

            <p>■宛名</br>
                <input type="text" name="name" placeholder="盛ジョビ男" value="<?php echo $name ?>">
            </p>

            <?php
            if (0 != ($error & 1 << 4)) echo '<p class="error">※正しい郵便番号を入力してください</p>';
            ?>

            <p>■郵便番号（ハイフンなし）</br>
                <input type="text" name="street_number" placeholder="0000000" pattern="[0-9]{7,7}" value="<?php echo $street_number ?>">
            </p>

            <?php
            if (0 != ($error & 1 << 5)) echo '<p class="error">※都道府県を入力してください</p>';
            ?>

            <p class="street1">■住所1</br>
                <input type="text" name="street_address1" placeholder="都道府県" value="<?php echo $street_address1 ?>">
            </p>

            <?php
            if (0 != ($error & 1 << 6)) echo '<p class="error">※市区町村を入力して下さい</p>';
            ?>

            <p class="street2">■住所2</br>
                <input class="street" type="text" name="street_address2" placeholder="市、区、町、村、丁目、番地等" value="<?php echo $street_address2 ?>">
            </p>

            <p class="street3">■住所3</br>
                <input class="street" type="text" name="street_address3" placeholder="マンション、アパート名、部屋番号等" value="<?php echo $street_address3 ?>">
            </p>

            <?php
            if (0 != ($error & 1 << 7)) echo '<p class="error">※電話番号を入力して下さい</p>';
            ?>

            <p>■電話番号（ハイフンなし）</br>
                <input type="number" name="phone_number" placeholder="1234567890" value="<?php echo $phone_number ?>">
            </p>

            <div class="btn-regist_edit-center">
                <input class="btn-regist_edit" type="submit" name="regist_edit" value="登録する">
            </div>
        </form>

        <div class="regist_edit_footer">
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