using UnityEngine;

/// <summary>
/// シングルトン基底クラス
/// </summary>
/// <typeparam name="T"></typeparam>
public abstract class Singleton<T> : MonoBehaviour where T : MonoBehaviour
{
    private static T instance;
    public static T Instance
    {
        get
        {
            if (!instance) instance = FindObjectOfType<T>();
            return instance;
        }
    }

    protected virtual void Awake()
    {
        if (!instance) instance = this as T;
    }
}
