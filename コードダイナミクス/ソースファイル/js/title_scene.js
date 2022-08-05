class TitleScene extends Phaser.Scene {

    // コンストラクタ
    constructor() {
        // 最初に表示するシーンは第二引数true
        super({ key: 'TitleScene', active: true });
    }

    // 初期化処理
    preload() {
        //拝啓
        this.load.image('bg', 'sprites/bg1.png');

        //タイトルロゴ
        this.load.image('logo', 'sprites/title_logo.png');

        //パネル
        this.load.image('ui', 'sprites/ue.png')

        //電車
        this.load.image('densya', 'sprites/densya.png');
        this.load.image('densya2', 'sprites/densya.png');
        this.load.image('densya3', 'sprites/densya.png');

        //ライフ
        this.load.image('life', 'sprites/mask.png');

        //スコア画像
        this.load.image('score', 'sprites/score.png');

        // アニメーション画像 女マスクあり
        this.load.spritesheet('mole_1_1', 'sprites/female.png', { frameWidth: 116, frameHeight: 116, endFrame: 5 });
        // // アニメーション画像 女マスク無し
        this.load.spritesheet('mole_1_0', 'sprites/female_not.png', { frameWidth: 116, frameHeight: 116, endFrame: 5 });


        // アニメーション画像 男マスクあり
        this.load.spritesheet('mole_0_1', 'sprites/male.png', { frameWidth: 116, frameHeight: 116, endFrame: 5 });
        // // アニメーション画像 男マスク無し
        this.load.spritesheet('mole_0_0', 'sprites/male_not.png', { frameWidth: 116, frameHeight: 116, endFrame: 5 });


        //アニメーション画像　レアキャラ
        this.load.spritesheet('mole_R_0', 'sprites/jyobi.png', { frameWidth: 116, frameHeight: 116, endFrame: 5 });
        //アニメーション画像　レアキャラマスクあり
        this.load.spritesheet('mole_R_1', 'sprites/jyobi_mask.png', { frameWidth: 116, frameHeight: 116, endFrame: 5 });

        // アニメーション画像　ミスタップエフェクト
        this.load.spritesheet('noMask', 'sprites/no_mask.png', { frameWidth: 181, frameHeight: 181, endFrame: 6 });

        // アニメーション画像　タップエフェクト
        this.load.spritesheet('goMask', 'sprites/go_mask.png', { frameWidth: 116, frameHeight: 116, endFrame: 12 });

        //　アニメーション画像　タップエフェクト２
        this.load.spritesheet('tap', 'sprites/tap.png', { frameWidth: 128, frameHeight: 128, endFrame: 43 });
        //サウンドの鳴らすかフラグ

        // SE
        // this.load.audio('switch_sumi', 'audio/switchB.mp3'), { stream: true };
        // this.load.audio('switch_tan', 'audio/switchA.mp3'), { stream: true };
        if (document.cookie.split('; ')[0].split('=')[1] == "off") {
            console.log("サウンドオフ");
            this.load.audio('miss', 'audio/se/no_sound.mp3'), { stream: true };
            this.load.audio('miss2', 'audio/se/no_sound.mp3'), { stream: true };
            this.load.audio('miss3', 'audio/se/no_sound.mp3'), { stream: true };

            this.load.audio('tukeru', 'audio/se/no_sound.mp3'), { stream: true };
            this.load.audio('tukeru2', 'audio/se/no_sound.mp3'), { stream: true };
            this.load.audio('tukeruRare', 'audio/se/no_sound.mp3'), { stream: true };

            this.load.audio('densya', 'audio/se/no_sound.mp3'), { stream: true };
            this.load.audio('count', 'audio/se/no_sound.mp3'), { stream: true };
            this.load.audio('combo', 'audio/se/no_sound.mp3'), { stream: true };

            // BGM
            this.load.audio('bgm2', 'audio/se/no_sound.mp3', { stream: true });
        } else {
            this.load.audio('miss', 'audio/se/miss.mp3'), { stream: true };
            this.load.audio('miss2', 'audio/se/miss2.mp3'), { stream: true };
            this.load.audio('miss3', 'audio/se/miss3.mp3'), { stream: true };

            this.load.audio('tukeru', 'audio/se/tukeru.mp3'), { stream: true };
            this.load.audio('tukeru2', 'audio/se/tukeru2.mp3'), { stream: true };
            this.load.audio('tukeruRare', 'audio/se/rare_tukeru.wav'), { stream: true };

            this.load.audio('densya', 'audio/se/run.mp3'), { stream: true };
            this.load.audio('count', 'audio/se/countdown.mp3'), { stream: true };
            this.load.audio('combo', 'audio/se/combo.wav'), { stream: true };

            // BGM
            this.load.audio('bgm2', 'audio/bgm/いい/hard_bgm.mp3', { stream: true });
        }



    }

    create() {

        // マスク無し男出る
        this.anims.create({
            key: 'mole_0_0_show', // アニメーションの名前
            frameRate: 30, // フレームレート
            // frames: 'mole_0_1',
            // repeat: -1,
            frames: [
                { key: 'mole_0_0', frame: 4 },
                { key: 'mole_0_0', frame: 3 },
                { key: 'mole_0_0', frame: 2 },
                { key: 'mole_0_0', frame: 1 },
                { key: 'mole_0_0', frame: 0 },
            ]
        });

        // マスク無し女出る
        this.anims.create({
            key: 'mole_1_0_show', // アニメーションの名前
            frameRate: 30, // フレームレート
            // frames: 'mole_0_1',
            // repeat: -1,
            frames: [
                { key: 'mole_1_0', frame: 4 }, // mole_1_0
                { key: 'mole_1_0', frame: 3 },
                { key: 'mole_1_0', frame: 2 },
                { key: 'mole_1_0', frame: 1 },
                { key: 'mole_1_0', frame: 0 },
            ]
        })


        // マスク無し男潜る
        this.anims.create({
            key: 'mole_0_0_hide', // アニメーションの名前
            frameRate: 30, // フレームレート
            frames: [
                { key: 'mole_0_0', frame: 0 },
                { key: 'mole_0_0', frame: 1 },
                { key: 'mole_0_0', frame: 2 },
                { key: 'mole_0_0', frame: 3 },
                { key: 'mole_0_0', frame: 4 },
            ]
        });

        // マスク無し女潜る
        this.anims.create({
            key: 'mole_1_0_hide', // アニメーションの名前
            frameRate: 30, // フレームレート
            frames: [
                { key: 'mole_1_0', frame: 0 },
                { key: 'mole_1_0', frame: 1 },
                { key: 'mole_1_0', frame: 2 },
                { key: 'mole_1_0', frame: 3 },
                { key: 'mole_1_0', frame: 4 },
            ]
        });


        // マスク男出る
        this.anims.create({
            key: 'mole_0_1_show', // アニメーションの名前
            frameRate: 30, // フレームレート
            // frames: 'mole_0_1'
            frames: [
                { key: 'mole_0_1', frame: 4 },
                { key: 'mole_0_1', frame: 3 },
                { key: 'mole_0_1', frame: 2 },
                { key: 'mole_0_1', frame: 1 },
                { key: 'mole_0_1', frame: 0 },
            ]
        });

        // マスク女出る
        this.anims.create({
            key: 'mole_1_1_show', // アニメーションの名前
            frameRate: 30, // フレームレート
            // frames: 'mole_0_1'
            frames: [
                { key: 'mole_1_1', frame: 4 },
                { key: 'mole_1_1', frame: 3 },
                { key: 'mole_1_1', frame: 2 },
                { key: 'mole_1_1', frame: 1 },
                { key: 'mole_1_1', frame: 0 },
            ]
        });

        // マスク男もぐる
        this.anims.create({
            key: 'mole_0_1_hide', // アニメーションの名前
            frameRate: 30, // フレームレート
            frames: [
                { key: 'mole_0_1', frame: 0 },
                { key: 'mole_0_1', frame: 1 },
                { key: 'mole_0_1', frame: 2 },
                { key: 'mole_0_1', frame: 3 },
                { key: 'mole_0_1', frame: 4 },
            ]
        });

        // マスク女もぐる
        this.anims.create({
            key: 'mole_1_1_hide', // アニメーションの名前
            frameRate: 30, // フレームレート
            frames: [
                { key: 'mole_1_1', frame: 0 },
                { key: 'mole_1_1', frame: 1 },
                { key: 'mole_1_1', frame: 2 },
                { key: 'mole_1_1', frame: 3 },
                { key: 'mole_1_1', frame: 4 },
            ]
        });
        // レアキャラ出る
        this.anims.create({
            key: 'mole_R_0_show', // アニメーションの名前
            frameRate: 30, // フレームレート
            frames: [
                { key: 'mole_R_0', frame: 4 },
                { key: 'mole_R_0', frame: 3 },
                { key: 'mole_R_0', frame: 2 },
                { key: 'mole_R_0', frame: 1 },
                { key: 'mole_R_0', frame: 0 },
            ]
        });

        // レアキャラ潜る
        this.anims.create({
            key: 'mole_R_0_hide', // アニメーションの名前
            frameRate: 30, // フレームレート
            frames: [
                { key: 'mole_R_0', frame: 0 },
                { key: 'mole_R_0', frame: 1 },
                { key: 'mole_R_0', frame: 2 },
                { key: 'mole_R_0', frame: 3 },
                { key: 'mole_R_0', frame: 4 },
            ]
        });
        // マスクレアキャラ出る
        this.anims.create({
            key: 'mole_R_1_show', // アニメーションの名前
            frameRate: 30, // フレームレート
            frames: [
                { key: 'mole_R_1', frame: 4 },
                { key: 'mole_R_1', frame: 3 },
                { key: 'mole_R_1', frame: 2 },
                { key: 'mole_R_1', frame: 1 },
                { key: 'mole_R_1', frame: 0 },
            ]
        });

        // マスクレアキャラ潜る
        this.anims.create({
            key: 'mole_R_1_hide', // アニメーションの名前
            frameRate: 30, // フレームレート
            frames: [
                { key: 'mole_R_1', frame: 0 },
                { key: 'mole_R_1', frame: 1 },
                { key: 'mole_R_1', frame: 2 },
                { key: 'mole_R_1', frame: 3 },
                { key: 'mole_R_1', frame: 4 },
            ]
        });

        // アニメーション定義
        //タップエフェクト
        this.anims.create({
            key: 'goMask', // アニメーションの名前
            frames: 'goMask', // 画像のkey
            frameRate: 30, // フレームレート
            repeat: 0, // リピートなし
            // repeatDelay: 2000 // 繰り返し間隔(ミリ秒)

        });
        // アニメーション定義
        //　ミスタップエフェクト
        this.anims.create({
            key: 'noMask', // アニメーションの名前
            frames: 'noMask', // 画像のkey
            frameRate: 30, // フレームレート
            repeat: 0, // リピートなし
            // repeatDelay: 2000 // 繰り返し間隔(ミリ秒)
        });

        // 背景
        this.bg = this.add.sprite(500, 500, 'bg');
        this.bg.setPosition(0, 0);
        this.bg.setOrigin(0, 0);
        this.bg.setScale(1.2);

        this.logo = this.add.sprite(game.config.width / 2, 400, 'logo');

        // // タイトルの表示
        // this.titleText = this.add.text(
        //     game.config.width / 2,
        //     game.config.height / 2,
        //     '防げ！集団感染', // 表示するテキスト
        //     {
        //         font: 'italic 35pt Courier New', // bold=太文字,italic=斜体 文字サイズ 使用するフォント
        //         color: '#fafad2', // 文字色
        //         stroke: '#006400', // 文字の輪郭の色
        //         strokeThickness: 4, // 文字の輪郭の太さ
        //         align: 'center', // 文字揃え(left,right,center)
        //     });
        // this.titleText.setOrigin(0.5, 0.5);


        // 「Tap to start」テキストの表示
        this.pressText = this.add.text(
            game.config.width / 2,
            game.config.height / 2 + 200,
            "Tap to start ", {
                font: '30pt bold',
                color: '#FF4161',
                stroke: '#b22222', // 文字の輪郭の色
                strokeThickness: 4
            });
        this.pressText.setOrigin(0.5, 0.5);
        this.pressText.setScale(2, 2);
        this.oldActivePointer = game.input.activePointer.isDown;

        // getRanking(function(result) {
        //     //成功時の処理
        //     result.forEach(data => {
        //         console.log(data.name + "" + data.score);
        //     });
        // }, function(result) {
        //     //失敗時の処理
        //     console.log("しっぱい")
        // });

    }

    update(time, delta) {
        // テキストの点滅
        this.pressText.alpha = pingPong(time * 0.001, 1);

        // クリックかタッチで画面遷移する
        if (game.input.activePointer.isDown && !this.oldActivePointer) {
            this.scene.start("MainScene");
        }
        this.oldActivePointer = game.input.activePointer.isDown;
    }

}

// function getRanking(successFunc, errorFunc) {
//     var xmlhttp = new XMLHttpRequest();
//     //ローカルファイルのphpを実行
//     xmlhttp.open("GET", "http://web-network.sakura.ne.jp/games2020/teacher/unity/ranking-game/api/get_top_score.php", true);
//     xmlhttp.responseType = 'text';

//     var clientId = "student";
//     var clientSecret = "jyobi0200021";
//     var authorizationBasic = window.btoa(clientId + ':' + clientSecret);
//     xmlhttp.setRequestHeader('Authorization', 'Basic ' + authorizationBasic);
//     //phpの読み込み時の処理
//     xmlhttp.onload = function() {
//         if (xmlhttp.readyState === xmlhttp.DONE) {
//             if (xmlhttp.status === 200) { //成功ステータスコード
//                 //通信成功時の処理
//                 let obj = JSON.parse(xmlhttp.responseText)
//                 successFunc(obj);

//             } else {
//                 //通信失敗時の処理
//                 let obj = JSON.parse(xmlhttp.responseText)
//                 errorFunc(obj);
//             }
//         }
//     }
//     xmlhttp.send();
// }