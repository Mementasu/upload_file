//
//　モグラクラス
//
class Mole extends Phaser.GameObjects.Sprite {

        // コンストラクタ
        constructor(scene, id, x, y, parent) {

            // 継承元のPhaser.GameObjects.Spriteのコンストラクタの呼び出し
            super(scene, x, y, 'mole_0_0', 0); // 第五引数は分割した画像のフレーム数
            this.scene = scene;
            this.parent = parent;
            this.scene.add.existing(this);
            this.id = id; // id
            this.setOrigin(0.5, 0.5); // アンカー左上
            this.setScale(1);
            this.showTime = Phaser.Math.Between(450, 650); //モグラの顔が出てる時間
            this.hideTime = Phaser.Math.Between(150, 350); //モグラの顔が出てない時ｋな
            this.changeSpan = 0; //切り替え時間

            // 確率
            this.summonRate = 49; //マスク男女が出る確率
            this.bounusRate = 3; //レアキャラ（ジョビ子ちゃん）が出る確率

            // 生成するモグラの設定
            this.baseName = "mole";
            this.isSex = Phaser.Math.Between(0, 1); //出てくるモグラの性別を決める確率
            this.isMask = Phaser.Math.Between(0, 99) < this.summonRate; // true or false
            this.isRare = Phaser.Math.Between(0, 99) < this.bounusRate; // true or false
            this.isShow = false; // 表示状態
            this.isWhac = false; // 叩かれたかどうか

            //モグラを叩いた時のポイント
            this.wackPoint = 10;
            this.rarePoint = 100;

            // 画像のクリック処理の実装
            this.setInteractive();
            this.on(
                'pointerdown', // イベント名
                this.onDown, // 呼び出す関数
                this // 関数に渡すコンテキスト(自身を渡す)
            );

        }



        preUpdate(time, delta) {

            if (this.scene.gameState != this.scene.GameState.Game) return;

            // たたかれていなければ一定間隔で画像を切り替える
            // if (!this.isWhac) {
            super.preUpdate(time, delta); // アニメーションの更新は継承元で行われる

            this.changeSpan += delta;
            if (this.isShow && (this.showTime < this.changeSpan)) {
                // これから隠れる時の処理
                this.changeSpan = 0;
                this.showTime = Phaser.Math.Between(1000, 1600); // 表示時間を再セット
                this.onHide();
            } else if (!this.isShow && (this.hideTime < this.changeSpan)) {
                // これから現れる時の処理
                this.changeSpan = 0;
                //  this.oneEffectFlg = false;
                this.hideTime = Phaser.Math.Between(150, 300); // 非表示時間を再セット
                this.onShow();
            }
            //    }
        }

        // 表示
        // タイトルシーンでもう一つポイントマイナスのアニメーション（Show,Hide）を定義
        onShow() {

            // 状態再設定
            this.isShow = true;
            this.isWhac = false;
            this.isRare = Phaser.Math.Between(0, 99) < this.bounusRate; // true or false

            if (this.isRare) {
                this.isMask = false;
            } else {
                this.isMask = Phaser.Math.Between(0, 99) < this.summonRate; // true or false
            }

            this.play(this.getAnimKeyName());
        }

        // 隠れる
        onHide() {
            this.isShow = false;

            this.play(this.getAnimKeyName());

        }


        getKeyName() {
            let keyName = this.baseName;
            keyName += this.isRare ? '_R_' : '_' + (this.isSex ? 1 : 0) + '_';
            keyName += this.isMask ? 1 : 0;
            return keyName;
        }

        getAnimKeyName() {
            return this.getKeyName() + (this.isShow ? '_show' : '_hide');
        }

        getGrobalPosX() {
            return this.parentContainer.x + this.x;
        }

        getGrobalPosY() {
            return this.parentContainer.y + this.y;
        }

        // クリック処理
        onDown(pointer) {

            if (this.scene.gameState != this.scene.GameState.Game) return;
            if (this.isWhac) return;

            this.isWhac = true;

            if (this.frame.name < 1 && !this.isMask) {
                // タップされたときの処理
                this.isMask = true;

                // 3秒間は表示する
                this.showTime = 3000;


                //レアモグラかそうでないかによってスコアとポイントの量を変える
                if (this.getKeyName() == "mole_R_1") {
                    this.scene.tukeru.play();
                    this.scene.tukeruRare.play();

                    //スコア加算
                    this.scene.score += this.rarePoint;

                    //コンボ加算
                    this.scene.combo++;

                    //10コンボにつき100スコア加算
                    if (this.scene.combo % 10 == 0) {
                        this.scene.score += 150;
                        this.scene.comboSe.play();

                    }

                    //上にシューってでるポイントテキスト
                    this.pointText = new MoveText(this.scene, this.getGrobalPosX(), this.getGrobalPosY(), "+" + this.rarePoint, "#ff0000");

                    //上にシューってでるコンボテキスト
                    this.comboText = new MoveText(this.scene, this.getGrobalPosX(), this.parentContainer.y + this.y + 65, this.scene.combo + "combo!", "#ff4500");

                } else {
                    this.scene.tukeru.play();
                    this.scene.tukeru2.play();

                    //スコア加算
                    this.scene.score += this.wackPoint;
                    //コンボ加算
                    this.scene.combo++;
                    //10コンボにつき100スコア加算
                    if (this.scene.combo % 10 == 0) {
                        this.scene.score += 150;
                        this.scene.comboSe.play();

                    }
                    //上にシューってでるテキスト
                    this.pointText = new MoveText(this.scene, this.getGrobalPosX(), this.getGrobalPosY(), "+" + this.wackPoint, "#ff0000");

                    //上にシューってでるコンボテキスト
                    this.comboText = new MoveText(this.scene, this.getGrobalPosX(), this.getGrobalPosY() + 65, this.scene.combo + "combo!", "#ff4500");



                }


                // SE再生
                // this.scene.seSwitchDown2.play();


                // タップエフェクト再生
                let goMask = this.scene.add.sprite(this.x, this.y, 'goMask', 0);
                goMask.play({
                    key: 'goMask', // 指定したkeyのアニメーションを実行
                    // delay: Math.random() * 3000 // 再生までの時間
                });
                this.parent.add(goMask);


                // 一定時間後に画像を切り替える
                this.scene.time.delayedCall(
                    400, // 待機する時間ms
                    function() {
                        // 画像切り替え
                        this.setTexture(this.getKeyName(), 0);
                    }, // 実行する関数
                    [], // 関数の引数
                    this); // 関数の中で使用するthisの値
            } else {

                //スコア減算
                if (this.scene.score > 0) {
                    this.scene.score -= this.wackPoint;
                    this.pointText = new MoveText(this.scene, this.getGrobalPosX(), this.getGrobalPosY(), "-" + this.wackPoint, "#0000cd");

                }

                //コンボ初期化
                this.scene.combo = 0;

                //ライフ減算　ライフ画像を消す
                if (this.scene.life > 0) {
                    this.scene.lifeImage[--this.scene.life].destroy();
                }

                // SE再生
                // this.scene.seSwitchDown.play();
                this.scene.missSe.play();
                this.scene.missSe2.play();
                this.scene.missSe3.play();

                // ミスタップエフェクト再生
                let noMask = this.scene.add.sprite(this.x, this.y, 'noMask', 0);
                noMask.play({
                    key: 'noMask', // 指定したkeyのアニメーションを実行
                    // delay: Math.random() * 3000 // 再生までの時間
                });
                this.parent.add(noMask);
            }

        }
    }
    // 往復移動クラス
class Pingpong extends Phaser.GameObjects.GameObject {
    constructor(scene, target, moveX, moveY, speed, isPingpong = true) {
        super(scene);
        this.scene = scene;
        this.scene.add.existing(this);
        this.target = target;
        this.setMovePos(moveX, moveY);
        this.speed = speed;
        this.isPingpong = isPingpong;

        this.startX = this.target.x;
        this.startY = this.target.y;
        this.time = 0;
        this.isPause = true;
        this.isReverce = false;
    }

    pingPong(t, length) {
        if (length == 0) return 0;

        let l = length * 2;
        let r = t % l;

        if (length < r) {
            this.isReverce = false;
            return l - r;
        } else {
            this.isReverce = true;
            return r;
        };
    }

    setMovePos(moveX, moveY) {
        this.moveX = moveX;
        this.moveY = moveY;
        this.direction = new Phaser.Math.Vector2(this.moveX, this.moveY).normalize();
    }

    preUpdate(time, delta) {
        if (this.isPause) return;

        this.time += delta;
        let t = this.time * 0.001 * this.speed;

        let ppx = this.pingPong(t * this.direction.x, this.moveX);
        this.target.x = this.startX + ppx;
        this.target.y = this.startY + this.pingPong(t * this.direction.y, this.moveY);

        let children = this.target.getAll();
        children.forEach(child => {

            child.setScale(this.isReverce ? -1 : 1, 1);
        });

        if (this.isPingpong) {
            // 画面外に出たら反対側から戻ってくる
            //this.target.x = Phaser.Math.Wrap(this.target.x, 0, game.config.width);
            this.target.y = Phaser.Math.Wrap(this.target.y, 0, game.config.height);
        }
    }
}


///
///　上にいきながら一定時間で消えるテキストクラス
///
class MoveText extends Phaser.GameObjects.Text {

        // コンストラクタ
        constructor(scene, x, y, moji, textColor) {

            // 継承元のPhaser.GameObjects.Spriteのコンストラクタの呼び出し
            super(scene, x, y, moji, {
                font: 'italic 40pt bold', // bold=太文字,italic=斜体 文字サイズ 使用するフォント
                color: textColor, // 文字色
                stroke: '#fafad2', // 文字の輪郭の色
                strokeThickness: 5, // 文字の輪郭の太さ
                align: 'center', // 文字揃え(left,right,center)
            }); // 第五引数は分割した画像のフレーム数

            this.scene = scene;
            this.scene.add.existing(this);


            this.scene.time.addEvent({
                delay: 500,
                callback: function() {
                    this.destroy();
                },
                callbackScope: this,
                loop: false,
            });
        }



        preUpdate(time, delta) {
            this.y -= delta / 1000 * 150;
        }

    }
    //
    // ステージの基底クラス
    //
class MainScene extends Phaser.Scene {

    constructor() {
        super({ key: 'MainScene', active: false })
    }

    create() {
        //選択した難易度
        this.level = "easy";
        this.combo = 0;
        // 背景
        this.bg = this.add.sprite(500, 500, 'bg');
        this.bg.setPosition(0, 0);
        this.bg.setOrigin(0, 0);
        this.bg.setScale(1.2);

        //パネル
        this.ui = this.add.sprite(110, 110, 'ui');
        this.ui.setOrigin(0, 0);
        this.ui.setScale(1.2);

        //ライフ画像
        this.lifeImage = [3];
        this.lifeImage[0] = this.add.sprite(130, 170, 'life');
        this.lifeImage[0].setOrigin(0, 0);
        this.lifeImage[0].setScale(1.4);

        this.lifeImage[1] = this.add.sprite(255, 170, 'life');
        this.lifeImage[1].setOrigin(0, 0);
        this.lifeImage[1].setScale(1.4);

        this.lifeImage[2] = this.add.sprite(380, 170, 'life');
        this.lifeImage[2].setOrigin(0, 0);
        this.lifeImage[2].setScale(1.4);

        //ポイント画像
        this.scoreImage = this.add.sprite(160, 280, 'score');
        this.scoreImage.setOrigin(0, 0);

        //獲得スコア　→　マスク男女を叩いてゲットできるやつ
        this.score = 0;


        // SE
        // this.seSwitchDown = this.sound.add('switch_tan');
        // this.seSwitchDown2 = this.sound.add('switch_sumi'), { volume: 1 };
        this.missSe = this.sound.add('miss', { volume: 1 });
        this.missSe2 = this.sound.add('miss2', { volume: 1 });
        this.missSe3 = this.sound.add('miss3', { volume: 3 });
        this.tukeru = this.sound.add('tukeru', { volume: 3 });
        this.tukeru2 = this.sound.add('tukeru2', { volume: 3 });
        this.tukeruRare = this.sound.add('tukeruRare', { volume: 3 });
        this.densya_run = this.sound.add('densya', { volume: 3 });
        this.countDown = this.sound.add('count');
        this.comboSe = this.sound.add('combo', { volume: 5 });
        //BGM
        this.bgm = this.sound.add('bgm2', { volume: 1 });


        // ゲームのステータス
        this.GameState = { Standby: 0, Game: 1, Clear: 2, End: 3 };
        this.gameState = this.GameState.Standby;

        this.countDown.play();
        // 開始までの時間
        this.standbyCountDown = 3;

        // 制限時間
        this.gameTime = 30;

        //残基
        this.life = 3;
        this.moles = [];
        this.molePosList = [
            // モグラの場所
            { x: 320, y: 0 },
            { x: 60, y: 0 },
            { x: -120, y: 0 },
            { x: -290, y: 0 },

        ]

        // コンテナ生成位置
        let containers = [
            { x: -500, y: 590 },
            { x: -500, y: 790 },
            { x: -500, y: 990 },
            { x: -500, y: 500 }
        ];

        // コンテナ3つ生成
        this.containers = [];
        for (let i = 0; i < 3; i++) {
            this.containers[i] = this.add.container(containers[i].x, containers[i].y); // コンテナ生成
            this.containers[i].add(this.add.sprite(0, 0, 'densya')); // 電車生成

            // モグラの生成
            for (let j = 0; j < this.molePosList.length; j++) {
                let mole = new Mole(
                    this,
                    j,
                    this.molePosList[j].x,
                    this.molePosList[j].y,
                    this.containers[i]
                );
                this.moles.push(mole);
                this.containers[i].add(mole);
            }

            // 移動処理
            this.containers[i].pingPong = new Pingpong(
                this, // 現在のシーン
                this.containers[i], // 移動させるターゲット
                1600, // x
                0, // y
                Phaser.Math.Between(100, 225)); // 移動速度
        }

        //  カウントダウンテキストの表示
        this.timeText = this.add.text(game.config.width / 2 - 30, 40, game.time, {
            font: '55pt Ravie',
            color: '#000',
            stroke: '#fff',
            strokeThickness: 3,
            align: 'center',
        });

        //スコアテキストの表示
        this.scorText = this.add.text(385, 315, '', {
            font: '60pt Bauhaus',
            color: '#ff6347', // 文字色
            stroke: '#b22222', // 文字の輪郭の色
            strokeThickness: 8,
            align: 'center',
        });



        // 開始テキストの表示
        this.standbyText = this.add.text(
            game.config.width / 2 - 150,
            game.config.height / 2,
            '開始まで:' + this.standbyCountDown, {
                font: '45pt Ravie',
                color: '#fff', // 文字色
                stroke: '#b22222', // 文字の輪郭の色
                strokeThickness: 8,
                align: 'center',
            });



        // ゲーム開始前のカウントダウン
        let cuontDownTimer = this.time.addEvent({
            delay: 1000,
            //standbyCountDown - 1を入れる
            repeat: 2,
            callback: function() {
                this.standbyText.text = '開始まで:' + --this.standbyCountDown;

                // カウントダウンが0になったらゲームを開始する
                if (this.standbyCountDown == 0) {

                    this.standbyText.destroy();
                    this.gameStart();
                    this.bgm.play();
                    for (let i = 0; i < this.containers.length; i++) {
                        this.containers[i].pingPong.isPause = false;
                    }

                }
            },
            callbackScope: this,
        });
    }
    update() {
            //スコア表示
            this.scorText.text = '' + this.score;
        }
        // ゲーム開始処理
    gameStart() {
        this.gameState = this.GameState.Game;
        this.densya_run.play();
        // 1秒間隔で時間を更新する
        this.countDownTimer = this.time.addEvent({
            delay: 1000, // 実行間隔
            callback: function() {
                this.timeText.text = --this.gameTime;
                // 制限時間が０もしくはライフが０でゲーム終了
                if (this.gameTime == 0 || this.life == 0) {
                    this.countDownTimer.remove();
                    this.gameEnd();
                }
            }, // 実行する関数
            // args: [], // 関数へ渡す引数
            callbackScope: this, // 関数の中で使用するthisの値
            loop: true // ループ
        });

    }


    gameEnd() {

        this.gameState = this.GameState.End;

        for (let i = 0; i < this.containers.length; i++) {
            this.containers[i].pingPong.isPause = true;
        }

        // POST先(画面遷移先のPHP)
        var url = "https://web-network.sakura.ne.jp/games2021/collabo/team-b/result.php";

        // パラメータを付与する場合
        var inputs = '';
        //スコア、ライフ、難易度を送る
        var params = [
            ["score", this.score],
            ["life", this.life],
            ["level", this.level]
        ]; // POSTで次の画面に送りたい値z
        for (var i = 0, n = params.length; i < n; i++) {
            inputs += '<input type="hidden" name="' + params[i][0] + '" value="' + params[i][1] + '" />';
        }

        // POST遷移
        $("body").append('<form action="' + url + '" method="post" id="post">' + inputs + '</form>');
        $("#post").submit();

        let resultText = "Game Over";

        this.gameEndText = this.add.text(
            game.config.width / 2,
            game.config.height / 2,
            resultText, // 表示するテキスト
            {
                font: 'italic 50pt bold', // bold=太文字,italic=斜体 文字サイズ 使用するフォント
                color: '#0', // 文字色
                stroke: '#fafad2', // 文字の輪郭の色
                strokeThickness: 5, // 文字の輪郭の太さ
                align: 'center', // 文字揃え(left,right,center)
            });
        this.gameEndText.setOrigin(0.5, 0.5);
    }

    //タップ処理
    onDown(pointer) {
        //アニメーション生成    
        let tap = this.add.sprite(this.x, this.y, 'tap', 0);
        tap.play({
            key: 'tap', // 指定したkeyのアニメーションを実行
            // delay: Math.random() * 3000 // 再生までの時間
        });

    }
}