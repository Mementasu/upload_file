<?php
// DB情報読みこみ(ほかのPHPファイル読み込み)
require_once("config.php");

try{
    // DBへ接続
    $pdo = new PDO($dsn, $db_user, $db_password);

    // テーブルのデータ取得

    // ランキング上位
    $sql = 'SELECT name, score, FIND_IN_SET(
        score, (
          SELECT GROUP_CONCAT(
            score ORDER BY score DESC
          )
          FROM `2021_4203110_ranking`
        )
      ) AS rank FROM `2021_4203110_ranking` ORDER BY rank ASC LIMIT 8;';
    // $stmt = $pdo->prepare($sql);
    // $stmt->bindValue(":num", $num, PDO::PARAM_INT);
    // $stmt->execute();
    $data = $pdo->query($sql);
    // echo $data;
    
    if (!empty($data)) {
        foreach ($data as $value) {
            // 1レコードずつ配列へ格納
            $result[] = array(
                "rank" => (int)$value["rank"],
                "id" => (int)$value["id"],
                "name" => $value["name"],
                "score" => (int)$value["score"]
            );
            // echo $result[0];
        }
    }

    // // 自分の順位
    // $sql2 =  'SET @rownum=0;' + 'SELECT @rownum:=@rownum+1 as num, score FROM `2021_4203110_ranking` WHERE id=:id ORDER BY score DESC;';
    // //$stmt2->bindValue(":id", $_SESSION["user_id"], PDO::PARAM_INT);
    // // $stmt2->execute();

    // $data2 = $pdo->query($sql2);
    

    // if (!empty($data2)){
    //     $result2[] = array(
    //         "id" => (int)$value["id"],
    //         "name" => $value["name"],
    //         "score" => (int)$value["score"]
    //     );
    //     // echo $result2[$data];
    //}

} catch (PDOException $e) {
    // DBエラー
    http_response_code(500);
    die($e->getMessage());
}

// 配列をJsonのフォーマットで返す
echo json_encode($result);

// 接続を閉じる
$pdo = null;
?>