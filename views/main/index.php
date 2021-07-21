<?php
/* @var $this yii\web\View */
/* @var $productProvider ActiveDataProvider */
/* @var $uploadsProvider ArrayDataProvider */

use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\bootstrap\Modal;
use yii\helpers\Html;

$uploaded = !empty($model->result);
?>
<div class="container-all">
	<div class="container-top<?= $uploaded ? ' uploaded':''?>">
		<?php
		if ($uploaded){
			echo GridView::widget([
				'dataProvider' => $uploadsProvider,
			]);
		}
		?>
	</div>
	<div class="container-middle<?= $uploaded ? ' uploaded':''?>">
		<!--<h1>Это main/index</h1>
		<p></p>-->
		<?php
		if ($uploaded){
		?>
			<strong>Выберите из списка поле из которого будет обновлена Закупочная цена:</strong>
		<?php
			$form = ActiveForm::begin();
			$columns = array_keys($model->result[0]);
			echo Html::dropDownList('buy_price', null, $columns);
			ActiveForm::end();
			?>
			<button class="btn btn-primary">Обновить</button>
			<?php
		}
		else{
			Modal::begin([
				'header' => '<b>Выберите файл прайса Поставщика</b>',
				'toggleButton' => [
					'label' => 'Обновить цены',
					'tag' => 'button',
					'class' => 'btn btn-primary',
				],
				'footer' => '&nbsp;',
			]);
				$form = ActiveForm::begin([
					'options' => [
						'enctype' => 'multipart/form-data'
					],
					'action'  => ['/main/upload']
				]);
				?>
				<?= $form->field($model,'priceFile')->fileInput([
					'accept'    => 'application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/msword,image/*',
				])
				?>
				<span class="comment">(допустимые форматы pdf, xls, xlsx, doc, docx)</span>
				<button class="btn btn-primary">Загрузить</button>
				<?php ActiveForm::end();
			Modal::end();
		}
		?>
	</div>
	<div><h4>Прайс Валлес</h4></div>
	<div class="container-bottom<?= $uploaded ? ' uploaded':''?>">
		<?php
		echo GridView::widget([
			'dataProvider' => $productProvider,
		]);
		?>
	</div>
</div>