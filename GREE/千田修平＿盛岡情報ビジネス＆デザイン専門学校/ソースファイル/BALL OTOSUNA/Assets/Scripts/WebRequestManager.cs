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
    /// �X�R�A���M����
    /// </summary>
    /// <param name="scoreData"></param>
    /// <param name="successCallback"></param>
    /// <param name="errorCallback"></param>
    public static void SendScore(ScoreData scoreData, Action<string> successCallback = null, Action<string> errorCallback = null)
    {
        // ���f�f�[�^
        WWWForm formData = new();
        formData.AddField("name", scoreData.name);
        formData.AddField("mode", scoreData.mode);
        formData.AddField("score", scoreData.score);

        // �T�[�o�ւ̃��N�G�X�g�쐬�Ƒ��M
        UnityWebRequest request = UnityWebRequest.Post(REQUEST_URL_ADD_SCORE, formData);
        request.SendWebRequest(
            successCallback,
            errorCallback
            );
    }

    /// <summary>
    /// �����L���O�擾����
    /// </summary>
    /// <param name="successCallback"></param>
    /// <param name="errorCallback"></param>
    public static void GetRanking(Action<string> successCallback, Action<string> errorCallback)
    {
        // ���s����WebRequest
        UnityWebRequest request = UnityWebRequest.Get(REQUEST_URL_GET_TOP_SCORE);
        request.SendWebRequest(
            successCallback,
            errorCallback
            );
    }

    /// <summary>
    /// ���N�G�X�g���M�p���\�b�h
    /// </summary>
    /// <param name="request"></param>
    /// <param name="successCallback"></param>
    /// <param name="errorCallback"></param>
    private static async void SendWebRequest(this UnityWebRequest request, Action<string> successCallback, Action<string> errorCallback)
    {
        // �x�[�V�b�N�F�ؗp�p�����[�^�Z�b�g
        request.SetAuthPram();

        // �ʐM���I������܂őҋ@����
        var op = await request.SendWebRequest().ToUniTask(
            Progress.Create<float>(x =>
            {
                Debug.Log($"Sending..{x * 100}%");
            }));

        var result = op.downloadHandler.text;

        // �ʐM���ʂ̔���
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
        // ���[�UID�ƃp�X���[�h���o�C�g�z��ɕϊ�
        string auth = $"{BASIC_USER_NAME}:{BASIC_PASSWORD}";
        var b = Encoding.GetEncoding("ISO-8859-1").GetBytes(auth);

        // �o�C�g�z���Web�Ŏg�p�ł���Base64�ɕϊ�
        auth = Convert.ToBase64String(b);
        auth = $"Basic {auth}";
        request.SetRequestHeader("AUTHORIZATION", auth);
    }
}
