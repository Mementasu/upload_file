<?php
// 言語、内部エンコーディングを指定
mb_language("japanese");
mb_internal_encoding("UTF-8");


// 共通ヘッダの読み込み
include(dirname(__FILE__) . "/header.php");
// DB情報取得
require_once("api/config.php");
//セッションからIdを持ってくる
session_start();
$id = $_SESSION["user_id"];

// デバッグ用にIDを固定する
// $id = 1;

//住所3(マンション・アパート名)があるかフラグ
$streetFlg = false;

//　ログインされていない場合は強制的にログインpageにリダイレクト
if (!isset($id)) {
    header("Location: login.php");
    exit();
}

//このページに来たらユーザー情報をDBから持ってくる
try {
    // DBへ接続
    $pdo = new PDO($dsn, $db_user, $db_password);

    //データべースからユーザー情報を持ってくる
    $sql = "SELECT name,email,street_number,street_address1,street_address2,street_address3,phone_number,gift_status FROM `2021_team-b_user` WHERE id=:id AND status=1";
    $stmt = $pdo->prepare($sql);
    // $stmt->bindValue(":id", $_SESSION["user_id"], PDO::PARAM_INT);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $row_count = $stmt->rowCount();
    if ($row_count == 1) {
        $myInfo = $stmt->fetch();
        $name = $myInfo["name"];
        $email = $myInfo["email"];
        $street_number = $myInfo["street_number"];
        $street_address1 = $myInfo["street_address1"];
        $street_address2 = $myInfo["street_address2"];
        $street_address3 = $myInfo["street_address3"];
        $phone_number = $myInfo["phone_number"];
        $gift_status = $myInfo["gift_status"];

        $_SESSION["name"] = $name;
        $_SESSION["email"] = $email;
        $_SESSION["id"] = $id;

        //住所３があるならフラグをトゥルーにする
        if ($street_address3 != null) {
            $streetFlg = true;
        }
    } else {
        echo "エラー";
        var_dump($myInfo);
    }
} catch (PDOException $e) {
    var_dump($e->getMessage());
}
//既に応募していたらエラーを出して前の画面に戻す
if ($gift_status != 0 && !isset($_POST['apply-back'])) {
    $errorAlert = "既に応募が完了しています。当選結果発表までお待ちください。 ";
    $alert = "<script type='text/javascript'> alert('" . $errorAlert . "');</script>";
    echo ($alert);
}
//応募ボタン押下かつ選択したギフトナンバーが0（初期値）じゃないなら
else if (isset($_POST['apply-next']) && $_POST['gift'] != "0") {

    //セッションから名前とメアドを持ってくる
    $email =  $_SESSION["email"];
    $name =  $_SESSION["name"];

    //選択したギフトの番号を取得
    $gift = $_POST['gift'];


    //gift_statusが０なのかチェック
    //  →すでに応募していないかを確かめる
    // $pdo = new PDO($dsn, $db_user, $db_password);

    // //ギフト選択番号とギフトステータスを更新
    // $sql2 = "SELECT gift_status FROM `2021_team-b_user` WHERE id=:id";
    // $stmt2 = $pdo->prepare($sql2);
    // $stmt2->bindValue(':id', $id, PDO::PARAM_INT);
    // $stmt2->execute();
    // $row_count = $stmt2->rowCount();

    // if ($row_count == 1) {
    //     // 合致したユーザー情報を取得
    //     $user_data = $stmt2->fetch(PDO::FETCH_ASSOC);
    //     $gift_status = $user_data["gift_status"];
    // }

    $pdo = null;

    //応募済みじゃなければ
    if ($gift_status == 0) {
        //データベースに応募完了の登録をする
        $pdo = new PDO($dsn, $db_user, $db_password);

        //ギフト選択番号とギフトステータスを更新
        $sql3 = "UPDATE `2021_team-b_user` SET gift_number=:gift,gift_status=1 WHERE id=:id";
        $stmt3 = $pdo->prepare($sql3);
        $stmt3->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt3->bindValue(':gift', $gift, PDO::PARAM_STR);
        $stmt3->execute();

        //メール送信のために選択したギフトを数値から商品名へ変える
        switch ($gift) {
            case 1:
                $gift = "PlayStation5";
                break;
            case 2:
                $gift = "P5S";
                break;
            case 3:
                $gift = "ピピンアットマーク";
                break;
            case 4:
                $gift = "ぴゅう太Mk2";
                break;
        }
   
        //応募完了メールの送信
        //メールの送信
        $dn = $_SERVER["HTTP_HOST"];
        //送信先
        $to = $email;
        //タイトル
        $subject = "応募完了のお知らせ";
        //本文
        $body = "<html><body>";
        $body .= "<p>PC DEP0 αプレゼントキャンペーンをご利用いただきありがとうございます。</p>";
        $body .= "<p>プレゼントの応募が完了しました。</p>";
        $body .= "<p>選択したプレゼントは" . $gift . "です。</p>";
        $body .= "<p>下記の流れに沿って抽選・当選者様への配送を行います。ご確認ください。</p>";
        $body .= "<p>1.キャンペーン終了後、応募者様の中から抽選いたします。</p>";
        $body .= "<p>2.当選者宛に「プレゼント当選のお知らせ」のメールを送信します。</p>";
        $body .= "<p>3.上記の工程をキャンペーン終了後に行い、発送の準備ができ次第順次プレゼントを発送していきます。</p>";
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

        //画面遷移
        header("Location: apply_confirm.php");
        
    } else {
        //応募済みなら前のページに戻す
        header("Location: level_select.php");
    }
    //修正ボタン押下
} else if (isset($_POST['apply-back'])) {
    header("Location: regist_edit.php");
} else {
    $errorAlert = "応募する景品を選択してください。";
    $alert = "<script type='text/javascript'> alert('" . $errorAlert . "');</script>";
    echo ($alert);
}
?>

<body id="apply">
    <div class="frame">
        <form class="form" action="apply.php" method=POST>
            <div class="applyTop"><img src="img/applyTop.png"></div>
            <div class="box">

                <div class="bg">
                    <img src="img/apply_bgBox.png">
                </div>


                <p class="text">以下の内容をご確認の上応募ボタンを押下してください。<br />
                    登録内容を修正する場合は修正ボタンを押下してください。</p>

                <div class="myInfo">
                    <p class="atena">■宛名</p>
                    <p class="name"><?php echo $name; ?></p>
                    <hr class="style1">

                    <p class="num1">■郵便番号(ハイフンなし) </p>
                    <p class="street_number"> <?php echo $street_number; ?></p>
                    <hr class="style2">

                    <p class="add1">■住所１</p>
                    <p class="street_address1"> <?php echo $street_address1; ?></p>
                    <hr class="style3">


                    <p class="add2">■住所２ </p>
                    <p class="street_address2"><?php echo $street_address2; ?></p>
                    <hr class="style4">

                    <!-- 住所３があるときだけ表示するようにする -->
                    <?php if ($streetFlg) { ?>
                        <p class="add3">■住所３</p>
                        <p class="street_address3"> <?php echo $street_address3; ?></p>
                        <hr class="style5">
                    <?php } ?>


                    <p class="num2">■電話番号(ハイフンなし)</p>
                    <p class="phone_number"><?php echo $phone_number; ?></p>
                    <hr class="style6">


                    <div class="bg2">
                        <img src="img/apply/bg2.png">
                    </div>

                    <div class="applyImg">
                        <img src="img/apply/apply.png">
                    </div>

                    <div class="select">
                        <select class="a" name="gift">
                            <option value="0" selected>景品を選択してください</option>
                            <option value="1">①PS5</option>
                            <option value="2">②P5S</option>
                            <option value="3">③ピピンアットマーク</option>
                            <option value="4">④ぴゅう太Mk2</option>
                        </select>
                    </div>


                </div>

                <div class="buttons">
                    <div class="btn-apply-center">
                        <!-- 修正ボタン -->
                        <input class="btn-apply-back" type="submit" name="apply-back" value="修正">

                        <!-- 応募ボタン -->
                        <input class="btn-apply" type="submit" name="apply-next" value="応募">
                    </div>

                </div>

            </div>

        </form>
    </div>
</body>