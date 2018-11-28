<?php

namespace api\modules\v1\controllers;

use yii\web\Controller;
use api\modules\v1\models\CommonUser;
use yii\filters\VerbFilter;
use yii\filters\Cors;
use yii\filters\auth\HttpBearerAuth;
/**
 * Users Controller API
 *
 * @author yasin
 */
class BaseController extends Controller
{
    public $enableCsrfValidation = false;

    protected $_user;
    private $exception = [
        'reset-pwd',
        'captcha',
        'send-captcha',
        'login'
    ];
    
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [],
            ],
            'corsFilter' => [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Origin' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 86400,
                    'Access-Control-Expose-Headers' => [],
                ],
            ],
        ];
        if (\Yii::$app->getRequest()->getMethod() !== 'OPTIONS') {
            $behaviors['authenticator'] = [
                'class' => HttpBearerAuth::className(),
                'optional' => [
                    'login',
                    'signup'
                ],
            ];
        }
        
        return $behaviors;
    }
    
    
    public function beforeAction($action){
        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            die;
        }
        
        if(!in_array($action->id, $this->exception)){
            $this->_user = CommonUser::validateToken();
            if(!$this->_user){
                return $this->sendJson(['code'=>-1, 'data'=>[], 'message'=>'token验证失败']);
            }
        }
        
        return parent::beforeAction($action);
    }

    public function init() {
        parent::init();
        header('Content-type:text/html; charset=utf-8');
    }

    protected function sendJson($data){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        echo json_encode($data);
        return;
    }

}
