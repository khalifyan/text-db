<?php

namespace app\models;

use app\db\DB;
use app\db\helpers\UserHelper;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class LoginForm extends Model
{
    public  $username;
    public  $password;
    public  $rememberMe = true;

    private  $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required']
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword($attribute): bool
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if ($user && !Yii::$app->getSecurity()->validatePassword($this->password, $user['password'])) {
                $db = new DB();
                if (!UserHelper::errorCount($user)) {
                        $db->insert([
                            'username' => $this->username,
                            'password' => $user['password'],
                            'error_count' => !empty($user['error_count']) ? $user['error_count'] + 1 : 1,
                            'error_time' => strtotime('now')
                        ]);
                        $this->addError($attribute, 'Неверные данные');
                } else {
                    if (UserHelper::errorTime(strtotime('now'), $user['error_time']) === true) {
                        $db->insert([
                            'username' => $this->username,
                            'password' => $user['password'],
                        ]);
                        $this->addError($attribute, 'Неверные данные');
                    } else {
                        $this->addError($attribute, 'Попробуйте еще раз через ' . UserHelper::errorTime(strtotime('now'), $user['error_time']) . ' секунд');
                    }
                }
                return false;
            } else if (!$user || !Yii::$app->getSecurity()->validatePassword($this->password, $user['password'])) {
                $this->addError($attribute, 'Неверные данные');
                return false;
            } else {
                Yii::$app->session->set('user_id', $this->user['id']);
                return true;
            }
        }

        return false;
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login(): bool
    {
        if ($this->validate()) {
            return $this->validatePassword("password");
        }

        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return object|void
     */
    public function getUser()
    {
        $db = new DB();
        if ($this->_user === false) {
            $this->_user = $db::getByUsername($this->username);
        }

        return $this->_user;
    }
}
