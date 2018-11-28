<?php
namespace backend\controllers;

use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\School;

/**
 * Headmaster controller
 */
class SchoolController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionList()
    {
        return $this->render('list');
    }

    public function actionGetList(){
        if(\Yii::$app->request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $get = \Yii::$app->request->get();
            $list = School::getList($get);
            return $list;
        }
    }
    
    /**
          *  园长添加
     * @return string
     */
    public function actionEdit(){
        $model = new School();
        if ($model->load(\Yii::$app->request->post()) && $model->addSchool()) {
            return $this->redirect(["school/list"]);
        }
        if(\Yii::$app->request->get("id")){
            $id = \Yii::$app->request->get("id");
            $model = School::findById($id);
        }
        
        return $this->render('edit', ['model'=>$model]);
    }
    

    
}
