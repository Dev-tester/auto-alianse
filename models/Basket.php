<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property int $userId
 * @property int $partId
 */
class Basket extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'baskets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'partId'], 'required'],
            [['userId', 'partId'], 'integer'],
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
            'userId' => 'Basket Owner ID',
            'partId' => 'Basket product ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParts()
    {
        return $this->hasMany(Part::className(), ['id' => 'partId']);
    }
}
