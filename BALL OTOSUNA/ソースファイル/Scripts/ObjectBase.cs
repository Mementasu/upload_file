using System.Collections;
using System.Collections.Generic;
using UnityEngine;

public class ObjectBase : MonoBehaviour
{
    [SerializeField] protected Rigidbody2D rb;
    [SerializeField] protected AudioSource audioSource;
    [SerializeField] protected AudioClip   boundSE;
    protected float moveLimit;

    protected virtual void Start()
    {
        // �ړ��\�͈͂�ݒ�
        moveLimit = GameManager.Instance.moveLimitX;

    }

    protected virtual void Update()
    {
        // ���E�̉�ʊO�ɏo�����ɂȂ������A���Α��ɕ����]��
        if (transform.position.x < moveLimit ||
            transform.position.x > -moveLimit)
        {
            if (transform.position.x < moveLimit)  transform.position = new Vector2(transform.position.x + 0.1f, transform.position.y);
            if (transform.position.x > -moveLimit) transform.position = new Vector2(transform.position.x - 0.1f, transform.position.y);
            rb.velocity = new Vector2(-rb.velocity.x, rb.velocity.y);
        }
    }

}
