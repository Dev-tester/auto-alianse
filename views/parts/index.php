<?php
/* @var $this yii\web\View */
/* @var $model app\models\Part */
/* @var $partProvider ActiveDataProvider */

use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\bootstrap\Modal;
use yii\helpers\Html;

$uploaded = !empty($model->result);
?>
<div class="container-all">
	<div><h4>Прайс Auto Alliance</h4></div>
	<div class="container-bottom">
		<?php
		echo GridView::widget([
			'dataProvider' => $partProvider,
            'filterModel' => $model,
            'columns' => [
                [
                    'filter' => false,
                    'attribute'=>'id',
                ],
                'number',
                'title',
                'price',
                'volume',
            ],
		]);
		?>
	</div>
</div>