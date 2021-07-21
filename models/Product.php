<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string|null $title
 * @property int|null $buy_price
 * @property string|null $buy_currency
 * @property int|null $retail_price
 * @property string|null $retail_currency
 * @property string|null $site
 */
class Product extends \yii\db\ActiveRecord
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
            [['id'], 'required'],
            [['id', 'buy_price', 'retail_price'], 'integer'],
            [['title'], 'string', 'max' => 1024],
            [['buy_currency', 'retail_currency'], 'string', 'max' => 32],
            [['site'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'buy_price' => 'Buy Price',
            'buy_currency' => 'Buy Currency',
            'retail_price' => 'Retail Price',
            'retail_currency' => 'Retail Currency',
            'site' => 'Site',
        ];
    }
}
