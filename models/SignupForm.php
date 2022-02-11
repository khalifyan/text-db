<?php

namespace app\models;

use app\db\DB;
use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $password;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],


            ['password', 'required'],
            ['password', 'string', 'min' => 5],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful
     * @throws \yii\base\Exception
     */
    public function signup(): ?bool
    {
        if (!$this->validate()) {
            return null;
        }

        $db = new DB();
        $db->insert([
                'username' => $this->username,
                'password' => Yii::$app->getSecurity()->generatePasswordHash($this->password)
            ]);

        return true;
    }
}
