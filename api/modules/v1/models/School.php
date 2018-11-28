<?php
namespace api\modules\v1\models;

/**
 * 院校
 */
class School extends CommonActiveRecord {
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['school_name', 'required', 'message' => '院校名称不能为空'],
            ['school_tel', 'required', 'message' => '院校电话不能为空']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName(){
        return '{{%gz_school}}';
    }
    
    
    /**
     * 保存学院信息
     * @return NULL|array
     */
    public function addSchool(){
        if (!$this->validate()) {
            $this->setRespones("ERROR_PARAMS", [], $this->getFirstErrors());
            return null;
        }

        $model = $this->findOne(['school_name'=>$this->school_name]);
        if($model){
            $this->setRespones("ERROR_OK", ['school_code'=>$model->school_code], '');
            return $model->school_code;
        }
        
        $this->school_code = self::createCode($this->school_name);
        
        if($this->save()){
            $this->setRespones("ERROR_OK", ['school_code'=>$this->school_code], '');
            return $this->school_code;
        }else{
            $this->setRespones("ERROR_SYSTEM_SAVE", [], $this->getFirstErrors());
            return null;
        }
    }
    
    /**
     * 生成唯一学校代码
     * @param string $str
     * @return string
     */
    private static function createCode($str){
        return substr(md5($str),8,16);
    }
    
    
}