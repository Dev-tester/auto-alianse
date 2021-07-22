<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $model
 */


$this->title = UserManagementModule::t('back', 'Создать пользователя');
$this->params['breadcrumbs'][] = ['label' => UserManagementModule::t('back', 'Пользователи'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

	<h2 class="lte-hide-title"><?= $this->title ?></h2>

	<div class="panel panel-default">
		<div class="panel-body">

			<?= $this->render('main', [
				"model" => $model,
			]) ?>
		</div>
	</div>

</div>