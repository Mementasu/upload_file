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
        // ˆÚ“®‰Â”\”ÍˆÍ‚ğİ’è
        moveLimit = new Vector2(GameManager.Instance.moveLimitX, GameManager.Instance.moveLimitY);
    }

    private void Update()
    {
        if (GameManager.Instance.isStart)
        {
            if (cntSpawnTime < 0)
            {
                // ¶¬‚·‚éÀ•W‚ğİ’è
                var spawnY   = moveLimit.y;
                var spawnX   = Random.Range(-moveLimit.x, moveLimit.x);
                spawnPos     = new Vector2(spawnX, spawnY);

                Instantiate(star, spawnPos, Quaternion.identity);

                // Ÿ‚Ì¶¬ŠÔ‚ğƒ‰ƒ“ƒ_ƒ€‚Éİ’è
                cntSpawnTime = Random.Range(minSpawnTime, maxSpawnTime);
            }
            else
            {
                cntSpawnTime -= Time.deltaTime;
            }
        }
        
    }
}
