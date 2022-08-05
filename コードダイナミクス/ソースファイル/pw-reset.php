<?php
//DB情報読み込み(他のHPファイル読み込み)
require_once('api/config.php');

//　共通ヘッダの読み込み
include(dirname(__FILE__) . '/header.php');
$id = $_GET["id"];

require('db/user.php'); // クラスの読み込み

ini_set('session.gc_maxlifetime', 180); // セッションの有効時間(秒)
session_start();

//アクセスフラグ2がfalseなら(URLを打ち込んで直接アクセスしたら)
if(!$_SESSION["accessFlg2"]){
    //リダイレクト
    header('Location: login.php');
}else{
    //変更ボタンが押されたら
    if (isset($_POST['pw-reset'])) {

    //フィルタリング
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    $password_verify  = filter_var($_POST['password_verify'], FILTER_SANITIZE_STRING);

    //エラーチェック
    $errors = array();
    if (strcmp($password, $password_verify) != 0) {
        $errors['pass_word_not_equal'] = "入力された二つのパスワードが違います！もう一度入力してください。";
    }
    //エラーがあればアラート表示
    if (0 < count($errors)) {
        $alertMsg = '';
        foreach ($errors as $key => $value) {
            $alertMsg = $alertMsg . $value . '\n';
        }
        echo "<script type='text/javascript'>alert('" . $alertMsg . "');</script>";
    } else {

        if (!$password == "") {
            //　パスワードを複合負荷な256bitのハッシュに変換
            $password_sha256 = hash('sha256', $password);
            try {
                $_SESSION["accessFlg3"] = true;
                //DBへ接続
                //パスワード,pinCode,pinCodeの作成時間を更新
                $user = new User(); // Userクラスのインスタンス生成
                $data = array("password" => $password_sha256,"pinCode"=>"","pincode_create_time"=>"0");
                $data = $user->updateData($id, $data);
            } catch (PDOException $e) {
                echo($e->getMessage());
            }
            //　完了画面へ遷移  
            header('Location: pw-reset-confirm.php?id=' . $id);
        } else {
            echo("パスワードを入力してください");
        }
        //  接続を閉じる
        $pdo = null;
    }
}
}

?>

<body id="pw-reset">
    <div class=frame>
        <p class="header"><img src="img/pw-logo.png"></p>
        <form class="form" action="pw-reset.php?id=<?php echo $id ?>" method="POST">
            <!-- <div class="box">
                <img class="bg" src="img/etc/box.png">
            </div> -->
            <div class="new-pw-text">
                <!-- <p class="text">新しいパスワードを入力してください</p> -->
                <div class="pw-div">
                    <p class="newpw">■新しいパスワード(8文字以上)</br></p>
                    <input class="pswd" type="password" name="password" pattern=".{8,}" size="30" maxlength="50"></br>
                    <p class="newpw">■もう一度入力して下さい</br></p>
                    <input class="pswd_verify" type="password" name="password_verify" size="30" maxlength="50" oncopy="return false" onpaste="return false" oncontextmenu="return false">
                </div>
            </div>
            <div class="btn-pw-reset-center">
                <input class="btn-pw-reset" type="submit" name="pw-reset" value="登録する">
            </div>
        </form>
    </div>
</body>
<?php
