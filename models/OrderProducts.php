<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_products".
 *
 * @property int $order_id
 * @property string $county_name
 * @property int $product_id
 * @property string $product_code
 * @property string $product_name
 * @property string $product_size
 * @property string $location_description
 * @property int $quantity
 * @property int $product_price
 * @property int $phone_number
 * @property int $user_id
 *
 * @property Location $countyName
 * @property Users $user
 * @property CartProducts $user0
 */
class OrderProducts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['county_name', 'product_id', 'product_code', 'product_name', 'product_size', 'location_description', 'quantity', 'product_price', 'phone_number', 'user_id'], 'required'],
            [['product_id', 'quantity', 'product_price', 'phone_number', 'user_id'], 'integer'],
            [['county_name', 'product_code'], 'string', 'max' => 100],
            [['product_name', 'location_description'], 'string', 'max' => 20],
            [['product_size'], 'string', 'max' => 10],
            [['county_name'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['county_name' => 'county_name']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => CartProducts::className(), 'targetAttribute' => ['user_id' => 'cart_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'county_name' => 'County Name',
            'product_id' => 'Product ID',
            'product_code' => 'Product Code',
            'product_name' => 'Product Name',
            'product_size' => 'Product Size',
            'location_description' => 'Location Description',
            'quantity' => 'Quantity',
            'product_price' => 'Product Price',
            'phone_number' => 'Phone Number',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountyName()
    {
        return $this->hasOne(Location::className(), ['county_name' => 'county_name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsert()
    {
        return $this->hasOne(CartProducts::className(), ['cart_id' => 'user_id']);
    }

    public function getQuantityy(){

        if($this->usert){
            $frt=$this->usert->quantity;
            $fre=$this->usert->product_price;

            $final=$frt*$fre;
           var_dump($final); exit;
            return $final;

        }
    }
}
