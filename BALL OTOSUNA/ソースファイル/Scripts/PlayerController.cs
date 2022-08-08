using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.InputSystem;

public class PlayerController : MonoBehaviour
{
    // 移動可能範囲
    private float moveLimit;

    [Header("スプライト・アニメーション")]
    [SerializeField] private SpriteRenderer sprite;
    [SerializeField] private Animator       animator;

    [Header("ステータス")]
    private bool isStan;                            // 行動不能か
    [SerializeField] private Rigidbody2D rb;
    [SerializeField] private float       moveSpeed; // 移動スピード
    [SerializeField] private float       jumpPower; // ジャンプ力
    [SerializeField] private bool        onGround;  // 接地判定

    [Header("入力")]
    [SerializeField] private PlayerInput playerInput;
    [SerializeField] private InputAction moveAction;
    [SerializeField] private InputAction jumpAction;
    [SerializeField] private InputAction dashAction;

    [SerializeField] private string moveActionName = "Move";
    [SerializeField] private string jumpActionName = "Jump";
    [SerializeField] private string dashActionName = "Dash";

    [Header("効果音")]
    [SerializeField] private AudioSource audioSource;
    [SerializeField] private AudioClip   jumpSE;


    private void Start()
    {
        // 移動可能範囲を設定
        moveLimit = GameManager.Instance.moveLimitX;

        moveAction = playerInput.actions.FindAction(moveActionName);
        jumpAction = playerInput.actions.FindAction(jumpActionName);
        dashAction = playerInput.actions.FindAction(dashActionName);

        jumpAction.started += OnJump;
    }

    private void Update()
    {
        OnGravity();

        if (isStan) return;
        OnMove();
    }

    /// <summary>
    /// 移動処理
    /// </summary>
    private void OnMove()
    {
        if (!GameManager.Instance.isStart)
        {
            if (animator.GetFloat("Speed") != 0) animator.SetFloat("Speed", 0);
            return;
        }

        var inputDir  = moveAction.ReadValue<Vector2>().x;
        var dashSpeed = dashAction.IsPressed() ? 1.5f : 1f;
        var move      = moveSpeed * dashSpeed * inputDir;

        rb.velocity   = new Vector3(move, rb.velocity.y, 0);

        // 画面外に出ないようにする
        if (transform.position.x <=  moveLimit) transform.position = new Vector2( moveLimit, transform.position.y);
        if (transform.position.x >= -moveLimit) transform.position = new Vector2(-moveLimit, transform.position.y);

        animator.SetFloat("Speed", Mathf.Abs(move));

        ChangeDirection((int)inputDir);
    }

    /// <summary>
    /// ジャンプ処理
    /// </summary>
    /// <param name="context"></param>
    private void OnJump(InputAction.CallbackContext context)
    {
        if (!GameManager.Instance.isStart || isStan || !onGround) return;

        rb.AddForce(jumpPower * transform.up, ForceMode2D.Impulse);
        audioSource.PlayOneShot(jumpSE);
    }

    /// <summary>
    /// 落下処理
    /// </summary>
    private void OnGravity()
    {
        var fallSpeed = !onGround ? rb.velocity.y : 0;
        animator.SetFloat("Vertical", fallSpeed);
    }

    /// <summary>
    /// 方向転換処理
    /// </summary>
    /// <param name="_dir"></param>
    private void ChangeDirection(int _dir)
    {
        if (!GameManager.Instance.isStart || _dir == 0) return;

        transform.localScale = new Vector2(_dir, 1);
    }

    private void OnCollisionEnter2D(Collision2D collision)
    {
        // 設置判定
        if (collision.gameObject.layer == 10)
        {
            onGround = true;
            animator.SetBool("OnGround", onGround);
        }

        // 星（障害物）との衝突判定
        if (collision.gameObject.tag == "Obstacle") StartCoroutine(Stan());
    }

    private void OnCollisionExit2D(Collision2D collision)
    {
        // 設置判定
        if (collision.gameObject.layer == 10)
        {
            onGround = false;
            animator.SetBool("OnGround", onGround);
        }
    }

    /// <summary>
    /// スタン（ダメージ）処理
    /// </summary>
    /// <returns></returns>
    private IEnumerator Stan()
    {
        isStan = true;
        animator.SetTrigger("Hit");

        yield return new WaitForSeconds(0.4f);

        isStan = false;

        StopCoroutine(Stan());
    } 
}
