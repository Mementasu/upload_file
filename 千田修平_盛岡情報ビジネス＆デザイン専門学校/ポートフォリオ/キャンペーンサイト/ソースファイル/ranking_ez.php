<?php
    ini_set('session.gc_divisor', 1); // 有効期限が切れたら必ずセッションを削除する
    // ini_set('session.gc_maxlifetime', 10); // セッションの有効時間(秒)

    ini_set('session.gc_maxlifetime', 1000); // セッションの有効時間(秒)
    // セッションを使うことを宣言
    session_start();

    //　ログインされていない場合は強制的にログインpageにリダイレクト
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
    } else {
        // DB情報取得
        require_once("api/config.php");

        try {
            // DBへ接続
            // 自分のスコア・順位

            // ランキング情報取得
            $pdo = new PDO($dsn, $db_user, $db_password);

           

            // 自分スコア・順位
            if($_GET["ranking"] == 'やさしい'){
                $sql = "SELECT score, FIND_IN_SET(
                    score, (
                      SELECT GROUP_CONCAT(
                        score ORDER BY score DESC
                      )
                      FROM `2021_4203110_ranking`
                    )
                  ) AS rank FROM `2021_4203110_ranking` WHERE id=:id";
                
                // ランキング上位
                $sql2 = 'SELECT name, score, FIND_IN_SET(
                    score, (
                      SELECT GROUP_CONCAT(
                        score ORDER BY score DESC
                      )
                      FROM `2021_4203110_ranking`
                    )
                ) AS rank FROM `2021_4203110_ranking` ORDER BY rank ASC LIMIT 8;';
            } else if($_GET["ranking"] == "むずかしい"){
                $sql = "SELECT score, FIND_IN_SET(
                    score, (
                      SELECT GROUP_CONCAT(
                        score ORDER BY score DESC
                      )
                      FROM `2021_4203110_ranking`
                    )
                  ) AS rank FROM `2021_4203110_ranking` WHERE id=:id";

                  // ランキング上位
                    $sql2 = 'SELECT name, score, FIND_IN_SET(
                    score, (
                      SELECT GROUP_CONCAT(
                        score ORDER BY score DESC
                      )
                      FROM `2021_4203110_ranking`
                    )
                  ) AS rank FROM `2021_4203110_ranking` ORDER BY rank ASC LIMIT 8;';
            };
            

            // 自分スコア・順位
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":id", $_SESSION["user_id"], PDO::PARAM_INT);
            
            $stmt->execute();

            $row_count = $stmt->rowCount();

            if ($row_count == 1){
                $my_result = $stmt->fetch();
                $my_rank = $my_result["rank"];
                $my_score = $my_result["score"];
            } else {
                echo "エラー";
                // $errors["urltoken_timeover"] = "このURLはご利用できません。有効期限が過ぎたかURLが間違えている可能性がございます。もう一度登録をやりなおしてください。"
            }

            // ランキング上位
            $data = $pdo->query($sql2);
                
            if (!empty($data)) {
                foreach ($data as $value) {
                    // 1レコードずつ配列へ格納
                    $result[] = array(
                        "rank" => (int)$value["rank"],
                        "name" => $value["name"],
                        "score" => (int)$value["score"]
                    );
                }
            }
            
        } catch(PDOException $e){
            var_dump(($e->getMessage()));
        }

        $pdo = null;
    }
    

    // var_dump("セッションに保存されているユーザーIDは" . $_SESSION["login"] . "です");
    // echo "こんにちは" . $_SESSION["user_name"] . "さん";


//共通ヘッダの読み込み    
include(dirname(__FILE__) . '/header.php');
?>

<body id="ranking_ez">
    <div class="frame">
        <div class="ranking_top">
            <p>ランキング</p>
            <img src="img/ranking.png">
        </div>

        <div class="ranking_top_level">

            <p>やさしい</p>
            <img src="img/easy.png">
        </div>

        <div class="my_score">
            <p>ベストスコア　<?php echo $my_score; ?></p>
            <p>現在の順位　<?php echo $my_rank; ?></p>
        </div>

        <div class="ranking">
            <?php 
            for($j=0; $j<3; $j++) {
                switch($j){
                    case 0: 
                        $e = 'rank';
                        break;
                    case 1: 
                        $e = 'name';
                        break;
                    case 2: 
                        $e = 'score';
                        break;
                };
            ?>
            <div class="top_<?php echo $e ?>">
                <p> 
                <?php 
                    for($i=0; $i<count($result); $i++) {
                        echo $result[$i][$e];
                        ?> </br> <?php
                    };?>
                </p>
            </div>
            <?php }; ?>

            <img src="img/ranking-top8.png">
            <input class="ranking_hard" type="submit" name="ranking-hard" value="むずかしい" onclick="location.href='ranking_ez.php'">
        </div>

        <div class="ranking_ez_footer">
            <a href="level_select.php">難易度選択に戻る</a>
            <a href="campaign_top.php">トップに戻る</a>
        </div>
        
    </div>
</body>