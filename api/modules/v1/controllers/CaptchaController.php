<?php
namespace api\modules\v1\controllers;


use Yii;
use PHPUnit\Util\Log\JSON;
use api\modules\v1\models\SmsVerify;

class CaptchaController extends BaseController
{

    /**
     * @inheritdoc
     * 
     * http://localhost/gaozhan/back/api/web/index.php?r=v1/captcha/captcha&refresh=1
     * http://localhost/gaozhan/back/api/web/index.php?r=v1/captcha/captcha
     * 
     */
    public function actions(){
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'backColor'=>0x000000,//背景颜色
                'maxLength' => 4, //最大显示个数
                'minLength' => 4,//最少显示个数
                'padding' => 5,//间距
                'height'=>40,//高度
                'width' => 130,  //宽度  
                'foreColor'=>0xffffff,     //字体颜色
                'offset'=>4,        //设置字符偏移量 有效果
                //'controller'=>'login',        //拥有这个动作的controller
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * 发送手机验证
     * @return JSON
     */
    public function actionSendCaptcha(){
        $post = Yii::$app->request->post();
        $SmsVerify = new SmsVerify();
        $SmsVerify->sendCaptcha($post);
        $this->sendJson($SmsVerify->returnData);
    }



}