<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string|null $number
 * @property string|null $title
 * @property int|null $price
 * @property int|null $volume
 */
class Part extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'parts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'title', 'volume', 'price'], 'required'],
            [['id', 'volume', 'price'], 'integer'],
            [['title'], 'string', 'max' => 1024],
            [['number'], 'string', 'max' => 32],
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
            'number' => 'Part article number',
            'title' => 'Part title',
            'volume' => 'Parts quantity',
            'price' => 'Price',
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = self::find();

        //$query->with(["status", "polyclinic", "treatment", "formDisease", "updatedBy"]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 40),
            ],
            'sort'=>[
                'defaultOrder'=>[
                    'id'=>SORT_DESC,
                ],
            ],
        ]);

        if (!$this->load($params)) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'number' => $this->number,
            'volume' => $this->volume,
            'price' => $this->price,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }

}
