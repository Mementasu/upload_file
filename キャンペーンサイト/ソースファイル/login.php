<?php
    require_once("api/config.php");

    if (isset($_GET["url_token"])) {
        $_SESSION = array();

    if(ini_get("session.use_cookies")){
        setcookie(session_name(),"", - 3600, "/");
        session_destroy();
    }
        // メール認証時の処理
        $url_token = $_GET["url_token"];
        try {
            // DB接続時
            $pdo = new PDO($dsn, $db_user, $db_password);
    
            // url_tokenが合致、AND regist_compが0の未登録者 AND 仮登録日から24時間以内
            $sql = "SELECT id FROM `2021_team-b_user` WHERE url_token=:url_token AND status=0 AND token_create_time > now() - interval 24 hour";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":url_token", $url_token, PDO::PARAM_STR);
            $stmt->execute();
    
            // レコード件数取得
            $row_count = $stmt->rowCount();
    
            // 24時間以内に仮登録され、本登録されていないトークンの場合は本登録完了とする
            if ($row_count == 1){
                $result = $stmt->fetch();
                $id = $result["id"];
    
                // 本登録完了
                $sql2 = "UPDATE `2021_team-b_user` SET status=1 WHERE id=:id";
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->bindValue(":id", $id, PDO::PARAM_STR);
                $stmt2->execute();
                
                // 接続を閉じる
                $pdo = null;
    
                // header("Location: tutorial.php"); // 相対パス
                header("Location: level_select.php");
                exit;
            } else {
                var_dump($row_count);
                var_dump($id);
                echo "このURLはご利用できません。有効期限が過ぎたかURLが間違えている可能性がございます。もう一度登録をやりなおしてください。";
                // $errors["urltoken_timeover"] = "このURLはご利用できません。有効期限が過ぎたかURLが間違えている可能性がございます。もう一度登録をやりなおしてください。"
            }
    
            
        } catch (PDOException $e) {
            echo $e;
        }
        // 接続を閉じる
        $pdo = null;
    } else if (isset($_POST["login"])){

        // ログイン処理
    
        // フォームデータの取得
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
        // エラーチェック
        $errors = array();
        if (!isset($email)){
            $errors["email"] = "正しいメールアドレスを入力してください";
        }
        if (!isset($password)){
            $errors["password"] = "パスワードを入力してください";
        }
    
        if (0 < count($errors)) {
            // エラーメッセージを連結してアラート表示
            $alertMsg = "";
            foreach ($errors as $key => $value) {
                $alertMsg = $alertMsg . $value . "\n";
            }
            echo "<script type='text/javascript'>alert('" . $alertMsg . "');</script>";
        } else {
        // パスワードを複合不可な256bitのハッシュに変換
        $password_sha256 = hash("sha256", $password);

        try {
            // DBへ接続
            $pdo = new PDO($dsn, $db_user, $db_password);
            
            // 本登録したユーザーの中からメールアドレスとパスワードが一致したユーザー情報を取得
            $sql = "SELECT id, name, now() FROM `2021_team-b_user` WHERE email=:email AND password=:password AND status=1";
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $stmt->bindValue(":password", $password_sha256, PDO::PARAM_STR);
            $stmt->execute();
            $row_count = $stmt->rowCount();
            if ($row_count == 1) {
                // 合致したユーザー情報を取得
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

                // ini_set('session.gc_divisor', 1); // 有効期限が切れたら必ずセッションを削除する
                // ini_set('session.gc_maxlifetime', 10); // セッションの有効時間(秒)

                // ログインIDを保存する
                session_start();
                session_regenerate_id(TRUE); // セッションIDを再生成

                $_SESSION["user_id"] = $user_data["id"]; // セッションにuserIDを登録(保持)
                $_SESSION["user_name"] = $user_data["name"];

                // $_SESSION["login_time"] = $user_data["now()"]; // ログイン時間更新

                $sql2 = "UPDATE `2021_team-b_user` SET login_time=now() WHERE id=:id";
                $stmt2 = $pdo->prepare($sql2);

                $stmt2->bindValue(":id", $_SESSION["user_id"], PDO::PARAM_INT);
                $stmt2->execute();

                if($stmt2->rowCount() == 1){
                    // チュートリアル画面に移動
                    header("Location: level_select.php");
                    exit();
                } else {
                    var_dump("ログイン時間更新にしっぱい");
                }

            } else {
                var_dump("パスワードかメールアドレスが違います");
            }
            } catch (PDOException $e) {
                var_dump($e->getMessage());
                echo "め";
            }
            // 接続を閉じる
            $pdo = null;
        }
    } else {
        // 初回アクセス時
        $email = "";
        $password = "";
    }

    // 共有ヘッダの読み込み
    include(dirname(__FILE__) . "/header.php");
?>

<body id ="login">
    <div class=frame>
        <img class="img-title" src="img/logo.png">
        <form class="form" action="login.php" method="POST">

            <div class="form-group">
                <label>メールアドレス</label><br>
                <input type="email" name="email" value="<?php echo $email ?>" size="30" maxlength="50">
            </div>

            <div class="form-group">
                <label>パスワード</label><br>
                <input type="password" name="password" value="<?php echo $password ?>" size="30" maxlength="50">
            </div>

            <div><input class="btn-login" type="submit" name="login" value="ログイン"></div>
        </form>

        <div class="footer">
            <p><a href="pw-edit.php">パスワードを忘れた方</a></p>
            <p><a href="regist.php">新規登録</a></p>
            <p><a href="campaign_top.php">トップに戻る</a></p>
        </div>
    </div>
</body>

</html>