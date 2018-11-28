<?php

namespace api\modules\v1\controllers;


use api\modules\v1\models\Cycle;
use api\modules\v1\models\SchoolClass;
use api\modules\v1\models\Label;

class SetController extends BaseController{

    /**
     * 添加评估期间 权限 校长 教师
     */
    public function actionAddCycles(){
        if(\Yii::$app->request->isAjax){
            $model = new Cycle();
            $model->addCycles();
            $this->sendJson($model->returnData);
        }
    }
    
    /**
     * 获取当前学年全部评估期间 权限 登录用户
     */
    public function actionGetCycles(){
        if(\Yii::$app->request->isAjax){
            $model = new Cycle();
            $model->getCycles();
            $this->sendJson($model->returnData);
        }
    }
    
    /**
          * 获取当前评估期间 权限 登录用户
     */
    public function actionGetCycle(){
        if(\Yii::$app->request->isAjax){
            $model = new Cycle();
            $model->getCycle();
            $this->sendJson($model->returnData);
        }
    }
    
    /**
     * 添加班级 权限 校长
     */
    public function actionAddClass(){
        if(\Yii::$app->request->isAjax){
            $post = [];
            $post["SchoolClass"] = \Yii::$app->request->post();
            $model = new SchoolClass();
            $model->load($post);
            $model->addClass();
            $this->sendJson($model->returnData);
        }
    }
    
    /**
     * 获取班级列表
     */
    public function actionGetClasses(){
        if(\Yii::$app->request->isAjax){
            $model = new SchoolClass();
            $model->getClasses();
            $this->sendJson($model->returnData);
        }
    }
    
    /**
     * 删除班级 权限 校长
     */
    public function actionDelClass(){
        if(\Yii::$app->request->isAjax){
            $model = new SchoolClass();
            $model->delClass();
            $this->sendJson($model->returnData);
        }
    }
    
    //添加标签 权限 教师
    public function actionEditLabel(){
        if(\Yii::$app->request->isAjax){
            $post = [];
            $post["Label"] = \Yii::$app->request->post();
            $model = new Label();
            $model->load($post);
            $model->edit();
            $this->sendJson($model->returnData);
        }
    }
    
    //删除标签 权限 教师
    public function actionDelLabel(){
        if(\Yii::$app->request->isAjax){
            $model = new Label();
            $model->del();
            $this->sendJson($model->returnData);
        }
    }
    
    //获取标签列表 登录用户
    public function actionGetLabels(){
        if(\Yii::$app->request->isAjax){
            $model = new Label();
            $model->getLabels();
            $this->sendJson($model->returnData);
        }
    }
    
}/* class end */
