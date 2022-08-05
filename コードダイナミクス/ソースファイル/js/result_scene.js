class ResultScene extends Phaser.Scene {

    // コンストラクタ
    constructor() {
        // 最初に表示するシーンは第二引数true
        super({ key: 'ResultScene', active: false });
    }

    // 初期化処理
    preload() {}

    // 開始処理
    create() {

        // テキストの表示
        this.resultText = this.add.text(
            game.config.width / 2,
            game.config.height / 2,
            'GameClear', // 表示するテキスト
            {
                font: 'italic 25pt Courier New', // bold=太文字,italic=斜体 文字サイズ 使用するフォント
                color: '#fff', // 文字色
                stroke: '#ff0', // 文字の輪郭の色
                strokeThickness: 2, // 文字の輪郭の太さ
                align: 'center', // 文字揃え(left,right,center)
            });

        // タイトルテキスト or 画像を表示する
        this.resultText.setOrigin(0.5, 0.5);

        // タイトルテキスト or 画像を表示する
        this.pressText = this.add.text(this.resultText.x, this.resultText.y + 100, "Press Enter");
        this.pressText.setOrigin(0.5, 0.5);

        // Enterキーの入力チェック
        this.enterKey = this.input.keyboard.addKey('Enter');
    }

    update(time, delta) {
        // Enterキーが入力されたら画面遷移する
        if (this.enterKey.isDown) {
            this.scene.start("TitleScene");
        }
    }
}