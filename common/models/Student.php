<?php
namespace common\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use backend\models\School;
use api\modules\v1\models\SchoolClass;

/**
 * 
 */
class Student extends ActiveRecord {
    
    /**
     * @inheritdoc
     */
    public static function tableName(){
        return '{{%gz_student}}';
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
    
    public function getSchool(){
        return $this->hasOne(School::className(), ['school_code'=>'school_code']);
    }
    
    public function getClass(){
        return $this->hasOne(SchoolClass::className(), ['class_code'=>'class_code']);
    }
    
    public function getParents(){
        return $this->hasMany(StudentParent::className(), ['student_id'=>'id']);
    }
    
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        if(\Yii::$app->user->identity->role == User::ROLE_TEACHER){
            return static::findOne(['id' => $id, 'class_code' => \Yii::$app->user->identity->class_code]);
        }
        if(\Yii::$app->user->identity->role == User::ROLE_MASTER_ACCESS){
            return static::findOne(['id' => $id, 'school_code' => \Yii::$app->user->identity->school_code]);
        }
        return null;
    }
    
 
    
}