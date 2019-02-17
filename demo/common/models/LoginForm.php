<?php
namespace common\models;

use admin\models\Admin;
use Yii;
use yii\base\Model;

/**
 * Login form
 * api登录接口
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return $this->generateToken($this->getUser());
        }
        return false;
    }


    /**
     * @param User $user
     * @return bool
     * 生成登录token
     */
    public function generateToken(Admin $user){
        $enterprise_id = Yii::$app->request->headers->get("Enterprise_id");
        if(empty($enterprise_id) || !is_numeric($enterprise_id)){
            return false;
        }
        $data = [
            "user_id" => $user->id,
            "time"  => time()
        ];
        return \Yii::$app->getSecurity()->encryptByPassword(implode("#" , $data) , $enterprise_id);
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Admin::findByUsername($this->username);
        }

        return $this->_user;
    }
}
