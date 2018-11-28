<?php
namespace backend\controllers;

use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\Headmaster;
use backend\models\School;

/**
 * Headmaster controller
 */
class HeadmasterController extends Controller
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
        if(\Yii::$app->request->isPost){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $post = \Yii::$app->request->post();
            $list = Headmaster::getList($post);
            return $list;
        }
    }
    
    /**
          *  园长添加
     * @return string
     */
    public function actionEdit(){
        $model = new Headmaster();
        if ($model->load(\Yii::$app->request->post()) && $model->signup()) {
            return $this->redirect(["headmaster/list"]);
        }
        if(\Yii::$app->request->get("id")){
            $model->uid = \Yii::$app->request->get("id");
            $model->findByUid();
        }
        
        $schoolModel = School::getSchool();
        return $this->render('edit', ['model'=>$model, 'schoolModel'=>$schoolModel]);
    }
    
    public function actionDel(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new Headmaster();
        $model->uid = \Yii::$app->request->post('id');
        if(!$model->del()){
            $msg = array_values($model->getFirstErrors());
            return ['code'=>-1, "message"=>$msg[0], 'data'=>[]];
        }
        return ['code'=>1, "message"=>'', 'data'=>[]];
    }
    
}
