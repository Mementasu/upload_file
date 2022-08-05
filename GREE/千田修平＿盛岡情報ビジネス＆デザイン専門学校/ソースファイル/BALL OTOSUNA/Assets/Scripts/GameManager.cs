using System;
using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;
using UnityEngine.InputSystem;
using TMPro;
using LitJson;
using System.Text;

public enum GameState
{
    Title,
    Game,
    Result,
}

public class GameManager : Singleton<GameManager>
{
    public GameState state;                 // ゲームの状況
    public bool      isStart;               // 開始したか
    public bool      isMiss;                // ミスになったか
    public bool      isStartTitleCoroutine; // Titleコルーチンが開始したか

    [SerializeField] private List<int> rankingDatas = new List<int>();

    public float moveLimitX { get; private set; } // 画面左下の座標
    public float moveLimitY { get; private set; } // 画面右上の座標

    [Header("UI")]
    [SerializeField]
    private GameObject titleUI;
    [SerializeField]
    private GameObject gameUI;
    [SerializeField]
    private GameObject resultUI;

    [Header("オーディオ")]
    [SerializeField]
    private AudioSource audioSource;
    [SerializeField]
    private AudioClip   cntSE;
    [SerializeField]
    private AudioClip   startSE;
    [SerializeField]
    private AudioClip   finishSE;

    [Header("ゲームオブジェクト")]
    [SerializeField]
    private GameObject player;
    [SerializeField]
    private GameObject ball;

    [Header("stateがTitleの時")]
    [SerializeField] private GameObject howtoplayPanel;

    [Header("stateがGameの時")]
    [SerializeField] private int countdownVal = 3;
    [SerializeField] private float timer;
    [SerializeField] private int   timerDigit = 2;
    [SerializeField] private int   sendTimer;
    [SerializeField] private TMP_Text countdownText;
    [SerializeField] private TMP_Text gameTimerText;

    [Header("stateがResultの時")]
    [SerializeField] private TMP_Text       resultTimerText;
    [SerializeField] private TMP_Text       resultRankingText;
    [SerializeField] private GameObject     resultRankingPanel;
    [SerializeField] private GameObject     resultHighScorePanel;
    [SerializeField] private TMP_InputField resultNameText;

    protected override void Awake()
    {
        // 画面左右のワールド座標をビューポートから取得
        moveLimitX = Camera.main.ViewportToWorldPoint(Vector2.zero).x;
        moveLimitY = Camera.main.ViewportToWorldPoint(Vector2.one).y;
    }

    // Start is called before the first frame update
    void Start()
    {
        isStartTitleCoroutine = false;
        ChangeState(GameState.Title);
    }

    // Update is called once per frame
    void Update()
    {
        switch(state)
        {
            case GameState.Title:
                if (!isStartTitleCoroutine)
                {
                    isStartTitleCoroutine = true;
                    StartCoroutine(Title());
                }

                if (Keyboard.current.shiftKey.wasPressedThisFrame)
                {
                    // 遊び方のUIを表示
                    howtoplayPanel.SetActive(!howtoplayPanel.activeSelf);
                }

                break;
            case GameState.Game:
                if (!isStart) return;

                UpdateTimer();
                if (isMiss) StartCoroutine(Finish());

                break;
            case GameState.Result:
                break;
        }
    }

    /// <summary>
    /// プレイヤーとボールの座標をリセット
    /// </summary>
    private void ResetPosition()
    {
        ball.transform.position     = new Vector2(0f, 5f);
        player.transform.position   = new Vector2(0f, -4.75f);
        player.transform.localScale = new Vector2(1, 1);
    }

    /// <summary>
    /// タイトル画面
    /// </summary>
    /// <returns></returns>
    private IEnumerator Title()
    {
        ChangeState(GameState.Title);
        isStart = false;

        if (howtoplayPanel.activeSelf) howtoplayPanel.SetActive(false);

        yield return new WaitUntil(() => Keyboard.current.spaceKey.wasPressedThisFrame);

        
        StartCoroutine(Countdown());

        StopCoroutine(Title());
    }

    /// <summary>
    /// ゲーム画面・開始カウントダウン
    /// </summary>
    /// <returns></returns>
    private IEnumerator Countdown()
    {
        ChangeState(GameState.Game);

        ResetTimer();

        countdownText.text = "READY";

        for (int i = 0; i < countdownVal; i++)
        {
            countdownText.text += ".";
            audioSource.PlayOneShot(cntSE);
            yield return new WaitForSeconds(1);
        }

        countdownText.text = "GO";
        audioSource.PlayOneShot(startSE);

        var waitTime = 1f;
        yield return new WaitForSeconds(waitTime);

        countdownText.enabled = false;
        isStart               = true;

        StopCoroutine(Countdown());
    }

    /// <summary>
    /// ゲーム画面・ゲーム終了
    /// </summary>
    /// <returns></returns>
    private IEnumerator Finish()
    {
        isStart = false;

        countdownText.enabled = true;
        countdownText.text = "FINISH";
        audioSource.PlayOneShot(finishSE);

        rankingDatas.Clear();

        // ランキングから上位5名のデータを取得
        OnRanking();

        var waitTime = 1f;
        yield return new WaitForSeconds(waitTime);

        StartCoroutine(Result());

        StopCoroutine(Finish());
    }

    /// <summary>
    /// リザルト画面
    /// </summary>
    /// <returns></returns>
    private IEnumerator Result()
    {
        ChangeState(GameState.Result);

        resultTimerText.text     = gameTimerText.text;
        resultRankingPanel.SetActive(false);

        var rank            = 0;
        var rankiLine       = 5;
        resultNameText.text = "";

        // ランキング配列に今回のスコアを挿入
        rankingDatas.Add(sendTimer);

        // ランキング配列をスコアが大きい順に並び変え
        for (int i = 0; i < rankingDatas.Count; i++)
        {
            var tmp = 0;
            for (int j = 0; j < rankingDatas.Count; j++)
            {
                if (rankingDatas[i] > rankingDatas[j])
                {
                    tmp        = rankingDatas[i];
                    rankingDatas[i] = rankingDatas[j];
                    rankingDatas[j] = tmp;
                }
            }
        }

        // 今回の記録の順位を取得
        for (int k = 0; k < rankingDatas.Count; k++)
        {
            if (rankingDatas[k] == sendTimer)
            {
                rank = k;
                break;
            }
        }

        // 今回のスコアがrankLineより大きければ名前入力欄を表示、データを送信
        if (rank < rankiLine)
        {
            resultHighScorePanel.SetActive(true);

            yield return new WaitUntil(() => resultNameText.text != "" && Keyboard.current.enterKey.wasPressedThisFrame);

            OnSend(resultNameText.text);

            var waitSendTime = 1f;
            yield return new WaitForSeconds(waitSendTime);

            OnRanking();
        }
        
        if (resultHighScorePanel.activeSelf) resultHighScorePanel.SetActive(false);

        resultRankingPanel.SetActive(true);

        isMiss = false;

        yield return new WaitUntil(() => 
            Keyboard.current.spaceKey.wasPressedThisFrame || 
            Keyboard.current.escapeKey.wasPressedThisFrame
        );

        isStartTitleCoroutine = false;

        if      (Keyboard.current.spaceKey.wasPressedThisFrame)  StartCoroutine(Countdown());
        else if (Keyboard.current.escapeKey.wasPressedThisFrame) StartCoroutine(Title());

        StopCoroutine(Result());
    }

    /// <summary>
    /// ゲームの状況を設定
    /// </summary>
    /// <param name="_state"></param>
    private void ChangeState(GameState _state)
    {
        switch (_state)
        {
            case GameState.Title:
                titleUI.SetActive(true);
                gameUI.SetActive(false);
                resultUI.SetActive(false);
                break;
            case GameState.Game:
                ResetPosition();

                titleUI.SetActive(false);
                gameUI.SetActive(true);
                resultUI.SetActive(false);
                break;
            case GameState.Result:
                titleUI.SetActive(false);
                gameUI.SetActive(false);
                resultUI.SetActive(true);
                break;
        }

        state = _state;
    }

    /// <summary>
    /// ゲーム画面・タイマー
    /// </summary>
    private void UpdateTimer()
    {
        timer += Time.deltaTime * Mathf.Pow(10, timerDigit);
        sendTimer = (int)timer;
        gameTimerText.text = (timer * Mathf.Pow(10, -timerDigit)).ToString("N2");
    }

    /// <summary>
    /// ゲーム画面・タイマー初期化
    /// </summary>
    private void ResetTimer()
    {
        timer = 0;
        gameTimerText.text = "0";
    }

    /// <summary>
    /// 送信処理
    /// </summary>
    public void OnSend(string _name)
    {
        ScoreData sendData = new ()
        {
            name = _name,
            mode = 1, // ゲームモード
            score = sendTimer,
        };

        WebRequestManager.SendScore(
            sendData,
            (string result) => Debug.Log("送信完了"),
            (string result) => Debug.LogError($"Error: {result}")
        );

    }

    /// <summary>
    /// ランキング取得処理
    /// </summary>
    public void OnRanking()
    {
        WebRequestManager.GetRanking(
            (string result) =>
            {
                if (!result.Equals("null"))
                {
                    // Jsonのパース
                    JsonData jsonDatas = JsonMapper.ToObject(result);

                    StringBuilder builder = new();

                    for (int i = 0; i < jsonDatas.Count; i++)
                    {
                        try
                        {
                            JsonReader jsonData = new(jsonDatas[i].ToJson()); // Jsonデータを1件ずつ取得する
                            ScoreData data = JsonMapper.ToObject<ScoreData>(jsonData); // jsonの構造と全く同じ構造体に代入できる
                            var scoreStr = data.score.ToString();

                            // StringBuilderによる文字列の作成
                            builder.Append($"{i + 1} : {data.name} ");
                            builder.Append("\n");
                            builder.Append($"{scoreStr.Insert(scoreStr.Length - 2, ".")} seconds");
                            builder.Append("\n\n"); 

                            rankingDatas.Add(data.score);
                        }
                        catch (Exception e)
                        {
                            Debug.LogError(e);

                            builder.Append($"Error Data");
                            builder.Append("\n"); 
                        }
                    }
                    resultRankingText.text = builder.ToString();
                }

                
            },
            (string result) => Debug.LogError($"Error: {result}")
        );
    }

}
