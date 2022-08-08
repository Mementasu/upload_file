<?php
include (dirname(__FILE__)) . '/header.php';
$debug = true;
$debug2 = true;
?>

<!-- 期間前 -->

<body id="campaign_top2">
    <div class=frame>
        <p><img src="img/logo.png"></p>
        <?php
        $startDay = "2021/07/13 15:00:00";
        $endDay = "2021/08/13 14:00:00";
        $toDay = (date("Y/m/d H:i:s"));


        //if (date("Y/m/d H:i:s") < $startDay) { 
        //if ($toDay< $startDay) { 
            if(!$debug){?>
            <p><img src="img/campaign_top/start_before.png"></p>
            <p><img src="img/campaign_top/animal.png"></p>
            <p><img src="img/campaign_top/kokuti.png"></p>
            <p class="btn-challenge">キャンペーン開始前</p>

        <?php //} else if ($toDay> $startDay && $toDay < $endDay) { 
            } else if ($debug) { ?>
            <!-- 期間中 -->
            <p><img src="img/campaign_top/campaign_top1.png"></p>
            <p><img src="img/campaign_top/keihin.png"></p>
            <p><a class="btn_challenge_now" href="/games2021/collabo/team-b/login.php"> チャレンジする </a> </p>

        <?php //} else if($toDay > $endDay){
             } else if(!$debug&&!$debug2){?>
        <!--期間後  -->
                <p><img src="img/campaign_top/campaign_end.png"></p>
        <?php }?>
    </div>
</body>