<?php
namespace backend\models;

use yii\base\Model;
use common\models\User;
use Faker\Provider\Uuid;

/**
 * Login form
 */
class Headmaster extends Model
{

    const ROLE_CODE = 100;
    const ROLE_CODE_INITIAL = 101; //未重置过密码
    
    public $uid;
    public $name;
    public $tel;
    public $school_name;
    public $school_tel;
    
    private $_user;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['uid', 'trim'],
            ['tel', 'trim'],
            ['tel', 'required', 'message' => '手机号不能为空'],
            ['name', 'required', 'message' => '姓名不能为空'],
            ['school_name', 'required', 'message' => '院校名称不能为空'],
            ['school_tel', 'required', 'message' => '院校电话不能为空'],
        ];
    }
    
    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        //添加院校
        $schoolParams = [];
        $schoolParams['School'] = [
            'school_name' => $this->school_name,
            'school_tel' => $this->school_tel
        ];
        $schoolModel = new School();
        if (!$schoolModel->load($schoolParams) || !$schoolModel->addSchool()) {
            $this->addError("alert", "保存园所信息失败");
            return null;
        }
        
        //用户注册
        
        if(!empty($this->uid)){
            $user = User::findIdentity($this->uid);
            if(!$user){
                $this->addError("alert", "园长信息不存在或已经删除，请返回后重新操作.");
                return null;
            }
        }else{
            $unUser = User::findByUsername($this->tel);
            if($unUser){
                $this->addError("tel", "该电话已经注册");
                return null;
            }
            
            $pwd = self::getInitialPwd();
            $user = new User();
            $user->uuid = Uuid::uuid();
            $user->setPassword($pwd);
            $user->role = self::ROLE_CODE_INITIAL;
        }
        $user->username = $this->tel;
        $user->tel = $this->tel;
        $user->name = $this->name;
        $user->school_code = $schoolModel->school_code;
        return $user->save() ? $user : null;
    }
    
    /**
          * 初始密码
     */
    public static function getInitialPwd(){
        return "iJgmRhCA1lCy746Sk1K2PA==";
    }

    public static function getList($post){
        $list = [
            'draw' => 0, //防止跨站脚本（XSS）攻击。
            'recordsTotal' => 0,   //即没有过滤的记录数（数据库里总共记录数）
            'recordsFiltered' => 0, //过滤后的记录数
            "data" => []
        ];
        if (empty($post['draw'])) {
            return $list;
        }
        
        $start = empty($post['start']) ? 0 : $post['start'];
        $length = empty($post['length']) ? 0 : $post['length'];
        $query = (new User())->find()->where(['role'=>User::ROLE_MASTER, 'status'=>User::STATUS_ACTIVE]);
        $countUser = clone $query;
        $user = $query->limit($length)->offset($start)->all();
//         $sql=$query ->createCommand()->getRawSql();
//         var_dump($sql);die;
        $list['draw'] = $post['draw'];
        $list['recordsTotal'] = $countUser->count();
        $list['recordsFiltered'] = $countUser->count();
        foreach ($user as $value){
            $list['data'][] = [
                "id"=>$value->id,
                "tel"=>$value->tel,
                "name"=>$value->name,
                'school_tel'=>($value->school) ? $value->school->school_tel : '',
                "school_name"=>($value->school) ? $value->school->school_name : '',
                'created_at'=>date("Y-m-d H:i", $value->created_at)
            ];
        }
        return $list;
    }
    
    public function findByUid(){
        if(!$this->uid){
            return null;
        }
        $this->_user = User::findIdentity($this->uid);
        if(!$this->_user){
            return null;
        }
        $this->name = $this->_user->name;
        $this->tel = $this->_user->tel;
        $this->school_name = ($this->_user->school) ? $this->_user->school->school_name : "";
        $this->school_tel = ($this->_user->school) ? $this->_user->school->school_tel : "";
        return true;
    }
    
    public function del(){
        if(!$this->findByUid()){
            $this->addError(["alert"=>"用户不存在或已经删除"]);
            return null;
        }
        $this->_user->status = User::STATUS_DELETED;
        if(!$this->_user->save(false)){
            $this->addError(["alert"=>"删除失败"]);
            return null;
        }
        return true;
    }
    
}
