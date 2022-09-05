let game;

// ゲーム設定
let gameOptions = {
    localStorageName: "switch_puzzlue_best_score"
}

// ページが読み込まれた時に呼ばれる
window.onload = function() {
    // Phaseの設定
    let gameConfig = {
        type: Phaser.AUTO,
        backgroundColor: 0x81caf5, // 背景カラー
        scale: {
            mode: Phaser.Scale.FIT, // ゲーム画面のスケールをページサイズにフィットさせる
            autoCenter: Phaser.Scale.CENTER_BOTH, // ゲーム画面をページの中央に合わせる
            parent: 'thegame', // thegameのidを持ったタグ直下にゲーム画面を接地する
            width: 720, // ゲーム画面の横サイズ
            height: 1136 // ゲーム画面縦サイズ
        },
        //audio: {
        //    disableWebAudio: true
        //},
        scene: [TitleScene, MainScene], // シーンクラスを全て定義
    }

    // ゲーム開始
    game = new Phaser.Game(gameConfig);
    window.focus(); // 画面をフォーカスする


}