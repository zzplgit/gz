<?php
namespace api\modules\v1\models;

use common\models\Year;

/**
 * 
 */
class Cycle extends CommonActiveRecord {
    
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
        return '{{%gz_cycle}}';
    }
    
    /**
          * 添加评估期间
     * @param array $data
     */
    public function addCycles(){
        $data = \Yii::$app->request->post("cycles");
        if(\Yii::$app->user->isGuest){
            $this->setRespones("ERROR_CODE_IS_WRONG");
            return null;
        }
        if(!in_array(\Yii::$app->user->identity->role, Common::MASTER_TEACHER_ROLE)){
            $this->setRespones("ERROR_USER_ROLE");
            return null;
        }
        
        $countData = count($data);
        if($countData < 2 || $countData > 4){
            $this->setRespones("ERROR_PARAMS", [], "评估期间不能小于2个或者大于4个");
            return null;
        }
        
        $yearModel = Year::getYear();
        if(!$yearModel){
            $this->setRespones("ERROR_PARAMS", [], "请先设置学年信息");
            return null;
        }
        $yearId = $yearModel->id;
        $time = time();
        foreach ($data as $key=>$value){
            if(empty($value['start_at']) || empty($value['end_at'])){
                $this->setRespones("ERROR_PARAMS", [], "参数错误缺少start_at或end_at");
                return null;
            }
            $lastEndAt = 0;
            $startAt = strtotime($value['start_at']." 00:00:00");
            $endAt = strtotime($value['end_at']." 23:59:59");
            if($endAt <= $startAt){
                $this->setRespones("ERROR_PARAMS", [], "评估期间结束时间不能小于等于开始时间");
                return null;
            }
            if(!empty($lastEndAt) && $startAt <= $lastEndAt){
                $this->setRespones("ERROR_PARAMS", [], "评估期间开始时间【".$value['start_at']."】不能小于上个期间的结束时间【".$data[$key-1]['end_at']."】");
                return null;
            }
            
            if($startAt < $yearModel->start_at){
                $this->setRespones("ERROR_PARAMS", [], "评估期间开始时间【".$value['start_at']."】不能小于当前学年的开始时间【".date("Y-m-d", $yearModel->start_at)."】");
                return null;
            }
            if($endAt > $yearModel->end_at){
                $this->setRespones("ERROR_PARAMS", [], "评估期间结束时间【".$value['end_at']."】不能大于当前学年的结束时间【".date("Y-m-d", $yearModel->end_at)."】");
                return null;
            }
            
            $data[$key]['start_at'] = $startAt;
            $data[$key]['end_at'] = $endAt;
            $data[$key]['year_id'] = $yearId;
            $data[$key]['school_code'] = \Yii::$app->user->identity->school_code;
            $data[$key]['created_at'] = $time;
            $data[$key]['updated_at'] = $time;
            $lastEndAt = $endAt;
        }
        $this->deleteAll(['year_id'=>$yearId, 'school_code'=>\Yii::$app->user->identity->school_code]);
        $exe = \Yii::$app->db->createCommand()->batchInsert(self::tableName(),['start_at','end_at','year_id','school_code','created_at','updated_at'], $data)->execute();
        if($exe){
            $this->setRespones("ERROR_OK");
            return true;
        }
        return null; 
    }
    
    /**
          * 获取当前学年全部评估期间 权限 登录用户
     * @return void|NULL
     */
    public function getCycles(){
        if(\Yii::$app->user->isGuest){
            $this->setRespones("ERROR_CODE_IS_WRONG");
            return null;
        }
        
        $responesData = [
            'year_id' => '',
            'year_title' => '',
            'year_start_at' => '',
            'year_end_at' => '',
            'cycles' => []
        ];
        
        $yearModel = Year::getYear();
        if(!$yearModel){
            $this->setRespones("ERROR_PARAMS", [], "请先设置学年信息");
            return null;
        }
        $responesData['year_id'] = $yearModel->id;
        $responesData['year_title'] = $yearModel->title;
        $responesData['year_start_at'] = date("Y-m-d", $yearModel->start_at);
        $responesData['year_end_at'] = date("Y-m-d", $yearModel->end_at);
        $model = $this->find()->where(['year_id'=>$yearModel->id, 'school_code'=>\Yii::$app->user->identity->school_code])->all();
        if($model){
            foreach ($model as $value){
                $responesData['cycles'][] = [
                    'start_at' => date("Y-m-d", $value->start_at),
                    'end_at' => date("Y-m-d", $value->end_at)
                ];
            }
        }
        $this->setRespones("ERROR_OK", $responesData);
        return;
    }
    
    /**
          * 获取当前评估期间 权限 登录用户
     * @return void|NULL
     */
    public function getCycle(){
        if(\Yii::$app->user->isGuest){
            $this->setRespones("ERROR_CODE_IS_WRONG");
            return null;
        }
        
        $responesData = [
            'year_id' => '',
            'year_title' => '',
            'year_start_at' => '',
            'year_end_at' => '',
            'start_at' => '',
            'end_at' => ''
        ];
        
        $yearModel = Year::getYear();
        if(!$yearModel){
            $this->setRespones("ERROR_PARAMS", [], "请先设置学年信息");
            return null;
        }
        $responesData['year_id'] = $yearModel->id;
        $responesData['year_title'] = $yearModel->title;
        $responesData['year_start_at'] = date("Y-m-d", $yearModel->start_at);
        $responesData['year_end_at'] = date("Y-m-d", $yearModel->end_at);
        $model = $this->find()->where(['year_id'=>$yearModel->id, 'school_code'=>\Yii::$app->user->identity->school_code])->all();
        if($model){
            $time = time();
            foreach ($model as $value){
                if($time > $value->start_at && $time <= $value->end_at){
                    $responesData['start_at'] = date("Y-m-d", $value->start_at);
                    $responesData['end_at'] = date("Y-m-d", $value->end_at);
                    break;
                }
            }
        }
        $this->setRespones("ERROR_OK", $responesData);
        return;
    }
    
    
    
    
    
    

    
    
}