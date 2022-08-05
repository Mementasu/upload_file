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
        // echo $_SESSION["login_time"];

        // DB情報取得
        require_once("api/config.php");

        try {
            // DBへ接続
            $pdo = new PDO($dsn, $db_user, $db_password);

            $sql = "SELECT point, last_play_time, login_time, user_name FROM `2021_team-b_user` WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":id", $_SESSION["user_id"], PDO::PARAM_INT);

            $stmt->execute();
            // echo $stmt->rowCount();

            if ($stmt->rowCount() == 1){
                $result = $stmt->fetch();
                $lastPlayTime = $result["last_play_time"];
                $loginTime = $result["login_time"];
                $point = $result["point"];
                $user_name = $result["user_name"];

                //echo date('Ymd', strtotime($lastPlayTime));
                //echo date('Ymd', strtotime($loginTime));

                // play_count参照
                $sql2 = "SELECT play_count FROM `2021_team-b_user` WHERE id=:id";
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->bindValue(":id", $_SESSION["user_id"], PDO::PARAM_INT);

                $stmt2->execute();

                if($stmt2->rowCount() == 1) { 
                    $result2 = $stmt2->fetch();
                    $playCount = $result2["play_count"];
                    // echo $result2["play_count"];

                    if(date('Ymd', strtotime($lastPlayTime)) != date('Ymd', strtotime($loginTime))){
                        // LastPlayTimeが今日でなければPlayCountを1にリセットする 
                        // 広告を見れるようにする
                        // $_SESSION["play_count"] = 1; // ポイント獲得管理

                        // echo "日が違う";
    
                        if($playCount != 1){ // play_count != 1
                            // echo "0時リセット";
                            $sql3 = "UPDATE `2021_team-b_user` SET play_count=1 WHERE id=:id";
    
                            $stmt3 = $pdo->prepare($sql3);
                            $stmt3->bindValue(":id", $_SESSION["user_id"], PDO::PARAM_INT);
                            
                            $stmt3->execute();
                            // echo $stmt3->rowCount();
    
                            if ($stmt3->rowCount() == 1) {
                                // echo "stmt3問題なし";
                                $playCount = 1;
                            } else {
                                // echo "stmt3エラー";
                            }
    
                        } else { // play_count == 1
                            // echo "リセット更新なし";
                        }
                        
                       
    
                    } else if(isset($_POST["reset"])){
                        // 広告を見てきたならPlayCountを+1
                        // $_SESSION["play_count"] += 1; // ポイント獲得管理
                        // if LastWachAdTimeが今日でなければ広告を流せる　
                        // 広告を見たらPlayCountを+1にし、LastWachAdTimeを更新
        
                        // echo "広告見てきた";
                        $sql5 = "UPDATE `2021_team-b_user` SET play_count=play_count+1, last_watch_ad_time=now() WHERE id=:id";
    
                        $stmt5 = $pdo->prepare($sql5);
    
                        $stmt5->bindValue(":id", $_SESSION["user_id"], PDO::PARAM_INT);
                        
                        $stmt5->execute();
                        // echo $stmt5->rowCount();
    
                        if ($stmt5->rowCount() == 1){
                            $sql6 = "SELECT play_count FROM `2021_team-b_user` WHERE id=:id";
                            $stmt6 = $pdo->prepare($sql6);
                            $stmt6->bindValue(":id", $_SESSION["user_id"], PDO::PARAM_INT);
    
                            $stmt6->execute();
                            if($stmt6->rowCount() == 1) {
                                // echo "プレイ可能回数更新";
                                $result3 = $stmt6->fetch();
                                $playCount = $result3["play_count"];
                            } else {
                                // echo "stmt6エラー";
                            }
                        } else {
                            // echo "stmt5エラー";
                        }
    
                    } else {
                        // echo "更新なし";
                    }

                } else {
                    // echo "stmt2エラー";
                }

                // echo "プレイ可能回数更新";
                $_SESSION["play_count"] = $playCount;
                // echo $_SESSION["play_count"];

                // echo "現在" . $playCount . "回ポイントもらえる";

            } else {
                // playcountのみ表示
                

                // カラム名変える

                // 難易度選択画面に入る
                // if LastPlayTimeが今日でなければPlayCountを1にリセットする last_login_time -> last_play_time　終
                // if PlayCountが0より大きければプレイできる get_point -> play_count 終
                // ゲーム

                // リザルト画面に遷移する
                // LastPlayTimeを更新する　終
                // PlayCountを-1する　終
                // if LastWachAdTimeが今日でなければ広告を流せる　
                // 広告を見たらPlayCountを+1にし、LastWachAdTimeを更新

                // echo "あぺ";
                // $errors["urltoken_timeover"] = "このURLはご利用できません。有効期限が過ぎたかURLが間違えている可能性がございます。もう一度登録をやりなおしてください。"
            }

            


        } catch(PDOException $e){
            var_dump(($e->getMessage()));
        }
        $pdo = null;
    }


    // var_dump("セッションに保存されているユーザーIDは" . $_SESSION["login"] . "です");
    // echo "こんにちは" . $user_name . "さん";


//共通ヘッダの読み込み
include(dirname(__FILE__) . '/header.php');
?>

<body id="level_select">

    <div class=frame >
        <div class="edit">
            <input type="submit" value="登録情報編集"  onclick="location.href='regist_edit.php'">

            

        </div>
        

        <div class="point">
            <p class="user">user: <?php echo $user_name ?> </p>
            <p class="now_point">現在のポイント <?php echo $point; ?></p>
            <p class="check_point"><?php if ($point < 50) { ?> 景品まであと <?php echo 50 - $point; ?> ポイント
            <?php } else { ?> 景品と交換できます <?php } ?></p>
        </div>

        <div class="select">
            <div class="select_img">
                <p>ゲーム</p>
                <img src="img/level_select_ranking/game.png">
            </div>
            <div class="select_level">
                
                <input class="sound" id="sound" type="image" onclick="sound();">
                
                <script type="text/javascript">
                // document.getElementById('level_select').getElementsByClassName('sound');
                //console.log(document.getElementById('level_select').getElementsByClassName('frame'));
                function sndImg() {
                    var a = document.getElementById("sound");
                    // console.log(a);

                    if (document.cookie.split( '; ' )[ 0 ].split( '=' )[ 1 ] == "off" || document.cookie.split( '; ' )[ 0 ].split( '=' )[ 0 ] != "sound"){
                        a.setAttribute("src", "img/level_select_ranking/sound_off.png");
                    }
                    else {
                        a.setAttribute("src", "img/level_select_ranking/sound_on.png");
                    }
                    

                    
                }
                
                function sound() {
                    
                    if (document.cookie.split( '; ' )[ 0 ].split( '=' )[ 1 ] == "off" || document.cookie.split( '; ' )[ 0 ].split( '=' )[ 0 ] != "sound"){
                        document.cookie = "sound=on";
                        console.log("変更on");
                        
                    }
                    else {
                        document.cookie = "sound=off";
                        console.log("変更off");
                        
                    }

                    sndImg();
                    
                }

                if (document.cookie.split( '; ' )[ 0 ].split( '=' )[ 0 ] != "sound") sound();

                sndImg();

            </script>
                
                <p class="add_point">
                    <?php
                        if($_SESSION["play_count"] > 0){ ?> 　クリアしてポイントゲット！
                        <?php } else { ?> 本日分のポイントは獲得済みです <?php }
                    ?>
                </p>
                <input class="select_easy" type="submit" name="select-easy" value="   やさしい" onclick="location.href='game_top_ez.php'">
                <p class="add_point">
                    <?php
                        if($_SESSION["play_count"] > 0){ ?> 　たくさんポイントゲット！
                        <?php } else { ?> 本日分のポイントは獲得済みです <?php }
                    ?>
                </p>
                        
                <input class="select_hard" type="submit" name="select-hard" value="   むずかしい" onclick="location.href='game_top_hard.php'">
                        
                <input class="select_tutorial" type="submit" name="select-tutorial" value="   あそびかた" onclick="location.href='game_tutorial.php'">
            
            </div>
            
        </div>

        <div class=ranking>
            <p>ランキング</p>
            <img src="img/level_select_ranking/ranking.png">
            <div class="select_rank">
                <form action="ranking.php" method="GET">
                    <input class="ranking_easy" type="submit" name="ranking" value="やさしい" onclick="location.href='ranking.php'">
                    <input class="ranking_hard" type="submit" name="ranking" value="むずかしい" onclick="location.href='ranking.php'">
                </form>
            </div>
        </div>
        <div class=keihin>
            <p><?php if ($point < 50) { ?> ５０ポイントで応募できるよ <?php } else { ?> ここから応募ができます <?php } ?></p>
            <img src="img/level_select_ranking/app.png" <?php if ($point >= 50) { ?> onclick="location.href='apply.php'" <?php } ?> >
        </div>

        <div class="level_select_footer">
            <a href="campaign_top.php">トップに戻る</a>
        </div>

    </div>
</body>