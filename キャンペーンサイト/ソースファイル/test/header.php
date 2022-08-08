<?php
//クロスサイトリクエストフォージェリ（CSRF）対策
//$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
//$token = $_SESSION['token'];

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');
?>

<!DOCTYPE html>
<html>

<head>
    <!-- 文字コードをUTF-8を使う -->
    <meta charset="utf-8">

    <!-- 幅をデバイスの画面サイズにし、ユーザーによるサイズ変更をできないようにする -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- ページを全画面で表示する(iPad/iPhpne用) -->
    <meta name="apple-mobile-web-app-capable" content="yes">

    <!-- ステータスバーを半透明で表示する(iPad/iPhpne用) -->
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- 共通css -->
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="chusen.css">
 

    <!-- JqueryというJSのライブラリ(便利なメソッド群) を読み込む-->
    <script src="//code.jquery.com/jquery-1.12.1.min.js"></script>
    
    <title>サンプルキャンペーンゲーム</title>
</head>