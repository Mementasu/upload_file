<?php
//継承元となるTableBaseクラスの読み込み
require_once('table-base.php');

//　Userテーブルに対するDB処理をまとめたクラス
class User extends TableBase
{
    //カラム
    public $id = 'id';
    public $point = 'point';
    public $email = 'email';
    public $password = 'password';
    public $name = 'name';
    public $street_number = "street_number";
    public $streetAdress1 = 'street_address1';
    public $streetAdress2 = 'street_address2';
    public $streetAdress3 = 'street_address3';
    public $phoneNumber = 'phone_number';
    public $urlToken = 'url_token';
    public $tokenCreateTime = 'token_create_time';
    public $pinCode = "pinCode";
    public $pincode_create_time = "pincode_create_time";
    public $appy_time = "appy_time";
    public $status = 'status';
    public $lastLoginTime = 'last_login_time';
    public $updateTime = 'update_time';
    public $createTime = 'create_time';
    public $score_easy = "score_easy";
    public $score_hard = "score_hard";
    public $gift_number = "gift_number";
    public $gift_status = "gift_status";
    public $select_status = "select_status";

    //コンストラクター
    public function __construct()
    {
        // 親のコンストラクタ呼び出し
        parent::__construct();

        //継承元のTableBaseの値をオーバーライド(上書き)する
        $this->tableName = '`2021_team-b_user`';
        $this->primarykey = $this->id; //主キー指定
    }
    //ログインできるかチェックし、できる場合はIDとNameを返し、できない場合はfalseを返す
    public function canLogin($email, $password)
    {

        //パスワードを複合負荷な256bitのハッシュに変換
        $password_sha256 =  hash('sha256', $password);

        try {

            // 本登録したユーザーの中からメールアドレスとパスワードが一致したユーザー情報を取得
            $sql = "SELECT {$this->id},{$this->name} FROM {$this->tableName} WHERE {$this->email} = :{$this->email} AND {$this->password} = :{$this->password} AND {$this->status} = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":{$this->email}", $email, PDO::PARAM_STR);
            $stmt->bindValue(":{$this->password}", $password_sha256, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->rowCount() == 1 ?
                $stmt->fetch(PDO::FETCH_ASSOC)
                : false;
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
        return false;
    }
}
