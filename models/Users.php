<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property int $phone_number
 * @property string $email_number
 * @property string $password_hash
 * @property string $date_created
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'phone_number', 'email_number', 'password_hash'], 'required'],
            [['phone_number'], 'integer'],
            [['date_created'], 'safe'],
            [['first_name', 'last_name'], 'string', 'max' => 50],
            [['email_number'], 'string', 'max' => 50],
            [['password_hash'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone_number' => 'Phone Number',
            'email_number' => 'Email Number',
            'password_hash' => 'Password',
            'date_created' => 'Date Created',
        ];
    }
}
