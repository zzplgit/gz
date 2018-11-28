<?php
namespace common\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use backend\models\School;
use api\modules\v1\models\SchoolClass;

/**
 * 
 */
class StudentParent extends ActiveRecord {
    
    /**
     * @inheritdoc
     */
    public static function tableName(){
        return '{{%gz_student_parent}}';
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
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'is_delete' => 0]);
    }
    
 
    
}