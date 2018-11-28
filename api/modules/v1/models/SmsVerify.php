<?php

namespace api\modules\v1\models;

use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;

/**
  * 手机验证
 */
class SmsVerify extends CommonActiveRecord {


    public $captchaLength = 4;
    private $scenes = [
        'FORGET_THE_PASSWORD'
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName(){
        return '{{%gz_sms_verify}}';
    }
    
    /**
     * 发送手机短信验证码
     * @param array $post
     */
    public function sendCaptcha($post){
        if(empty($post['phone'])){
            $this->setRespones("ERROR_EMPTY_PHONE");
            return;
        }
        if(empty($post['scene']) || !in_array($post['scene'], $this->scenes)){
            $this->setRespones("ERROR_PARAMS_SCENE");
            return;
        }
        
        if(empty($post['captcha'])){
            $this->setRespones("ERROR_EMPTY_CAPTCHA");
            return;
        }
        
        $oldRecord = $this->find()->where(['phone'=>$post['phone']])->orderBy("send_at desc")->one();
        if($oldRecord){
            $time = time();
            if($time - $oldRecord->send_at < \Yii::$app->params['sms']['sms_send_gap']){
                $this->setRespones("ERROR_SEND_OFTEN");
                return;
            }
        }

        $captcha_validate  = new \yii\captcha\CaptchaAction('captcha',\Yii::$app->controller);
        if($post['captcha'] != $captcha_validate->getVerifyCode()){
            $this->setRespones("ERROR_PARAMS_CAPTCHA");
            return;
        }
        
        $phone = trim($post['phone']);
        $captcha = $this->createCaptcha();

        //发送接口
        $rs = self::sendCodeSms($phone, $captcha);
        if(isset($rs->Code) && $rs->Code == "OK"){
            $this->send_type = 1;
            $this->send_type_msg = "发送成功";
            $this->ali_request_id = $rs->RequestId;
            $this->ali_biz_id = $rs->BizId;
        }else{
            $this->send_type = 2;
            $this->send_type_msg = "发送失败:".$rs->Message;
            $this->ali_request_id = '';
            $this->ali_biz_id = '';
        }

        $this->phone = $phone;
        $this->captcha = $captcha;
        $this->scene = $post['scene'];
        $this->send_at = time();

        $this->ip = Common::getIp();
        if(!$this->save()){
            $this->setRespones("ERROR_SYSTEM_SAVE", [], $this->getFirstErrors());
            return;
        }
        return;
    }

    /**
     * 生成验证码
     * @return string
     */
    private function createCaptcha(){
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
        shuffle($chars);
        $output = "";
        for ($i=0; $i<$this->captchaLength; $i++){
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }

    /**
     * 发送手机
     * @param string $phone
     * @param string $code
     * @return static
     */
    public static function sendCodeSms($phone, $code) {
        $request = new SendSmsRequest();
        $request->setProtocol("https");
        $request->setPhoneNumbers($phone);
        $request->setSignName(\Yii::$app->params['sms']['aliyun_sign_name']);
        $request->setTemplateCode(\Yii::$app->params['sms']['aliyun_template_code']);
        $request->setTemplateParam(json_encode(array(
            "code"=>$code
        ), JSON_UNESCAPED_UNICODE));
        
        // 发起访问请求
        $accessKeyId = \Yii::$app->params['sms']['aliyun_accessKeyId'];
        $accessKeySecret = \Yii::$app->params['sms']['aliyun_accessKeySecret'];
        $acsResponse = \AliyunSms::getAcsClient($accessKeyId, $accessKeySecret)->getAcsResponse($request);
        
        return $acsResponse;
    }
    
    /**
     * 验证code是否有效
     * @param string $phone 电话号码
     * @param string $code 验证码
     * @param string $scene 发送类型
     * @return boolean
     */
    public static function validateCode($phone, $code, $scene){
        $model = self::find()->where(['phone'=>$phone, 'captcha'=>$code, 'scene'=>$scene, 'send_type'=>1])->orderBy("send_at desc")->one();
        $pastTime = \Yii::$app->params['sms']['sms_past_time'];
        if($model && ($model->send_at+$pastTime) >= time()){
            return true;
        }
        return false;
    }
 





}
