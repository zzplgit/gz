<?php
namespace api\modules\v1\models;

/**
 * 
 */
class Label extends CommonActiveRecord {
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'trim'],
            ['label_name', 'required', 'message' => '标签名称不能为空'],
            ['label_describe', 'trim']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName(){
        return '{{%gz_label}}';
    }

    
    /**
     * @return NULL|array
     */
    public function edit(){
        $model = null;
        if (!$this->validate()) {
            $this->setRespones("ERROR_PARAMS", [], $this->getFirstErrors());
            return null;
        }
        if(\Yii::$app->user->isGuest || !in_array(\Yii::$app->user->identity->role, Common::TEACHER_ROLE)){
            $this->setRespones("ERROR_USER_ROLE");
            return null;
        }
        
        if($this->id){
            $model = self::findById($this->id);
            if(!$model){
                $this->setRespones("ERROR_DATA");
                return null;
            }
        }else{
            $model = $this;
        }
        $model->class_code = \Yii::$app->user->identity->class_code;
        $model->school_code = \Yii::$app->user->identity->school_code;
        $model->label_name = $this->label_name;
        $model->label_describe = $this->label_describe;
        if ($model->save()){
            $this->setRespones("ERROR_OK", ['id'=>$model->id]);
            return true;
        }
        return null;
    }
    
    /**
     * 
     * @return NULL|boolean
     */
    public function del(){
        if(\Yii::$app->user->isGuest || !in_array(\Yii::$app->user->identity->role, Common::TEACHER_ROLE)){
            $this->setRespones("ERROR_USER_ROLE");
            return null;
        }
        $id = \Yii::$app->request->post("id");
        if(empty($id)){
            $this->setRespones("ERROR_PARAMS", [], "缺少必要参数class id");
            return null;
        }
        $model = self::findById($id);
        if(!$model){
            $this->setRespones("ERROR_DATA");
            return null;
        }
        
        if($this->updateAll(['is_delete'=>Common::DATA_DELETED], ['id'=>$id])){
            $this->setRespones("ERROR_OK");
            return true;
        }
        return null;
    }

    /**
     * 
     * @return NULL
     */
    public function getLabels(){
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
        $model = $this->find()->where(['class_code'=>$classCode, 'is_delete'=>Common::DATA_ACTIVE])->all();
        if($model){
            foreach ($model as $value){
                $data[] = [
                    'id' => $value->id,
                    'label_name' => $value->label_name,
                    'label_describe' => $value->label_describe
                ];
            }
        }
        $this->setRespones("ERROR_OK", $data);
    }
    
    /**
     * 
     * @param string $id
     * @return \api\modules\v1\models\SchoolClass|NULL
     */
    public static function findById($id){
        $code = \Yii::$app->user->identity->class_code;
        $model = self::findOne(['id'=>$id, 'class_code'=>$code, 'is_delete'=>Common::DATA_ACTIVE]);
        return $model;
    }
    

    
}