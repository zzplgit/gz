<?php
namespace api\modules\v1\models;

use yii\base\Model;

class Response extends Model{
    
    private static $errors = [
        ['errDefine' => 'ERROR_OK'                     , 'index' => 1,   'errMsg' => 'success'],
        ['errDefine' => 'ERROR_SYSTEM'                 , 'index' => -1,  'errMsg' => '系统错误'],
        ['errDefine' => 'ERROR_SYSTEM_SAVE'            , 'index' => -2,  'errMsg' => ''],
        ['errDefine' => 'ERROR_PARAMS'                 , 'index' => -3,  'errMsg' => ''],
        ['errDefine' => 'ERROR_DATA'                   , 'index' => -4,  'errMsg' => '数据不存在或已删除'],
        ['errDefine' => 'ERROR_PARAMS_SCENE'           , 'index' => 101,  'errMsg' => '参数错误:scene'],
        ['errDefine' => 'ERROR_PARAMS_CAPTCHA'         , 'index' => 102,  'errMsg' => '验证码输入错误'],
        
        ['errDefine' => 'ERROR_EMPTY_PHONE'            , 'index' => 201,  'errMsg' => '手机号不能为空'],
        ['errDefine' => 'ERROR_SEND_OFTEN'             , 'index' => 202,  'errMsg' => '请勿频繁发送'],
        ['errDefine' => 'ERROR_EMPTY_CAPTCHA'          , 'index' => 203,  'errMsg' => '验证码不能为空'],
        ['errDefine' => 'ERROR_EMPTY_PASSWORD'         , 'index' => 204,  'errMsg' => '密码不能为空'],
        
        
        ['errDefine' => 'ERROR_USERNAME_PASSWORD'      , 'index' => 2,   'errMsg' => '用户名或密码不正确'],
        ['errDefine' => 'ERROR_CODE_IS_WRONG'          , 'index' => 3,   'errMsg' => 'token不正确，请重新获取'],
        ['errDefine' => 'ERROR_OLD_PASSWORD'           , 'index' => 4,   'errMsg' => '旧密码不正确'],
        ['errDefine' => 'ERROR_EMAIL_EXIST'            , 'index' => 5,   'errMsg' => 'email已被使用'],
        ['errDefine' => 'ERROR_USER_EXIST'             , 'index' => 6,   'errMsg' => '用户已存在'],
        ['errDefine' => 'ERROR_USER_RESET_TOKEN_WRONG' , 'index' => 7,   'errMsg' => '重置密码token不正确'],
        ['errDefine' => 'ERROR_USER_NOT_EXIST'         , 'index' => 8,   'errMsg' => '用户不存在或已删除'],
        ['errDefine' => 'ERROR_USER_ROLE'              , 'index' => 9,   'errMsg' => '您所在的用户组没有该操作权限']
        
    ];

    /**
     * 设置返回
     * @param string $code
     * @param array $data
     * @param string $msg
     * @return string
     */
    public static function getResponse($code, $data = [], $msg = ''){
        $response = [
            'code' => 1,
            'message' => 'success',
            'data' => []
        ];
        $response['code'] = $code;
        foreach (self::$errors as $val){
            if($code == $val['errDefine']){
                $response['code'] = $val['index'];
                $response['message'] = $val['errMsg'];
            }
        }
        
        if(!empty($msg)){
            if(is_array($msg)){
                $msg = array_values($msg)[0];
            }
            $response['message'] = $msg;
        }
        $response['data'] = $data;
        return $response;
    }
    
    
}