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
            // ƒQ[ƒ€’†‚Í•¨—‰‰Z‚ª“­‚­
            case GameState.Game:
                if (!rb.simulated && GameManager.Instance.isStart) rb.simulated = true;
                break;
            // ƒQ[ƒ€ŠO‚Í•¨—‰‰Z‚ª“­‚©‚È‚¢
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

        // Ú’n‚µ‚½‚çƒ~ƒX”»’è
        if (collision.gameObject.layer == 10)
        {
            rb.velocity = Vector2.zero;
            GameManager.Instance.isMiss = true;
        }
    }
}
