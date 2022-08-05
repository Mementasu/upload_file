<?php


// 文字コード設定
// header('Content-Type: application/json; charset=UTF-8');
//言語、内部エンコーディングを指定
mb_language("japanese");
mb_internal_encoding("UTF-8");
//cssの読み込み
include(dirname(__FILE__) . "/header.php");

// DB情報取得
require_once("../api/config.php");

//タイムアウト時間を設定
set_time_limit(300);

$array = array();

//抽選人数
$selectPeople = 0;

$x = 0;

$page = 10;

$a;
$b = array();

$pdo = new PDO($dsn, $db_user, $db_password);
$debugSql = "UPDATE `2021_team-b_user` SET select_status = 0";
$debugStmt = $pdo->prepare($debugSql);
$debugStmt->execute();
$pdo = null;
// num(n$n_number)が存在するか
//抽選ボタンを押したら
if (isset($_POST["chusen"])) {
    // if (isset($_GET["num"])) {
    $num = filter_var($_POST['chusen_num'], FILTER_SANITIZE_NUMBER_INT);

    //POSTしたギフトナンバーによって当選者数を変更する
    switch ($_POST["chusen_num"]) {
        case 1:
            // 1:PS5 10人
            $selectPeople = 10;
            break;
        case 2:
            // 2:P5S 30人
            $selectPeople = 30;
            break;
        case 3:
            // 3:ピピン 2人
            $selectPeople = 2;
            break;
        case 4:
            // 4:ぴゅう太　２人
            $selectPeople = 2;
            break;
    }

    // numをエスケープ(xss対策
    $param = htmlspecialchars($_POST["chusen_num"]);


    // メイン処理
    try {

        // DBへ接続
        $pdo = new PDO($dsn, $db_user, $db_password);

        //景品応募済みで抽選していない景品ユーザーをランダムに人数分取得
        $sql = "SELECT id,name,email FROM `2021_team-b_user` WHERE gift_number=:gift_number AND gift_status=1 AND select_status=0 order by Rand() limit :select_people";
        $stmt = $pdo->prepare($sql);

        // $stmt->bindValue(":id", $_SESSION["user_id"], PDO::PARAM_INT);
        $stmt->bindValue(":gift_number", $_POST["chusen_num"], PDO::PARAM_INT);
        $stmt->bindValue(":select_people", $selectPeople, PDO::PARAM_INT);
        $stmt->execute();

        //fetchAllで↑のSQLで取得した当選者のデータを$users_data配列にぶち込む
        $users_data = $stmt->fetchAll();


        // exit;

        $page_sql = "SELECT id,name,email
        FROM `2021_team-b_user`
        ORDER BY id
        LIMIT 10 OFFSET 10";
        $stmt5 = $pdo->prepare($page_sql);
        $stmt5->execute();

        //当選者のID名前を全員分表示する
        echo ("当選者" . "<br/>");
        for ($i = 0; $i < $selectPeople; $i++) {
            $x += $i + 1;
            echo ($x . "人目 <br/>");
            echo ("ID：" . $users_data[$i][0] . "　名前：" . $users_data[$i][1] . "<br/>");
            echo ("---------------------------------------------------------------------------- <br/>");
            $x = 0;
        }

        if ($users_data == null) {

            $errorAlert = "この景品は既に抽選済みです";
            $alert = "<script type='text/javascript'> alert('" . $errorAlert . "');</script>";
            echo ($alert);
        } else {

            //当選者のステータス更新
            for ($i = 0; $i < count($users_data); $i++) {
                $sql3 = "UPDATE `2021_team-b_user` SET select_status = 1 WHERE id=:id";
                $stmt3 = $pdo->prepare($sql3);
                $stmt3->bindValue(':id', $users_data[$i]["id"], PDO::PARAM_STR);
                $stmt3->execute();
            }

            //ステータス更新の処理が終わったら当選者以外のselect_statusを2(非当選者)にする
            $sql2 = "UPDATE `2021_team-b_user` SET select_status=2 WHERE gift_number=:gift_number AND gift_status=1 AND select_status=0";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->bindValue(":gift_number", $_POST["chusen_num"], PDO::PARAM_INT);
            $stmt2->execute();

            //　-> $users_data[n][0],[n][1],[n][2],[n][3]
            //                    ID   名前   メアド プレゼントナンバー

            //数値じゃなくて ["id"] ["name"] ["email"] でも取得可能

            //当選者の人数分for文してメールを送る
            for ($i = 0; $i < count($users_data); $i++) {

                //メールの送信
                $dn = $_SERVER["HTTP_HOST"];
                //送信先
                $to = $users_data[$i]["email"];
                //タイトル
                $subject = "【PC DEP0 αより】当選のご案内";
                //本文
                $body = "<html><body>";
                $body .= "PC DEP 0αプレゼントキャンペーンをご利用いただきまして、誠にありがとうございます。" . $users_data[$i]["name"] . "様にお申込みいただいた景品をご用意いたしました。<br>";
                $body .= "景品は1週間以内に用意し、その後3営業日以内に発送させていただきます。<br>";
                $body .= "なお、個人情報は改正個人情報保護法に乗っ取り、削除いたします。<br>";

                $body .= "<p>====================================================</p>";
                $body .= "<p>※このメールに心当たりのない場合ははお手数をおかけしますがメールの削除をお願いいたします。</p>";
                $body .= "<p>====================================================</p>";


                $body .= "<br/>";
                $body .= "</body></html>";

                //メールのプロパティ
                $headers = implode("\r\n", [
                    "From: test@test.com",
                    "MIME-Version: 1.0",
                    "Content-Type: text/html; charset=UTF-8"
                ]);

                //メールを送信する
                mail($to, $subject, $body, $headers);
                var_dump($to);
                // exit;
            }

        }
    } catch (PDOException $e) {
        var_dump($e->getMessage());
    }
}

//接続を切る
$pdo = null;

?>

<body id="chusen">
    <form class="form" action="testApi2.php" method="POST">

        <h3>■抽選するgift_numberと対応している景品名</h1>
            <table class="chusen_table" align="center">
                <tr>
                    <th>gift_number</th>
                    <th>景品名</th>
                </tr>

                <tr>
                    <td class="gift_number">1</td>
                    <td class="pr_name">PlayStation5(10名)</td>
                </tr>

                <tr>
                    <td class="gift_number">2</td>
                    <td class="pr_name">P5S(30名)</td>
                </tr>

                <tr>
                    <td class="gift_number">3</td>
                    <td class="pr_name">ぴゅう太Mk2(2名)</td>
                </tr>

                <tr>
                    <td class="gift_number">4</td>
                    <td class="pr_name">ピピンアットマーク(2名)</td>
                </tr>
            </table>
            <!-- <p>gift_number: 1=PlayStation5(10名)</p>
        <p>gift_number: 2=P5S(30名)</p>
        <p>gift_number: 3=ぴゅう太Mk2(2名)</p>
        <p>gift_number: 4=ピピンアットマーク(2名)</p>
         -->
            <h3>■抽選する景品を選択</h3>
            <div class="labels">
                <label>
                    <input type="radio" name="chusen_num" value="1" required>PlayStation5 </br>
                </label>

                <label>
                    <input type="radio" name="chusen_num" value="2">P5S</br>
                </label>

                <label>
                    <input type="radio" name="chusen_num" value="3">ぴゅう太Mk2</br>
                </label>

                <label>
                    <input type="radio" name="chusen_num" value="4">ピピンアットマーク</br>
                </label>
            </div>
            </br><input class="chusen-btn" type="submit" name="chusen" value="抽選する">
    </form>
</body>