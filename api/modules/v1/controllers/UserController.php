<?php

namespace api\modules\v1\controllers;



//use api\modules\v1\models\Headmaster;
use api\modules\v1\models\CommonUser;

class UserController extends BaseController{

    /**
     * 添加校长 【接口关闭：院长信息统一从后台添加】
     */
    public function actionAddHeadmaster(){
        return false;
//         $post = [];
//         $model = new Headmaster();
//         $post["Headmaster"] = \Yii::$app->request->post();
//         if ($model->load($post)) {
//             $model->signup();
//         }
//         $this->sendJson($model->returnData);
    }

    /**
     * 登录
     */
    public function actionLogin(){
        $model = new CommonUser();
        $post = \Yii::$app->request->post();
        $model->login($post);
        $this->sendJson($model->returnData);
    }
 
    /**
     * 重置密码
     */
    public function actionResetPwd(){
        $model = new CommonUser();
        $post = \Yii::$app->request->post();
        $model->resetPwd($post);
        $this->sendJson($model->returnData);
    }

    

}/* class end */
