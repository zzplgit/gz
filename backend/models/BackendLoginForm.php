<?php
namespace backend\models;

use common\models\LoginForm;
use common\models\User;

/**
 * Login form
 */
class BackendLoginForm extends LoginForm
{

    private $_user;
    
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(['username' => $this->username, 'status' => User::STATUS_ACTIVE, 'role'=>0]);
        }

        return $this->_user;
    }
}
