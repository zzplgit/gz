<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Teacher;

class TeacherController extends BaseController{

    /**
     * 添加
     */
    public function actionAdd(){
        $post = [];
        $model = new Teacher();
        $post["Teacher"] = \Yii::$app->request->post();
        if ($model->load($post)) {
            $model->signup();
        }
        $this->sendJson($model->returnData);
    }

    /**
     * 编辑
     */
    public function actionEdit(){
        $model = new Teacher();
        $model->edit();
        $this->sendJson($model->returnData);
    }
    
    /**
     * 编辑头像
     */
    public function actionEditAvatar(){
        $model = new Teacher();
        $model->editAvatar();
        $this->sendJson($model->returnData);
    }
    
    /**
     * 获取教师列表
     */
    public function actionGetList(){
        if(\Yii::$app->request->isAjax){
            $model = new Teacher();
            $model->getList();
            $this->sendJson($model->returnData);
        }
    }
    
    /**
     * 获取教师详情
     */
    public function actionGetInfo(){
        if(\Yii::$app->request->isAjax){
            $model = new Teacher();
            $model->getInfo();
            $this->sendJson($model->returnData);
        }
    }
    
}/* class end */
