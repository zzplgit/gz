<?php
namespace api\modules\v1\models;

use common\models\User;
use Faker\Provider\Uuid;
use common\models\UploadForm;

class Teacher extends Common{
    
    const ROLE_CODE = 200;
    const JOB_STATUS_DELETED = 0;
    const JOB_STATUS_ACTIVE = 10;
    
    public $name;
    public $tel;
    public $password;
    public $class_code;
    public $job_status;
    
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
            ['class_code', 'required', 'message' => '参数错误：缺少必要参数class_code'],
            ['password', 'required', 'message' => '密码不能为空'],
            ['password', 'string', 'min' => 6, 'message' => '密码不能少于6位'],
            ['job_status', 'trim'],
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
        
        if(\Yii::$app->user->isGuest){
            $this->setRespones("ERROR_CODE_IS_WRONG");
            return null;
        }

        $classCode = SchoolClass::findByCode($this->class_code);
        if(!$classCode){
            $this->setRespones("ERROR_PARAMS", [], "参数错误：班级信息不存在或已删除");
            return null;
        }
        
        if(!$this->verifyRole($this->class_code, $classCode->school_code)){
            return null;
        }
        
        //用户注册
        $user = new User();
        $user->uuid = Uuid::uuid();
        $user->username = $this->tel;
        $user->tel = $this->tel;
        $user->name = $this->name;
        $user->school_code = \Yii::$app->user->identity->school_code;
        $user->class_code = $this->class_code;
        $user->email = "";
        $user->role = self::ROLE_CODE;
        
        $user->status = (intval($this->job_status) === self::JOB_STATUS_DELETED) ? self::JOB_STATUS_DELETED : self::JOB_STATUS_ACTIVE;
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
     * 
     * @return NULL|\yii\web\IdentityInterface
     */
    public function edit(){
        $data = \Yii::$app->request->post();
        if(empty($data['id'])){
            $this->setRespones("ERROR_PARAMS", [], "参数错误：缺少必要参数id");
            return null;
        }
        $user = User::findIdentity($data['id']);
        if(!$user){
            $this->setRespones("ERROR_PARAMS", [], "教师信息不存在或已删除");
            return null;
        }
        if(!$this->verifyRole($user->class_code, $user->school_code)){
            return null;
        }
        
        if(!empty($data['name'])){
            $user->name = $data['name'];
        }
        if(!empty($data['password'])){
            $user->setPassword($data['password']);
        }
        if(isset($data['job_status']) && $data['job_status'] != ''){
            $user->status = ($data['job_status']!=self::JOB_STATUS_DELETED) ? self::JOB_STATUS_ACTIVE : self::JOB_STATUS_DELETED;
        }
        
        if($user->save()){
            $this->setRespones("ERROR_OK");
            return $user;
        }
        $this->setRespones("ERROR_SYSTEM_SAVE", [], $user->getFirstErrors());
        return null;
    }
    
    /**
          * 保存头像
     * @return NULL|\yii\web\IdentityInterface
     */
    public function editAvatar(){
        $data = \Yii::$app->request->post();
        if(empty($data['id'])){
            $this->setRespones("ERROR_PARAMS", [], "参数错误：缺少必要参数id");
            return null;
        }
        $user = User::findIdentity($data['id']);
        if(!$user){
            $this->setRespones("ERROR_PARAMS", [], "教师信息不存在或已删除");
            return null;
        }
        if(!$this->verifyRole($user->class_code, $user->school_code)){
            return null;
        }
        if(empty($data['image'])){
            $this->setRespones("ERROR_PARAMS", [], "参数错误：缺少必要参数image");
            return null;
        }
        
        $formModel = new UploadForm();
        if(!$formModel->saveBase64($data['image'], 'avatar')){
            $this->setRespones("ERROR_PARAMS", [], "图片上传失败");
            return null;
        }
        $user->avatar = $formModel->file_path;
        if($user->save()){
            $this->setRespones("ERROR_OK");
            return $user;
        }
        $this->setRespones("ERROR_SYSTEM_SAVE", [], $user->getFirstErrors());
        return null;
    }

    /**
     * 
     * @param string $classCode
     * @param string $schoolCode
     * @return boolean
     */
    private function verifyRole($classCode, $schoolCode){
        switch (\Yii::$app->user->identity->role) {
            case Common::TEACHER_USER:
                if(\Yii::$app->user->identity->class_code != $classCode){
                    $this->setRespones("ERROR_USER_ROLE", [], "只能添加自己班级下的教师");
                    return false;
                }
                break;
            case Common::MASTER_USER:
                if(\Yii::$app->user->identity->school_code != $schoolCode){
                    $this->setRespones("ERROR_USER_ROLE", [], "教师所在班级不属于您的园所");
                    return false;
                }
                break;
            default:
                $this->setRespones("ERROR_USER_ROLE");
                return false;
                break;
        }
        return true;
    }
    
    /**
     * 
     * @return NULL
     */
    public function getList(){
        if(\Yii::$app->user->isGuest){
            $this->setRespones("ERROR_CODE_IS_WRONG");
            return null;
        }
        $classCode = \Yii::$app->user->identity->class_code;
        if(in_array(\Yii::$app->user->identity->role, Common::MASTER_ROLE)){
            $classCode = \Yii::$app->request->post("class_code");
            if(!$classCode){
                $this->setRespones("ERROR_PARAMS", [], "缺少必要参数class_code");
                return null;
            }
        }
        
        $data = [];
        $model = User::find()->where(['class_code'=>$classCode, 'status'=>User::STATUS_ACTIVE])->all();
        if($model){
            foreach ($model as $value){
                $data[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                    'tel' => $value->tel,
                    'avatar' => empty($value->avatar) ? '' : \Yii::$app->params['cdnHost'].$value->avatar
                ];
            }
        }
        $this->setRespones("ERROR_OK", $data);
    }
    
    /**
     * @return NULL
     */
    public function getInfo(){
        $id = \Yii::$app->request->post('id');
        if(empty($id)){
            $this->setRespones("ERROR_PARAMS", [], "参数错误：缺少必要参数id");
            return null;
        }
        
        $model = User::findIdentityTeacher($id);
        if(!$model){
            $this->setRespones("ERROR_DATA");
            return null;
        }
        
        if(in_array(\Yii::$app->user->identity->role, Common::TEACHER_ROLE) && $model->class_code != \Yii::$app->user->identity->class_code){
            $this->setRespones("ERROR_USER_ROLE");
            return null;
        }
        
        if(in_array(\Yii::$app->user->identity->role, Common::MASTER_ROLE) && $model->school_code != \Yii::$app->user->identity->school_code){
            $this->setRespones("ERROR_USER_ROLE");
            return null;
        }
        
        $data = [
            'id' => $model->id,
            'name' => $model->name,
            'tel' => $model->tel,
            'avatar' => empty($model->avatar) ? '' : \Yii::$app->params['cdnHost'].$model->avatar,
            'school_name' => $model->school ? $model->school->school_name : '',
            'class_name' => $model->class ? $model->class->class_name : '',
        ];
        $this->setRespones("ERROR_OK", $data);
    }
    
    
}

