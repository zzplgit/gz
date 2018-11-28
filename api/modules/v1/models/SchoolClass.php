<?php
namespace api\modules\v1\models;


use common\models\User;
use Faker\Provider\Uuid;

/**
 * 
 */
class SchoolClass extends CommonActiveRecord {
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'trim'],
            ['class_name', 'required', 'message' => '班级名称不能为空']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName(){
        return '{{%gz_school_class}}';
    }

    
    /**
     * 添加班级 权限 校长
     * @return NULL|array
     */
    public function addClass(){
        $model = null;
        if (!$this->validate()) {
            $this->setRespones("ERROR_PARAMS", [], $this->getFirstErrors());
            return null;
        }
        if(\Yii::$app->user->isGuest){
            $this->setRespones("ERROR_CODE_IS_WRONG");
            return null;
        }
        if(!in_array(\Yii::$app->user->identity->role, Common::MASTER_ROLE)){
            $this->setRespones("ERROR_USER_ROLE");
            return null;
        }
        
        $checkCode = true;
        if($this->id){
            $model = self::findById($this->id);
            if(!$model){
                $this->setRespones("ERROR_DATA");
                return null;
            }
            $checkCode = ($model->class_name == $this->class_name) ? false : true;
        }else{
            $model = $this;
            $model->class_code = self::createCode();
        }
        if($checkCode && self::findByName($this->class_name)){
            $this->setRespones("ERROR_DATA", [], '班级名称已存在');
            return null;
        }
        
        $model->class_name = $this->class_name;
        $model->school_code = \Yii::$app->user->identity->school_code;
        if ($model->save()){
            $this->setRespones("ERROR_OK", ['id'=>$model->id, 'class_code'=>$model->class_code]);
            return true;
        }
        return null;
    }
    
    /**
     * 
     * @return NULL|boolean
     */
    public function delClass(){
        if(\Yii::$app->user->isGuest){
            $this->setRespones("ERROR_CODE_IS_WRONG");
            return null;
        }
        if(!in_array(\Yii::$app->user->identity->role, Common::MASTER_ROLE)){
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
        //检查是否有教师关联
        $teacher = User::find()->where(['class_code'=>$model->class_code, 'role'=>Teacher::ROLE_CODE, 'status'=>User::STATUS_DELETED])->one();
        if($teacher){
            $this->setRespones("ERROR_PARAMS", [], "存在关联的教师不能删除");
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
    public function getClasses(){
        if(\Yii::$app->user->isGuest){
            $this->setRespones("ERROR_CODE_IS_WRONG");
            return null;
        }
        $data = [];
        $model = $this->find()->where(['school_code'=>\Yii::$app->user->identity->school_code, 'is_delete'=>Common::DATA_ACTIVE])->all();
        if($model){
            foreach ($model as $value){
                $data[] = [
                    'id' => $value->id,
                    'class_name' => $value->class_name,
                    'class_code' => $value->class_code
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
        $school_code = \Yii::$app->user->identity->school_code;
        $model = self::findOne(['id'=>$id, 'school_code'=>$school_code, 'is_delete'=>Common::DATA_ACTIVE]);
        return $model;
    }
    
    /**
     * 
     * @param string $code
     * @return \api\modules\v1\models\SchoolClass|NULL
     */
    public static function findByCode($code){
        $model = self::findOne(['class_code'=>$code, 'is_delete'=>Common::DATA_ACTIVE]);
        return $model;
    }
    
    /**
     *
     * @param string $code
     * @return \api\modules\v1\models\SchoolClass|NULL
     */
    public static function findByName($name){
        $model = self::findOne(['class_name'=>$name, 'is_delete'=>Common::DATA_ACTIVE]);
        return $model;
    }
    
    
    /**
     * 
     * @param string $className
     * @return string
     */
    private static function createCode(){
        $strCode = Uuid::uuid();
        return Common::createCode($strCode);
    }

    
}