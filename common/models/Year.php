<?php
namespace common\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * 
 */
class Year extends ActiveRecord {
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', "trim"],
            ['title', 'required', 'message' => '学年名称不能为空'],
            ['start_at', 'required', 'message' => '开始时间不能为空'],
            ['end_at', 'required', 'message' => '结束时间不能为空']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName(){
        return '{{%gz_year}}';
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
    public function addYear(){
        if (!$this->validate()) {
            return null;
        }
        
        $startAt = strtotime($this->start_at." 00:00:00");
        $endAt = strtotime($this->end_at." 23:59:59");
        
        //开始时间不能小于等于结束时间
        if($endAt <= $startAt){
            $this->addError("start_at", "结束时间不能小于等于开始时间");
            return null;
        }
        //开始时间不能小于等于 数据库中存的结束时间
        $cQuery = self::find()->where([">=", 'end_at', $startAt]);
        if($this->id){
            $cQuery->andWhere(["<>", 'id', $this->id]);
            $this->isNewRecord = false;
        }
        $cModel = $cQuery->one();
        if($cModel){
            $this->addError("start_at", "学年开始时间不能小于等于已存在学年的结束时间");
            return null;
        }

        $this->start_at = $startAt;
        $this->end_at = $endAt;
        return $this->save() ? $this : null;
    }
    
    /**
          * 获取当前学年
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getYear(){
        $time = time();
        $model = self::find()->andWhere(["<", 'start_at', $time])->andWhere([">=", 'end_at', $time])->one();
        return $model;
    }

    public static function findById($id){
        $model = self::find()->where(['id'=>$id])->one();
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
        $time = time();
        $query = self::find()->where([">=", 'end_at', $time]);
        
        $countQuery = clone $query;
        $model = $query->limit($length)->offset($start)->all();
        $list['draw'] = $post['draw'];
        $list['recordsTotal'] = $countQuery->count();
        $list['recordsFiltered'] = $countQuery->count();
        foreach ($model as $value){
            $list['data'][] = [
                "id"=>$value->id,
                'title'=>$value->title,
                'start_at'=>date("Y-m-d", $value->start_at),
                "end_at"=>date("Y-m-d", $value->end_at),
                "created_at"=>date("Y-m-d H:i:s", $value->created_at),
                "updated_at"=>date("Y-m-d H:i:s", $value->updated_at)
            ];
        }
        return $list;
    }
    
}