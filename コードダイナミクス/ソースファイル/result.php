<?php

    ini_set('session.gc_divisor', 1); // 有効期限が切れたら必ずセッションを削除する
    // ini_set('session.gc_maxlifetime', 10); // セッションの有効時間(秒)

    ini_set('session.gc_maxlifetime', 1000); // セッションの有効時間(秒)
    // セッションを使うことを宣言
    session_start();


    // ログインされていない場合は強制的にログインpageにリダイレクト
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
    } else {
        // DB情報取得
        require_once("api/config.php");
        
        $level = $_POST["level"]; // やさしい or むずかしい
        $life = $_POST["life"]; // ライフ
        $score = $_POST["score"]; // ポイント（スコア）
        $total = $life * $score; 
        $clear = ($total <= 0) ? false : true; // クリア判定
        // スコアによって獲得ポイント変える

        $addPoint = ($level == "easy") ? 
            ($total >= 1500 ? 5 : ($total >= 500 && $total < 1500 ? 3 : 1)):  
            ($total >= 5000 ? 7 : ($total >= 2000 && $total < 5000 ? 5 : 1)); // ポイント

        // テスト
        // $getPoint = true;
        // $_SESSION["play_count"] = 1;
        // 本番
        $getPoint = ($_SESSION["play_count"] > 0) ? true: false;

        // echo $_POST["level"];
        // echo $_POST["life"];
        // echo $_POST["score"];

        try {
            // DBへ接続
            $pdo = new PDO($dsn, $db_user, $db_password);

            // LastPlayTimeを更新する
            // PlayCountを-1する
            // if LastWachAdTimeが今日でなければ広告を流せる
            // 広告を見たらPlayCountを+1にし、LastWachAdTimeを更新

            // 現在の応募ポイント・ゲームスコアがハイスコアなら更新
            // 更新
            if($clear){
                // テスト
                // $sql = "UPDATE `2021_team-b_user` SET  
                //     point=point+:add_point,
                //     score_easy=IF(:lv='easy', IF(score_easy<:total, :total, score_easy), score_easy), 
                //     score_hard=IF(:lv='hard', IF(score_hard<:total, :total, score_hard), score_hard) ,
                //     last_play_time = now(),
                //     play_count=play_count-1
                //     WHERE id=:id";
                //     echo 111;
                
                // 本番
                if($_SESSION["play_count"] > 0){
                    $sql = "UPDATE `2021_team-b_user` SET  
                    point=point+:add_point,
                    score_easy=IF(:lv='easy', IF(score_easy<:total, :total, score_easy), score_easy), 
                    score_hard=IF(:lv='hard', IF(score_hard<:total, :total, score_hard), score_hard),
                    last_play_time = now(),
                    play_count=play_count-1
                    WHERE id=:id";
                    // echo 111;
                } 
                else {
                    $sql = "UPDATE `2021_team-b_user` SET  
                    score_easy=IF(:lv='easy', IF(score_easy<:total, :total, score_easy), score_easy), 
                    score_hard=IF(:lv='hard', IF(score_hard<:total, :total, score_hard), score_hard),
                    last_play_time = now()
                    WHERE id=:id";
                    // echo 222;
                }
                
                
                $stmt = $pdo->prepare($sql);
                if($_SESSION["play_count"] > 0){
                    $stmt->bindValue(":add_point", $addPoint, PDO::PARAM_INT);
                }
                $stmt->bindValue(":lv", $level, PDO::PARAM_STR);
                $stmt->bindValue(":total", $total, PDO::PARAM_INT);
                $stmt->bindValue(":id", $_SESSION["user_id"], PDO::PARAM_INT);
    
                $stmt->execute();
                //echo $stmt->rowCount();
    
                if ($stmt->rowCount() == 1){
                    $sql2 = "SELECT play_count FROM `2021_team-b_user` WHERE id=:id";
                    $stmt2 = $pdo->prepare($sql2);
                    $stmt2->bindValue(":id", $_SESSION["user_id"], PDO::PARAM_INT);
    
                    $stmt2->execute();

                    if($stmt2->rowCount() == 1){
                        $result2 = $stmt2->fetch();
                        $_SESSION["play_count"] = $result2["play_count"];
                        // echo $_SESSION["play_count"];
                        // echo "変更有";  
                    } else {
                        // echo "変更失敗";
                    }

                      
                } else {
                    // echo "エラー";
                }
            }
            
            // 表示・last_watch_ad_time参照
            $sql3 = "SELECT point, last_watch_ad_time, last_play_time FROM `2021_team-b_user` WHERE id=:id";
                $stmt3 = $pdo->prepare($sql3);
                $stmt3->bindValue(":id", $_SESSION["user_id"], PDO::PARAM_INT);

                $stmt3->execute();

            if ($stmt3->rowCount() == 1){
                $result3 = $stmt3->fetch();
                $point = $result3["point"];
                $lastWatchAdTime = $result3["last_watch_ad_time"];
                $lastPlayTime = $result3["last_play_time"];

                // echo date('Ymd', strtotime($lastWatchAdTime)) != date('Ymd', strtotime($lastPlayTime));
                
            }  else {
                // echo "エラーだお";
            }

            

        } catch(PDOException $e){
            var_dump(($e->getMessage()));
        }

        $pdo = null;
        
    };

    //共通ヘッダの読み込み    
    include(dirname(__FILE__) . '/header.php');
?>

<body>
    <div id="show_ad">
        <!-- スキップ部分 -->
        <div id="skip"></div>

        <input class="sound" id="sound" type="image" onclick="sound();">

        <!-- /広告動画部分 -->
        <video class="video-js" data-setup={} playsinline autoplay muted oncontextmenu="return false;">
            <script type="text/javascript">
                // 広告ランダム再生
                var ads = [
                    "<source src='ad/sample1.mp4' type='video/mp4'>",
                    "<source src='ad/sample2.mp4' type='video/mp4'>",
                    "<source src='ad/sample3.mp4' type='video/mp4'>",
                    "<source src='ad/sample4.mp4' type='video/mp4'>",
                    "<source src='ad/sample5.mp4' type='video/mp4'>"
                ];
                var rand = Math.floor(Math.random() * ads.length);
                document.write(ads[rand]);

                var videoElem = document.querySelector("video");

                var countTime = 0;
                var skipTime = 5; // スキップできるまでの時間 
                var skipText;

                let time;

                
                 
                var counts = function() {
                    if(countTime < skipTime){
                            skipText = "<p>" + (skipTime - countTime) + "秒後にスキップできます</p>";
                            countTime++;
                    } else {
                        skipText = "<form class='form' action='level_select.php' method='POST'>" 
                            + "<input type='submit' class='reset_play_count' name='reset' value='×'>" 
                            // + "<input type='hidden' name='last_watch_ad_time' value=>" 現在時刻
                            + "</form>"; // level_selectに遷移
                        clearInterval(time);
                    }

                    document.getElementById("skip").innerHTML = skipText;
                }

                videoElem.addEventListener('play', function() {
                    if (document.cookie.split( '; ' )[ 0 ].split( '=' )[ 1 ] == "off"){
                        videoElem.muted = true;
                    }
                    else videoElem.muted = false;
                    sndImg();
                    counts();
                    time = setInterval(function(){
                        counts()
                    }, 1000);
                });

                function sndImg() {
                    var a = document.getElementById("sound");
                    // console.log(a);

                    if (videoElem.muted){
                        a.setAttribute("src", "img/level_select_ranking/sound_off.png");
                    }
                    else {
                        a.setAttribute("src", "img/level_select_ranking/sound_on.png");
                    }
                    

                    
                }
                
                function sound() {
                    
                    if (!videoElem.muted){
                        videoElem.muted = true;
                        console.log("変更on");
                        
                    }
                    else {
                        videoElem.muted = false;
                        console.log("変更off");
                        
                    }

                    sndImg();
                    
                }


            </script>

        </video>



    </div>


    <div id="result">
        <div class="frame">
            <div class="judge">
            <?php 
                if($clear){ // クリア ?>
                    <img src="img/result/clear.png">
                    <p class="judge_clear">クリア</p>
                <?php } else if(!$clear) { // 失敗 ?> 
                    <?php  if($level == "easy") { // 失敗 ?> 
                        <img src="img/result/failure_easy.png">
                    <?php } else {?>
                        <img src="img/result/failure_hard.png">
                    <?php } ?>
                    <p class="judge_failure">失敗</p>
                <?php };
            ?>
            </div>

                
            <div class="score">
                
                    
                <p class="residue">×<?php echo $life; ?></p> <!-- 残基 -->
                <p class="mask">×<?php echo $score; ?></p> <!-- 着けた -->
                <p class="game_score">スコア</p>
                <p class="game_score_point"><?php echo $total; ?></p> <!-- residue * mask -->
                <p class="get_point">
                    <?php
                    if($getPoint && $clear){
                            // $_SESSION["play_count"]--;
                            echo $addPoint; ?> ポイントゲット！ 
                    <?php
                        } else if (!$getPoint && $clear) {?>
                            ポイントは獲得済みです
                    <?php
                        } ?>
                </p>
                <p class="now_point">現在 <?php echo $point; ?> ポイント</p> <!-- 現在のポイント -->
                <img src="img/result/result.png">

                <!-- ゲームスコアのリセット -->
                <!-- <form action="result.php" method="POST">
                    <input type="hidden" name="level" value="">
                    <input type="hidden" name="life" value="0">
                    <input type="hidden" name="score" value="0">
                </form> -->

            </div>

            <div class="trans">
                <script>
                    //初期表示は非表示
                    document.getElementById("show_ad").style.display ="none";
                </script>
                    
                    
                <!-- 動画広告 retryで$_SESSION["play_count"]=1にする -->
                <?php 
                    if($point < 50 && $clear && date('Ymd', strtotime($lastWatchAdTime)) != date('Ymd', strtotime($lastPlayTime))){ ?>

                        <img class="retry_ad" src="img/result/retry-ad.png" onclick="clickBtn1()">
                    

                        <script>

                            function clickBtn1(){
                                document.getElementById("show_ad").style.display = "block"; // 広告表示
                                document.querySelector("body").style.backgroundColor = "#ffffff"; // 背景黒くする（いる？）
                                document.getElementById("result").style.display = "none"; // リザルト画面非表示
                            }

                        </script>

                    <?php } else { ?>
                        
                    <?php } ?>

                <!-- 広告見ずにリトライ -->
                    
                <img class="retry" src="img/result/retry.png" onclick="location.href='<?php echo ($level == 'easy') ? 'game_top_ez.php' : 'game_top_hard.php'; ?>'">

                <img class="back_level_select" src="img/result/back-level-select.png" onclick="location.href='level_select.php'">

                <?php if($point >= 50) { ?>
                    <p>応募する</p>
                    <img class="apply" src="img/level_select_ranking/app.png" onclick="location.href='apply.php'"> <!-- 応募フォームに遷移 -->
                <?php } ?>
                
                <script type="text/javascript">

                    var banners = [
                        "<a class='banner' target='_blank' href='https://www.mcdonalds.co.jp/'><img src='ad/sample.gif' alt='バナー'></a>",
                        "<a class='banner' target='_blank' href='https://www.google.com/?hl=ja'><img src='ad/sample2.gif' alt='バナー'></a>",
                        "<a class='banner' target='_blank' href='https://www.jp-bank.japanpost.jp/'><img src='ad/sample4.gif' alt='バナー'></a>",
                        "<a class='banner' target='_blank' href='https://www.jp-bank.japanpost.jp/'><img src='ad/sample5.gif' alt='バナー'></a>",
                        "<a class='banner' target='_blank' href='https://www.jp-bank.japanpost.jp/'><img src='ad/sample6.gif' alt='バナー'></a>",
                        "<a class='banner' target='_blank' href='https://www.jp-bank.japanpost.jp/'><img src='ad/sample7.gif' alt='バナー'></a>",
                        "<a class='banner' target='_blank' href='https://www.jp-bank.japanpost.jp/'><img src='ad/sample8.gif' alt='バナー'></a>",
                        "<a class='banner' target='_blank' href='https://www.jp-bank.japanpost.jp/'><img src='ad/sample9.gif' alt='バナー'></a>"
                    ];

                    var rand = Math.floor(Math.random() * banners.length);

                    document.write(banners[rand]);

                </script>

            </div>

            <div class="result_footer">
                <a href="campaign_top.php">トップに戻る</a>
            </div>
        </div>

        
    </div>
</body>

