<?php
namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class passwordForm extends Model
{
    public $password;
    public $token;
    public $phone;
    private $_user;

    public function getPasswordResetToken($phone)
    {
        $userData = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'username' => $phone,
        ]);

        if(!$userData){
            return 0;
        }

        if (!User::isPasswordResetTokenValid($userData->password_reset_token)) {
            $userData->generatePasswordResetToken();
        }

        if (!$userData->save()) {
            return 0;
        }

        return $userData->password_reset_token;
    }

    public function changePassword()
    {
        if (empty($this->token) || !is_string($this->token)) {
            return false;
        }

        $this->_user = User::findByPasswordResetToken($this->token);
        if (!$this->_user) {
            return false;
        }

        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        return $user->save(false);
    }
}
