<?php
namespace api\modules\v1\models;


use common\models\StudentParent;
use common\models\Student;

class ParentForm extends Common{
    

    public $id;
    public $student_id;
    public $name;
    public $phone;
    public $send_sms;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', "trim"],
            ['student_id', 'required', 'message' => 'student_id不能为空'],
            ['name', 'required', 'message' => '家长姓名不能为空'],
            ['phone', 'required', 'message' => '家长电话不能为空'],
            ['send_sms', "trim"],
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
        
        $student = Student::findIdentity($this->student_id);
        if(!$student){
            $this->setRespones("ERROR_DATA");
            return null;
        }
        
        $model = new StudentParent();
        if($this->id){
            $model = StudentParent::findIdentity($this->id);
            if(!$model){
                $this->setRespones("ERROR_DATA");
                return null;
            }
        }
        $model->student_id = $this->student_id;
        $model->name = $this->name;
        $model->phone = $this->phone;
        $model->send_sms = empty($this->send_sms) ? 0 : 1;
        if ($model->save()){
            $this->setRespones("ERROR_OK", ['id'=>$model->id]);
            return true;
        }
        $this->setRespones("ERROR_SYSTEM_SAVE", [], $this->getFirstErrors());
        return null;
    }
    
    /**
     * 移除
     * @return NULL
     */
    public function remove(){
        $data = \Yii::$app->request->post();
        if(empty($data['id'])){
            $this->setRespones("ERROR_PARAMS", [], "参数错误：缺少必要参数id");
            return null;
        }
        
        $model = StudentParent::findIdentity($data['id']);
        if(!$model){
            $this->setRespones("ERROR_PARAMS", [], "学生家长信息不存在或已删除");
            return null;
        }
        $student = Student::findIdentity($model->student_id);
        if(!$student){
            $this->setRespones("ERROR_PARAMS", [], "您不是该学生的教师或者园长没有权限删除该学生的家长信息");
            return null;
        }
        $model->is_delete = 1;
        
        if ($model->save()){
            $this->setRespones("ERROR_OK", ['id'=>$model->id]);
            return true;
        }
        $this->setRespones("ERROR_SYSTEM_SAVE", [], $this->getFirstErrors());
        return null;
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
        
        $model = StudentParent::findIdentity($id);
        if(!$model){
            $this->setRespones("ERROR_DATA");
            return null;
        }
        $student = Student::findIdentity($model->student_id);
        if(!$student){
            $this->setRespones("ERROR_PARAMS", [], "您不是该学生的教师或者园长没有权限查看该学生的家长信息");
            return null;
        }
        
        $data = [
            'id' => $model->id,
            'name' => $model->name,
            'phone' => $model->phone,
            'send_sms' => $model->send_sms,
        ];
        $this->setRespones("ERROR_OK", $data);
    }
    
}

