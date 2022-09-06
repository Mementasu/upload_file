using System.Collections;
using System.Collections.Generic;
using UnityEngine;

public class StarManager : MonoBehaviour
{
    [SerializeField] GameObject star;
    private float minSpawnTime = 1;
    private float maxSpawnTime = 3;
    private float cntSpawnTime;
    private Vector2 spawnPos;
    private Vector2 moveLimit;

    private void Start()
    {
        // �ړ��\�͈͂�ݒ�
        moveLimit = new Vector2(GameManager.Instance.moveLimitX, GameManager.Instance.moveLimitY);
    }

    private void Update()
    {
        if (GameManager.Instance.isStart)
        {
            if (cntSpawnTime < 0)
            {
                // ����������W��ݒ�
                var spawnY   = moveLimit.y;
                var spawnX   = Random.Range(-moveLimit.x, moveLimit.x);
                spawnPos     = new Vector2(spawnX, spawnY);

                Instantiate(star, spawnPos, Quaternion.identity);

                // ���̐������Ԃ������_���ɐݒ�
                cntSpawnTime = Random.Range(minSpawnTime, maxSpawnTime);
            }
            else
            {
                cntSpawnTime -= Time.deltaTime;
            }
        }
        
    }
}
