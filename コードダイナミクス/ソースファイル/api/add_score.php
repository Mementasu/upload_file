<?php
// DB情報読み込み
require_once("config.php");

session_start();
$id = $_SESSION["user_id"];
echo $_SESSION["user_id"];

// // GetParameterのnameを取得
// if (isset($_POST["name"])) { // nameがあるなら
//     $name = $_POST["name"];
// } else {
//     http_response_code(400);
//     die("Not Parameter Name");
// }

// // GetParameterのScoreを取得
// if (isset($_POST["score"])) {
//     $score = $_POST["score"];
// } else {
//     http_response_code(400);
//     die("Not Parameter Score");
// }



// try {
//     // DBへ接続
//     $pdo = new PDO($dsn, $db_user, $db_password);
    
//     // データの追加
//     $stmt = $pdo->prepare("INSERT INTO `2021_team-b_ranking`(id, name, score) VALUES (:id, :name, :score)");
//     $stmt->bindValue(":id", $id, PDO::PARAM_INT);
//     $stmt->bindValue(":name", $name, PDO::PARAM_STR);
//     $stmt->bindValue(":score", $score, PDO::PARAM_INT);
//     $stmt->execute();
    

// } catch (PDOException $e) {
//     // DBエラー
//     http_response_code(500);
//     die($e->getMessage());
// }

// echo "success";

// // 接続を閉じる
// $pdo = null;

?>

<body>
    <a href="get_top_score.php">get_top_score</a>
</body>