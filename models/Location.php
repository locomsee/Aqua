<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "location".
 *
 * @property int $county_id
 * @property int $county_code
 * @property string $county_name
 *
 * @property OrderProducts[] $orderProducts
 */
class Location extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'location';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['county_code', 'county_name'], 'required'],
            [['county_code'], 'integer'],
            [['county_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'county_id' => 'County ID',
            'county_code' => 'County Code',
            'county_name' => 'County Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProducts()
    {
        return $this->hasMany(OrderProducts::className(), ['county_name' => 'county_name']);
    }
}
