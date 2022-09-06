<?php
    session_start();


    //共通ヘッダの読み込み    
    include(dirname(__FILE__) . '/header.php');
?>


<body id="ad">
    <div class="frame">
        <form class="form" action="level_select.php" method="POST">
            <!-- 仮 -->
            <input type="submit" class="reset_get_point" href="level_select.php" name="reset" value="もどる">
        </form>
    </div>
</body>