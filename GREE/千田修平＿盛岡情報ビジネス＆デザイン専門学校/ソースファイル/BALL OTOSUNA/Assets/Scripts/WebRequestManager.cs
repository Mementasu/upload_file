using System.Collections;
using UnityEngine;
using UnityEngine.Networking;
using System;
using LitJson;
using System.Text;
using TMPro;
using Cysharp.Threading.Tasks;

public struct ScoreData
{
    public int id;
    public string name;
    public int mode;
    public int score;
}


public static class WebRequestManager
{
    private const string REQUEST_URL_BASE = "https://web-network.sakura.ne.jp/games2021/4203110/api";

    public static string REQUEST_URL_ADD_SCORE     => $"{REQUEST_URL_BASE}/add_score.php";
    public static string REQUEST_URL_GET_TOP_SCORE => $"{REQUEST_URL_BASE}/get_top_score.php";

    private const string BASIC_USER_NAME = "student";
    private const string BASIC_PASSWORD  = "jyobi0200021";

    /// <summary>
    /// スコア送信処理
    /// </summary>
    /// <param name="scoreData"></param>
    /// <param name="successCallback"></param>
    /// <param name="errorCallback"></param>
    public static void SendScore(ScoreData scoreData, Action<string> successCallback = null, Action<string> errorCallback = null)
    {
        // 往診データ
        WWWForm formData = new();
        formData.AddField("name", scoreData.name);
        formData.AddField("mode", scoreData.mode);
        formData.AddField("score", scoreData.score);

        // サーバへのリクエスト作成と送信
        UnityWebRequest request = UnityWebRequest.Post(REQUEST_URL_ADD_SCORE, formData);
        request.SendWebRequest(
            successCallback,
            errorCallback
            );
    }

    /// <summary>
    /// ランキング取得処理
    /// </summary>
    /// <param name="successCallback"></param>
    /// <param name="errorCallback"></param>
    public static void GetRanking(Action<string> successCallback, Action<string> errorCallback)
    {
        // 実行するWebRequest
        UnityWebRequest request = UnityWebRequest.Get(REQUEST_URL_GET_TOP_SCORE);
        request.SendWebRequest(
            successCallback,
            errorCallback
            );
    }

    /// <summary>
    /// リクエスト送信用メソッド
    /// </summary>
    /// <param name="request"></param>
    /// <param name="successCallback"></param>
    /// <param name="errorCallback"></param>
    private static async void SendWebRequest(this UnityWebRequest request, Action<string> successCallback, Action<string> errorCallback)
    {
        // ベーシック認証用パラメータセット
        request.SetAuthPram();

        // 通信が終了するまで待機する
        var op = await request.SendWebRequest().ToUniTask(
            Progress.Create<float>(x =>
            {
                Debug.Log($"Sending..{x * 100}%");
            }));

        var result = op.downloadHandler.text;

        // 通信結果の判定
        if (request.result == UnityWebRequest.Result.ProtocolError ||
            request.result == UnityWebRequest.Result.ConnectionError)
        {
            errorCallback?.Invoke(result);
        }
        else
        {
            successCallback?.Invoke(result);
        }
    }

    private static void SetAuthPram(this UnityWebRequest request)
    {
        // ユーザIDとパスワードをバイト配列に変換
        string auth = $"{BASIC_USER_NAME}:{BASIC_PASSWORD}";
        var b = Encoding.GetEncoding("ISO-8859-1").GetBytes(auth);

        // バイト配列をWebで使用できるBase64に変換
        auth = Convert.ToBase64String(b);
        auth = $"Basic {auth}";
        request.SetRequestHeader("AUTHORIZATION", auth);
    }
}
