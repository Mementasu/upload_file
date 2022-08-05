using UnityEngine;

public class CursorManager : MonoBehaviour
{
    [SerializeField] private GameObject cursor;
    [SerializeField] private GameObject ball;

    private void Update()
    {
        // �{�[������̉�ʊO�ɏo�����A�J�[�\����\��
        if (ball.transform.position.y > GameManager.Instance.moveLimitY)
        {
            if (!cursor.activeSelf) cursor.SetActive(true);

            cursor.transform.position = new Vector2(ball.transform.position.x, cursor.transform.position.y);
        }
        else if (cursor.activeSelf)
        {
            cursor.SetActive(false);
        }
    }
}
