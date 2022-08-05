/// <summary>
/// pos1から見たpos2の角度を返す(ラジアン)
/// </summary>
/// <param name="pos1"></param>
/// <param name="pos2"></param>
/// <returns></returns>
function getRadian(pos1, pos2) {
    let radian = Math.atan2(pos2.y - pos1.y, pos2.x - pos1.x);
    radian += Phaser.Math.DegToRad(90);
    return radian;
}

/// <summary>
/// pos1から見たpos2の角度を返す(度)※0=右
/// </summary>
/// <param name="pos1"></param>
/// <param name="pos2"></param>
/// <returns></returns>
function getDegree(pos1, pos2) {
    let degree = getRadian(pos1, pos2) * 180 / Math.PI;
    return degree;
}

/// <summary>
/// ラジアンに対して上方向のベクトルを返す
/// </summary>
/// <param name="node"></param>
/// <returns></returns>
function getUp(r) {
    let up = new Phaser.Math.Vector2(Math.sin(r), -Math.cos(r));
    up.normalize();
    return up;
}

/// <summary>
/// ラジアンに対して右方向のベクトルを返す
/// </summary>
/// <param name="node"></param>
/// <returns></returns>
function getRight(r) {
    r += Phaser.Math.DegToRad(90);
    let right = new Phaser.Math.Vector2(Math.sin(r), -Math.cos(r));
    right.normalize();
    return right;
}

/// <summary>
/// ラジアンに対して左方向のベクトルを返す
/// </summary>
/// <param name="node"></param>
/// <returns></returns>
function getLeft(r) {
    let right = getRight(r);
    right.x *= -1;
    return right;
}

/// <summary>
/// ラジアンに対して下方向のベクトルを返す
/// </summary>
/// <param name="node"></param>
/// <returns></returns>
function getDown(r) {
    let up = getUp(r);
    up.y *= -1;
    return up;
}

/// <summary>
/// RGB([255,0,0])をHEXへ変換
/// </summary>
/// <param name="node"></param>
/// <returns></returns>
function rgbToHex(rgb) {
    return "0x" + rgb.map(function(value) {
        return ("0" + value.toString(16)).slice(-2);
    }).join("");
}

/// <summary>
/// tを0~lengthの間で往復するよう補完する
/// </summary>
/// <param name="t">値</param>
/// <param name="length">最大値</param>
/// <returns>0~最大値間の往復値</returns>
function pingPong(t, length) {
    let l = length * 2;
    let r = t % l;
    return (length < r) ? l - r : r;
}