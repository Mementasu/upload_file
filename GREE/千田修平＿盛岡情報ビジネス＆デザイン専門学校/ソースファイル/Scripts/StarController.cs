using UnityEngine;

public class StarController : ObjectBase
{
    [SerializeField] private AudioClip      damageSE;
    [SerializeField] private SpriteRenderer sprite;

    private bool  isBound;
    private float moveRange = 5;

    protected override void Start()
    {
        base.Start();

        var setMoveDir = Random.Range(-moveRange, moveRange);
        rb.velocity    = new Vector2(setMoveDir , rb.velocity.y);
        isBound        = false;
    }

    protected override void Update()
    {
        base.Update();

        if (GameManager.Instance.state != GameState.Game) Destroy(gameObject);
    }

    private void OnCollisionEnter2D(Collision2D collision)
    {
        if (collision.gameObject.tag == "Player") audioSource.PlayOneShot(damageSE);
        else audioSource.PlayOneShot(boundSE);

        // ínñ Ç…2âÒìñÇΩÇ¡ÇΩÇÁè¡Ç¶ÇÈ
        if (collision.gameObject.layer == 10)
        {
            if (!isBound)
            {
                isBound      = true;
                sprite.color = new Color(0.5f, 0.5f, 0.5f);
            }
            else Destroy(gameObject);
        }
    }
}
