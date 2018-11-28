<?php
namespace api\modules\v1\models;


use common\models\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;

class CommonUser extends Common{
    
    const ROLE_ACTIVE = [100, 101, 200];
    
    private $_user;
    
    
    /**
     * 登录
     * @param array $param
     */
    public function login($param){
        $responesData = [
            'token' => '',
            'role' => 0,
            'resetPwd' => false
        ];
        if(empty($param['phone'])){
            $this->setRespones("ERROR_EMPTY_PHONE");
            return;
        }
        
        $user = $this->getUser($param['phone']);
        if (!$user) {
            $this->setRespones("ERROR_USER_NOT_EXIST");
            return;
        }
        
        if(!in_array($user->role, self::ROLE_ACTIVE)){
            $this->setRespones("ERROR_USER_ROLE");
            return;
        }
        
        $responesData['role'] = $user->role;
        //院长登入,如果没有设置过密码 跳转至密码重置
        if($user->role == Headmaster::ROLE_CODE_INITIAL){
            $responesData['resetPwd'] = true;
            $this->setRespones("ERROR_OK", $responesData);
            return $user;
        }
        
        if(empty($param['password'])){
            $this->setRespones("ERROR_EMPTY_PASSWORD");
            return;
        }
        
        if (!$user->validatePassword($param['password'])) {
            $this->setRespones("ERROR_USERNAME_PASSWORD");
            return;
        }
        
        //生成token
        $token = $this->setToken(['uuid'=>$user->uuid, 'role'=>$user->role]);
        $responesData['token'] = $token;
        $this->setRespones("ERROR_OK", $responesData);
        return;
    }
    
    /**
     * 重置密码
     * @param array $param
     */
    public function resetPwd($param) {
        if(empty($param['phone'])){
            $this->setRespones("ERROR_EMPTY_PHONE");
            return;
        }
        if(empty($param['captcha'])){
            $this->setRespones("ERROR_EMPTY_CAPTCHA");
            return;
        }
        if(empty($param['password'])){
            $this->setRespones("ERROR_EMPTY_PASSWORD");
            return;
        }
        
        //短息验证码 验证
        if(!SmsVerify::validateCode($param['phone'], $param['captcha'], "FORGET_THE_PASSWORD")){
            $this->setRespones("ERROR_PARAMS_CAPTCHA");
            return;
        }
        $user = (new User())->findOne(['tel'=>$param['phone']]);
        if($user->role == Headmaster::ROLE_CODE_INITIAL){
            $user->role = Headmaster::ROLE_CODE;
        }
        $user->setPassword($param['password']);
        if($user->save()){
            $this->setRespones("ERROR_OK");
            return $user;
        }
        $this->setRespones("ERROR_SYSTEM_SAVE", [], $user->getFirstErrors());
        return;
    }
    
    
    /**
     * Finds user by [[tel]]
     *
     * @return User|null
     */
    protected function getUser($tel) {
        if ($this->_user === null) {
            $this->_user = User::findOne(['tel' => $tel, 'status' => User::STATUS_ACTIVE]);
        }
        return $this->_user;
    }
    
    /**
     * 设置token
     * @param array $params
     * @return string
     */
    private function setToken($params){
        $jwtParams = \Yii::$app->params['jwt'];
        $signer = new Sha256();
        $token = (new Builder())->setIssuer($jwtParams['iss'])
        ->setAudience($jwtParams['aud'])
        ->setIssuedAt(time())
        ->setNotBefore(time() + $jwtParams['nbf'])
        ->setExpiration(time() + $jwtParams['exp'])
        ->set('uuid', $params['uuid'])
        ->set('role', $params['role'])
        ->sign($signer, $jwtParams['key'])
        ->getToken();
        return $token->__toString();
    }
    
    /**
     * token验证
     * @return boolean|boolean|\common\models\User|NULL
     */
    public static function validateToken(){
        $token = \Yii::$app->request->headers->get("Authorization");
        if(empty($token)){
            return false;
        }
        
        $signer = new Sha256();
        $jwtParams = \Yii::$app->params['jwt'];

        $parser = (new Parser())->parse((string) $token);
        if(!$parser->verify($signer, $jwtParams['key'])){
            return false;
        }

        $data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
        $data->setIssuer($jwtParams['iss']);
        $data->setAudience($jwtParams['aud']);
        if(!$parser->validate($data)){
            return false;
        }
        $uuid = $parser->getClaim('uuid');
        $role = $parser->getClaim('role');
        
        if(!in_array($role, self::ROLE_ACTIVE)){
            return false;
        }
        
        if(!\Yii::$app->user->isGuest && \Yii::$app->user->identity->uuid == $uuid && \Yii::$app->user->identity->role == $role){
            return true;
        }else{
            $user = User::findOne(['uuid'=>$uuid, 'role'=>$role, 'status' => User::STATUS_ACTIVE]);
            if($user){
                \Yii::$app->user->login($user);
                return true;
            }
        }
        return false;
    }
    
    
}