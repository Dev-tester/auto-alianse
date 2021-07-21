<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name
 * @property string $password
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface{
	public $authKey;
	public $accessToken;

	public static function findIdentity($id){
		return static::findOne($id);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function findIdentityByAccessToken($token, $type = null){
		return static::findOne(['access_token' => $token]);
	}

	/**
	 * Finds user by username
	 *
	 * @param string $username
	 * @return static|null
	 */
	public static function findByUsername($username){
		return User::findOne(['username' => $username]);
	}

	/**
	 * Validates password
	 *
	 * @param string $password password to validate
	 * @return bool if password provided is valid for current user
	 */
	public function validatePassword($password){
		return $this->password === $password;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId(){
		return $this->id;
	}

	/**
     * {@inheritdoc}
     */
    public static function tableName(){
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['name', 'password'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['password'], 'string', 'max' => 40],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(){
        return [
            'id' => 'ID',
            'name' => 'Name',
            'password' => 'Password',
        ];
    }

	/**
	 * {@inheritdoc}
	 */
	public function getAuthKey(){
		return $this->authKey;
	}

	public function validateAuthKey($authKey){
		return $this->authKey === $authKey;
	}
}
