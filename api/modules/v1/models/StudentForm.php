<?php
namespace api\modules\v1\models;


use common\models\Student;
use common\models\UploadForm;

class StudentForm extends Common{
    

    
    public $id;
    public $class_code;
    public $name;
    public $sex_code;
    public $birthday;
    public $f_structure_code;
    public $ext_student_id;
    public $ext_region_id;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', "trim"],
            ['class_code', "trim"],
            ['name', 'required', 'message' => '学生姓名不能为空'],
            ['sex_code', 'required', 'message' => '学生性别不能为空'],
            ['birthday', 'required', 'message' => '生日不能为空'],
            ['f_structure_code', "trim"],
            ['ext_student_id', 'required', 'message' => '学生ID不能为空'],
            ['ext_region_id', "trim"],
        ];
    }
    
    
    public function edit(){
        if (!$this->validate()) {
            $this->setRespones("ERROR_PARAMS", [], $this->getFirstErrors());
            return null;
        }
        if(\Yii::$app->user->isGuest || !in_array(\Yii::$app->user->identity->role, Common::MASTER_TEACHER_ROLE)){
            $this->setRespones("ERROR_USER_ROLE");
            return null;
        }
        
        $classCode = \Yii::$app->user->identity->class_code ? \Yii::$app->user->identity->class_code : $this->class_code;
        if(empty($classCode)){
            $this->setRespones("ERROR_PARAMS", [], "参数错误：缺少必要参数class_code");
            return null;
        }
        
        if(empty(\Yii::$app->params['sex'][$this->sex_code])){
            $this->setRespones("ERROR_PARAMS", [], "参数错误：sex_code不在指定范围内");
            return null;
        }
        if(empty(\Yii::$app->params['f_structure'][$this->f_structure_code])){
            $this->setRespones("ERROR_PARAMS", [], "参数错误：f_structure_code不在指定范围内");
            return null;
        }
        
        $model = new Student();
        
        if($this->id){
            $model = Student::findIdentity($this->id);
            if(!$model){
                $this->setRespones("ERROR_DATA");
                return null;
            }
        }
        
        $model->class_code = $classCode;
        $model->school_code = \Yii::$app->user->identity->school_code;
        $model->name = $this->name;
        $model->sex_code = $this->sex_code;
        $model->sex = \Yii::$app->params['sex'][$this->sex_code];
        $model->birthday = strtotime($this->birthday);
        $model->f_structure_code = $this->f_structure_code;
        $model->f_structure = \Yii::$app->params['f_structure'][$this->f_structure_code];
        $model->ext_student_id = $this->ext_student_id;
        $model->ext_region_id = $this->ext_region_id;
        if ($model->save()){
            $this->setRespones("ERROR_OK", ['id'=>$model->id]);
            return true;
        }
        $this->setRespones("ERROR_SYSTEM_SAVE", [], $this->getFirstErrors());
        return null;
    }
    
    /**
          * 移除学生
     * @return NULL
     */
    public function remove(){
        $data = \Yii::$app->request->post();
        if(empty($data['id'])){
            $this->setRespones("ERROR_PARAMS", [], "参数错误：缺少必要参数id");
            return null;
        }
        $model = Student::findIdentity($data['id']);
        if(!$model){
            $this->setRespones("ERROR_PARAMS", [], "学生信息不存在或已删除");
            return null;
        }
        
        if(!empty($data['class_code'])){
            $classModel = SchoolClass::findByCode($data['class_code']);
            if(!$classModel){
                $this->setRespones("ERROR_PARAMS", [], "参数错误：class_code对应班级信息不存在或已删除");
                return null;
            }
            if(empty($data['school_code']) || $classModel->school_code != $data['school_code']){
                $this->setRespones("ERROR_PARAMS", [], "参数错误：school_code对应学校中未找到相关班级信息");
                return null;
            }
            $model->class_code = $data['class_code'];
            $model->school_code = $data['school_code'];
        }else{
            $model->class_code = 'other';
        }
        
        if ($model->save()){
            $this->setRespones("ERROR_OK", ['id'=>$model->id]);
            return true;
        }
        $this->setRespones("ERROR_SYSTEM_SAVE", [], $this->getFirstErrors());
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
        $model = Student::findIdentity($data['id']);
        if(!$model){
            $this->setRespones("ERROR_PARAMS", [], "学生信息不存在或已删除");
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
        $model->avatar = $formModel->file_path;
        if($model->save()){
            $this->setRespones("ERROR_OK");
            return $model;
        }
        $this->setRespones("ERROR_SYSTEM_SAVE", [], $model->getFirstErrors());
        return null;
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
        $model = Student::find()->where(['class_code'=>$classCode])->all();
        if($model){
            foreach ($model as $value){
                $data[] = [
                    'id' => $value->id,
                    'name' => $value->name,
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
        
        $model = Student::findIdentity($id);
        if(!$model){
            $this->setRespones("ERROR_DATA");
            return null;
        }
        
        $data = [
            'id' => $model->id,
            'name' => $model->name,
            'avatar' => empty($model->avatar) ? '' : \Yii::$app->params['cdnHost'].$model->avatar,
            'school_name' => $model->school ? $model->school->school_name : '',
            'class_name' => $model->class ? $model->class->class_name : '',
            'parents' => []
        ];
        if($model->parents){
            foreach($model->parents as $value){
                $data['parents'][] = [
                    'id' => $value->id,
                    'name' => $value->name,
                    'phone' => $value->phone
                ];
            }
        }
        $this->setRespones("ERROR_OK", $data);
    }
    
}

