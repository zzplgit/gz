<?php
namespace api\modules\v1\models;

use yii\base\Model;

class Common extends Model{
    
    const MASTER_USER = 100;
    const TEACHER_USER = 200;
    const TEACHER_ROLE = [200];
    const MASTER_ROLE = [100];
    const MASTER_TEACHER_ROLE = [100, 200];
    const DATA_DELETED = 1;
    const DATA_ACTIVE = 0;
    
    
    public $returnData = [];
    
    protected function setRespones($code, $data = [], $msg = ''){
        $this->returnData = Response::getResponse($code, $data, $msg);
    }
    
    protected function copyRespones($respones){
        $this->returnData = $respones;
    }
    
    /**
     *  获取IP
     *  return 数组
     */
    public static function getIp() {
        //return Yii::$app->getRequest()->getUserIP();此方法不能抓去用户真实ip
        if( array_key_exists('HTTP_CDN_SRC_IP', $_SERVER) && !empty($_SERVER['HTTP_CDN_SRC_IP']) ) {
            if (strpos($_SERVER['HTTP_CDN_SRC_IP'], ',')>0) {
                $addr = explode(",",$_SERVER['HTTP_CDN_SRC_IP']);
                return trim($addr[0]);
            } else {
                return $_SERVER['HTTP_CDN_SRC_IP'];
            }
        } else {
            return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 0;
        }
    }
    
    /**
     * 生成唯一代码
     * @param string $str
     * @return string
     */
    public static function createCode($str){
        return substr(md5($str),8,16);
    }
    
}