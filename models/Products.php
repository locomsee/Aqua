<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $product_id
 * @property string $product_code
 * @property string $product_name
 * @property string $product_size
 * @property string $product_location
 * @property int $product_price
 */
class Products extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_code', 'product_name', 'product_size', 'product_price'], 'required'],
            [['product_price'], 'integer'],
            [['product_code'], 'string', 'max' => 100],
            [['product_name'], 'string', 'max' => 20],
            [['product_size'], 'string', 'max' => 10],
            [['product_location'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'Product ID',
            'product_code' => 'Product Code',
            'product_name' => 'Product Name',
            'product_size' => 'Product Size',
            'product_location' => 'Product Location',
            'product_price' => 'Product Price',
        ];
    }
}
