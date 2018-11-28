<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\StudentForm;
use api\modules\v1\models\ParentForm;

class StudentController extends BaseController{


    /**
     * 编辑
     */
    public function actionEdit(){
        if(\Yii::$app->request->isAjax){
            $post = [];
            $post["StudentForm"] = \Yii::$app->request->post();
            $model = new StudentForm();
            $model->load($post);
            $model->edit();
            $this->sendJson($model->returnData);
        }
    }
    
    /**
     * 编辑头像
     */
    public function actionEditAvatar(){
        if(\Yii::$app->request->isAjax){
            $model = new StudentForm();
            $model->editAvatar();
            $this->sendJson($model->returnData);
        }
    }
    
    /**
     * 获取列表
     */
    public function actionGetList(){
        if(\Yii::$app->request->isAjax){
            $model = new StudentForm();
            $model->getList();
            $this->sendJson($model->returnData);
        }
    }
    
    /**
     * 获取详情
     */
    public function actionGetInfo(){
        if(\Yii::$app->request->isAjax){
            $model = new StudentForm();
            $model->getInfo();
            $this->sendJson($model->returnData);
        }
    }
    
    /**
     * 移除学生
     */
    public function actionRemove(){
        if(\Yii::$app->request->isAjax){
            $model = new StudentForm();
            $model->remove();
            $this->sendJson($model->returnData);
        }
    }
    
    /**
     * 编辑添加学生家长
     */
    public function actionEditParent(){
        if(\Yii::$app->request->isAjax){
            $post = [];
            $post["ParentForm"] = \Yii::$app->request->post();
            $model = new ParentForm();
            $model->load($post);
            $model->edit();
            $this->sendJson($model->returnData);
        }
    }
    
    /**
     * 删除家长 园长 教师
     */
    public function actionDelParent() {
        if(\Yii::$app->request->isAjax){
            $model = new ParentForm();
            $model->remove();
            $this->sendJson($model->returnData);
        }
    }
    
    /**
     * 获取家长详情
     */
    public function actionGetParentInfo(){
        if(\Yii::$app->request->isAjax){
            $model = new ParentForm();
            $model->getInfo();
            $this->sendJson($model->returnData);
        }
    }
    
    
    
}/* class end */
