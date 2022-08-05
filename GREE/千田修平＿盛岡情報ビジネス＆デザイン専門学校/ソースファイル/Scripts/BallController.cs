using System.Collections;
using System.Collections.Generic;
using UnityEngine;

public class BallController : ObjectBase
{
    protected override void Start()
    {
        base.Start();
    }

    protected override void Update()
    {
        base.Update();

        switch (GameManager.Instance.state)
        {
            // �Q�[�����͕������Z������
            case GameState.Game:
                if (!rb.simulated && GameManager.Instance.isStart) rb.simulated = true;
                break;
            // �Q�[���O�͕������Z�������Ȃ�
            default:
                if (rb.simulated) rb.simulated = false;
                if (rb.velocity != Vector2.zero) rb.velocity = Vector2.zero;
                break;
        }
    }


    private void OnCollisionEnter2D(Collision2D collision)
    {
        if (GameManager.Instance.state != GameState.Game) return;

        audioSource.PlayOneShot(boundSE);

        // �ڒn������~�X����
        if (collision.gameObject.layer == 10)
        {
            rb.velocity = Vector2.zero;
            GameManager.Instance.isMiss = true;
        }
    }
}
