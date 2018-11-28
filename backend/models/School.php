<?php
namespace backend\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * 院校
 */
class School extends ActiveRecord {
    
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
     * @inheritdoc
     */
    public function behaviors(){
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at'
            ],
        ];
    }
    
    /**
     * 保存学院信息
     * @return NULL|array
     */
    public function addSchool(){
        if (!$this->validate()) {
            return null;
        }
        $this->school_code = self::createCode($this->school_name);
        $model = $this->findOne(['school_code'=>$this->school_code]);
        if($model){
            $model->school_tel = $this->school_tel;
        }else{
            $model = $this;
        }
        return $model->save() ? $model : null;
    }
    
    /**
          * 生成唯一学校代码
     * @param string $str
     * @return string
     */
    private static function createCode($str){
        return substr(md5($str),8,16);
    }
    
    /**
          * 获取全部学校信息
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getSchool(){
        $schoolModel = self::find()->where(['is_delete'=>0])->all();
        return $schoolModel;
    }

    public static function findById($id){
        $model = self::find()->where(['id'=>$id, 'is_delete'=>0])->one();
        return $model;
    }
    
    /**
          * 获取列表
     * @param array $post
     * @return number[]|array[]|number[]|array[]|NULL[]
     */
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
        $query = self::find()->where(['is_delete'=>0]);
        $countUser = clone $query;
        $model = $query->limit($length)->offset($start)->all();

        $list['draw'] = $post['draw'];
        $list['recordsTotal'] = $countUser->count();
        $list['recordsFiltered'] = $countUser->count();
        foreach ($model as $value){
            $list['data'][] = [
                "id"=>$value->id,
                'school_tel'=>$value->school_tel,
                "school_name"=>$value->school_name
            ];
        }
        return $list;
    }
    
}