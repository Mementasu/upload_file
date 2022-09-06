using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.InputSystem;

public class PlayerController : MonoBehaviour
{
    // �ړ��\�͈�
    private float moveLimit;

    [Header("�X�v���C�g�E�A�j���[�V����")]
    [SerializeField] private SpriteRenderer sprite;
    [SerializeField] private Animator       animator;

    [Header("�X�e�[�^�X")]
    private bool isStan;                            // �s���s�\��
    [SerializeField] private Rigidbody2D rb;
    [SerializeField] private float       moveSpeed; // �ړ��X�s�[�h
    [SerializeField] private float       jumpPower; // �W�����v��
    [SerializeField] private bool        onGround;  // �ڒn����

    [Header("����")]
    [SerializeField] private PlayerInput playerInput;
    [SerializeField] private InputAction moveAction;
    [SerializeField] private InputAction jumpAction;
    [SerializeField] private InputAction dashAction;

    [SerializeField] private string moveActionName = "Move";
    [SerializeField] private string jumpActionName = "Jump";
    [SerializeField] private string dashActionName = "Dash";

    [Header("���ʉ�")]
    [SerializeField] private AudioSource audioSource;
    [SerializeField] private AudioClip   jumpSE;


    private void Start()
    {
        // �ړ��\�͈͂�ݒ�
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
    /// �ړ�����
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

        // ��ʊO�ɏo�Ȃ��悤�ɂ���
        if (transform.position.x <=  moveLimit) transform.position = new Vector2( moveLimit, transform.position.y);
        if (transform.position.x >= -moveLimit) transform.position = new Vector2(-moveLimit, transform.position.y);

        animator.SetFloat("Speed", Mathf.Abs(move));

        ChangeDirection((int)inputDir);
    }

    /// <summary>
    /// �W�����v����
    /// </summary>
    /// <param name="context"></param>
    private void OnJump(InputAction.CallbackContext context)
    {
        if (!GameManager.Instance.isStart || isStan || !onGround) return;

        rb.AddForce(jumpPower * transform.up, ForceMode2D.Impulse);
        audioSource.PlayOneShot(jumpSE);
    }

    /// <summary>
    /// ��������
    /// </summary>
    private void OnGravity()
    {
        var fallSpeed = !onGround ? rb.velocity.y : 0;
        animator.SetFloat("Vertical", fallSpeed);
    }

    /// <summary>
    /// �����]������
    /// </summary>
    /// <param name="_dir"></param>
    private void ChangeDirection(int _dir)
    {
        if (!GameManager.Instance.isStart || _dir == 0) return;

        transform.localScale = new Vector2(_dir, 1);
    }

    private void OnCollisionEnter2D(Collision2D collision)
    {
        // �ݒu����
        if (collision.gameObject.layer == 10)
        {
            onGround = true;
            animator.SetBool("OnGround", onGround);
        }

        // ���i��Q���j�Ƃ̏Փ˔���
        if (collision.gameObject.tag == "Obstacle") StartCoroutine(Stan());
    }

    private void OnCollisionExit2D(Collision2D collision)
    {
        // �ݒu����
        if (collision.gameObject.layer == 10)
        {
            onGround = false;
            animator.SetBool("OnGround", onGround);
        }
    }

    /// <summary>
    /// �X�^���i�_���[�W�j����
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
