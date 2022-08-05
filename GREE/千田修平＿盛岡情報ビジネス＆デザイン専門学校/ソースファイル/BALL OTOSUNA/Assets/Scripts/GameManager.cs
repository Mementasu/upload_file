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
    public GameState state;                 // �Q�[���̏�
    public bool      isStart;               // �J�n������
    public bool      isMiss;                // �~�X�ɂȂ�����
    public bool      isStartTitleCoroutine; // Title�R���[�`�����J�n������

    [SerializeField] private List<int> rankingDatas = new List<int>();

    public float moveLimitX { get; private set; } // ��ʍ����̍��W
    public float moveLimitY { get; private set; } // ��ʉE��̍��W

    [Header("UI")]
    [SerializeField]
    private GameObject titleUI;
    [SerializeField]
    private GameObject gameUI;
    [SerializeField]
    private GameObject resultUI;

    [Header("�I�[�f�B�I")]
    [SerializeField]
    private AudioSource audioSource;
    [SerializeField]
    private AudioClip   cntSE;
    [SerializeField]
    private AudioClip   startSE;
    [SerializeField]
    private AudioClip   finishSE;

    [Header("�Q�[���I�u�W�F�N�g")]
    [SerializeField]
    private GameObject player;
    [SerializeField]
    private GameObject ball;

    [Header("state��Title�̎�")]
    [SerializeField] private GameObject howtoplayPanel;

    [Header("state��Game�̎�")]
    [SerializeField] private int countdownVal = 3;
    [SerializeField] private float timer;
    [SerializeField] private int   timerDigit = 2;
    [SerializeField] private int   sendTimer;
    [SerializeField] private TMP_Text countdownText;
    [SerializeField] private TMP_Text gameTimerText;

    [Header("state��Result�̎�")]
    [SerializeField] private TMP_Text       resultTimerText;
    [SerializeField] private TMP_Text       resultRankingText;
    [SerializeField] private GameObject     resultRankingPanel;
    [SerializeField] private GameObject     resultHighScorePanel;
    [SerializeField] private TMP_InputField resultNameText;

    protected override void Awake()
    {
        // ��ʍ��E�̃��[���h���W���r���[�|�[�g����擾
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
                    // �V�ѕ���UI��\��
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
    /// �v���C���[�ƃ{�[���̍��W�����Z�b�g
    /// </summary>
    private void ResetPosition()
    {
        ball.transform.position     = new Vector2(0f, 5f);
        player.transform.position   = new Vector2(0f, -4.75f);
        player.transform.localScale = new Vector2(1, 1);
    }

    /// <summary>
    /// �^�C�g�����
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
    /// �Q�[����ʁE�J�n�J�E���g�_�E��
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
    /// �Q�[����ʁE�Q�[���I��
    /// </summary>
    /// <returns></returns>
    private IEnumerator Finish()
    {
        isStart = false;

        countdownText.enabled = true;
        countdownText.text = "FINISH";
        audioSource.PlayOneShot(finishSE);

        rankingDatas.Clear();

        // �����L���O������5���̃f�[�^���擾
        OnRanking();

        var waitTime = 1f;
        yield return new WaitForSeconds(waitTime);

        StartCoroutine(Result());

        StopCoroutine(Finish());
    }

    /// <summary>
    /// ���U���g���
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

        // �����L���O�z��ɍ���̃X�R�A��}��
        rankingDatas.Add(sendTimer);

        // �����L���O�z����X�R�A���傫�����ɕ��ѕς�
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

        // ����̋L�^�̏��ʂ��擾
        for (int k = 0; k < rankingDatas.Count; k++)
        {
            if (rankingDatas[k] == sendTimer)
            {
                rank = k;
                break;
            }
        }

        // ����̃X�R�A��rankLine���傫����Ζ��O���͗���\���A�f�[�^�𑗐M
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
    /// �Q�[���̏󋵂�ݒ�
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
    /// �Q�[����ʁE�^�C�}�[
    /// </summary>
    private void UpdateTimer()
    {
        timer += Time.deltaTime * Mathf.Pow(10, timerDigit);
        sendTimer = (int)timer;
        gameTimerText.text = (timer * Mathf.Pow(10, -timerDigit)).ToString("N2");
    }

    /// <summary>
    /// �Q�[����ʁE�^�C�}�[������
    /// </summary>
    private void ResetTimer()
    {
        timer = 0;
        gameTimerText.text = "0";
    }

    /// <summary>
    /// ���M����
    /// </summary>
    public void OnSend(string _name)
    {
        ScoreData sendData = new ()
        {
            name = _name,
            mode = 1, // �Q�[�����[�h
            score = sendTimer,
        };

        WebRequestManager.SendScore(
            sendData,
            (string result) => Debug.Log("���M����"),
            (string result) => Debug.LogError($"Error: {result}")
        );

    }

    /// <summary>
    /// �����L���O�擾����
    /// </summary>
    public void OnRanking()
    {
        WebRequestManager.GetRanking(
            (string result) =>
            {
                if (!result.Equals("null"))
                {
                    // Json�̃p�[�X
                    JsonData jsonDatas = JsonMapper.ToObject(result);

                    StringBuilder builder = new();

                    for (int i = 0; i < jsonDatas.Count; i++)
                    {
                        try
                        {
                            JsonReader jsonData = new(jsonDatas[i].ToJson()); // Json�f�[�^��1�����擾����
                            ScoreData data = JsonMapper.ToObject<ScoreData>(jsonData); // json�̍\���ƑS�������\���̂ɑ���ł���
                            var scoreStr = data.score.ToString();

                            // StringBuilder�ɂ�镶����̍쐬
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
