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
            // ランキング情報取得
            $pdo = new PDO($dsn, $db_user, $db_password);

            $score = "";
            
            if($_GET["ranking"] == "やさしい") $score = "score_easy";
            else if($_GET["ranking"] == "むずかしい") $score = "score_hard";

            // 自分スコア・順位
            $sql = "SELECT " . $score . ", FIND_IN_SET("
                . $score . ", (SELECT GROUP_CONCAT(" . $score . " ORDER BY " 
                . $score . " DESC) FROM `2021_team-b_user`)) AS rank 
                FROM `2021_team-b_user` WHERE id=:id";

            // 自分スコア・順位
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindValue(":id", $_SESSION["user_id"], PDO::PARAM_INT);
            
            $stmt->execute();

            $row_count = $stmt->rowCount();

            if ($row_count == 1){
                $my_result = $stmt->fetch();
                $my_rank = $my_result["rank"];
                $my_score = $my_result[$score];
            } else {
                echo "エラー";
                // $errors["urltoken_timeover"] = "このURLはご利用できません。有効期限が過ぎたかURLが間違えている可能性がございます。もう一度登録をやりなおしてください。"
            }

            // ランキング上位
            $sql2 = "SELECT user_name, " . $score . ", FIND_IN_SET("
                . $score . ", (SELECT GROUP_CONCAT(" . $score . " ORDER BY " 
                . $score . " DESC) FROM `2021_team-b_user`)) AS rank 
                FROM `2021_team-b_user` ORDER BY rank ASC LIMIT 8;";     
            $data = $pdo->query($sql2);
                
            if (!empty($data)) {
                foreach ($data as $value) {
                    // 1レコードずつ配列へ格納
                    $result[] = array(
                        "rank" => (int)$value["rank"],
                        "name" => $value["user_name"],
                        "score" => (int)$value[$score]
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

<body id="ranking">
    <div class="frame">
        <div class="ranking_top">
            <p>ランキング</p>
            <img src="img/level_select_ranking/ranking.png">
        </div>

        

        <div class="ranking_top_level">
            <?php 
                if ($_GET["ranking"] == "やさしい"){ ?>
                    <p class="ranking_ez">やさしい</p>
                    <img src="img/level_select_ranking/ranking-easy-top.png"><?php 
                } else if ($_GET["ranking"] == "むずかしい"){ ?>
                    <p class="ranking_hard">むずかしい</p>
                    <img src="img/level_select_ranking/ranking-hard-top.png"><?php 
                }
            ?>
            
        </div>

        
        

        <div class="my_score">
            <div class="text">
                <p>ベストスコア</p>
                <p>現在の順位</p>
            </div>
            <div class="var">
                <p><?php echo $my_score; ?></p>
                <p><?php echo $my_rank; ?></p>
            </div>
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

            <img src="img/level_select_ranking/ranking-top8.png">


            <!-- <img class="back_level_select" src="img/result/back-level-select.png" onclick="location.href='level_select.php'"> -->
        </div>

        

        <div class="ranking_footer">
            <!-- <a href="level_select.php">難易度選択に戻る</a> -->
            <div class="move">
            <form action="ranking.php" method="GET">
                <?php 
                    if($_GET["ranking"] == "やさしい"){ ?>
                        <input class="move_ranking_hard" type="submit" name="ranking" value="むずかしい" onclick="location.href='ranking.php'">
                    <?php }
                    else if ($_GET["ranking"] == "むずかしい") { ?>
                        <input class="move_ranking_ez" type="submit" name="ranking" value="やさしい" onclick="location.href='ranking.php'">
                    <?php }
                ?>
            </form>
        </div>
            <img class="back_level_select" src="img/result/back-level-select.png" onclick="location.href='level_select.php'">
            <a href="campaign_top.php">トップに戻る</a>
        </div>
        
    </div>
</body>