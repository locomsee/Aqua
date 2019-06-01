<?php

namespace app\models;

use app\helpers\MyActiveRecord;
use Yii;

/**
 * This is the model class for table "cart_products".
 *
 * @property int $cart_id
 * @property int $user_id
 * @property int $product_id
 * @property string $product_code
 * @property string $product_name
 * @property string $product_size
 * @property int $product_price
 * @property int $quantity
 *
 * @property Users $user
 */
class CartProducts extends MyActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cart_products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'product_id', 'product_code', 'product_name', 'product_size', 'product_price'], 'required'],
            [['user_id', 'product_id', 'product_price', 'quantity'], 'integer'],
            [['product_code'], 'string', 'max' => 100],
            [['product_name'], 'string', 'max' => 20],
            [['product_size'], 'string', 'max' => 10],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cart_id' => 'Cart ID',
            'user_id' => 'User ID',
            'product_id' => 'Product ID',
            'product_code' => 'Product Code',
            'product_name' => 'Product Name',
            'product_size' => 'Product Size',
            'product_price' => 'Product Price',
            'quantity' => 'Quantity',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }
}
