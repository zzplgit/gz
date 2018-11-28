<?php

namespace api\modules\v1\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;


class CommonActiveRecord extends ActiveRecord {

    public $returnData = [];

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
    
    protected function setRespones($code, $data = [], $msg = ''){
        $this->returnData = Response::getResponse($code, $data, $msg);
    }

}
