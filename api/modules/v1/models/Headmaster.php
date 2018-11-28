<?php
namespace api\modules\v1\models;

use common\models\User;
use Faker\Provider\Uuid;

class Headmaster extends Common{
    
    const ROLE_CODE = 100;
    const ROLE_CODE_INITIAL = 101; //未重置过密码
    
    public $name;
    public $tel;
    public $school_name;
    public $school_tel;
    public $password;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['tel', 'trim'],
            ['tel', 'required', 'message' => '手机号不能为空'],
            ['tel', 'unique', 'targetClass' => '\common\models\User', 'message' => '手机号已经注册'],
            ['name', 'required', 'message' => '姓名不能为空'],
            ['school_name', 'required', 'message' => '院校名称不能为空'],
            ['school_tel', 'required', 'message' => '院校电话不能为空'],
            ['password', 'required', 'message' => '密码不能为空'],
            ['password', 'string', 'min' => 6, 'message' => '密码不能少于6位'],
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
            $this->setRespones("ERROR_PARAMS", [], $this->getFirstErrors());
            return null;
        }
        //添加院校
        $schoolParams = [];
        $schoolParams['School'] = [
            'school_name' => $this->school_name,
            'school_tel' => $this->school_tel
        ];
        $schoolModel = new School();
        $schoolModel->load($schoolParams);
        if (!$schoolModel->addSchool()) {
            $this->copyRespones($schoolModel->returnData);
            return null;
        }
        
        //用户注册
        $user = new User();
        $user->uuid = Uuid::uuid();
        $user->username = $this->tel;
        $user->tel = $this->tel;
        $user->name = $this->name;
        $user->school_code = $schoolModel->returnData['data']['school_code'];
        $user->email = "";
        $user->role = self::ROLE_CODE_INITIAL;
        $user->setPassword($this->password);
        //生成随机的auth_key，用于cookie登陆
        //$user->generateAuthKey();
        if($user->save()){
            $this->setRespones("ERROR_OK");
            return $user;
        }
        $this->setRespones("ERROR_SYSTEM_SAVE", [], $user->getFirstErrors());
        return null;
    }
    
    /**
     * 初始密码
     */
    public static function getInitialPwd(){
        return "iJgmRhCA1lCy746Sk1K2PA==";
    }
    
}

