<?php
// DBの設定値呼び出し
require_once('db/config.php');

//  全てのテーブルクラスの継承元（スーパークラス）
class TableBase
{
    // ↓全テーブル共通の値
    public $primarykey = "";
    public $tableName = "";
    public $pdo;
    //　↓全テーブル共通の関数
    //第一引数と第二引数が合致した主キーのデータを返す
    //第二引数は指定しない場合は強制で * になる

    public function __construct()
    {
        //現在のドメイン名に合わせて鯖情報を切り替える
        $config = array();
        if (
            $_SERVER['SERVER_NAME'] === 'localhost' ||
            $_SERVER['SERVER_NAME'] === '127.0.0.1'
        ) {
            $config = DEVELOPMENT_CONFIG;
        } else {
            $config = PRODUCTION_CONFIG;
        }

        try {
            // DB接続
            $dns = "mysql:dbname={$config['database']};host={$config['host']}";
            $this->pdo = new PDO($dns, $config['user'], $config['password']);
            // エラーモード設定
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // ネイティブのプリペアドステートメントを使用
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function getData($id, $colmon = "*")
    {
        try {

            $sql = "SELECT {$colmon} FROM {$this->tableName} WHERE {$this->primarykey}={$id}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetch(); //結果を返す

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    public function getAndData(array $where,array $setcolmon)
    {
        try {

            $setString = "";
            $i = 0;
            $length = count($setcolmon);
            foreach ($setcolmon as $key => $value) {
                $setString .= "{$key}={$value}";
                if (++$i < $length) {
                    $setString .= ",";
                }
            }

            $setwhere = "";
            $i = 0;
            $length = count($where);
            foreach ($where as $key => $value) {
                $setwhere.= "{$key}={$value}";
                if (++$i < $length) {
                    $setwhere .= "AND";
                }
            }


            $sql = "SELECT {$setString} FROM {$this->tableName} WHERE {$setwhere}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetch(); //結果を返す

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    //第一引数で指定・・・id
    //第二引数で指定・・・アップデートしたいカラム
    public function updateData($id, array $setValues)
    {
        try {

            $setString = "";
            $i = 0;
            $length = count($setValues);
            foreach ($setValues as $key => $value) {
                $setString .= "{$key}={$value}";
                if (++$i < $length) {
                    $setString .= ",";
                }
            }

            $sql = "UPDATE {$this->tableName} SET {$setString} WHERE {$this->primarykey}={$id}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return true; //結果を返す

        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
}
