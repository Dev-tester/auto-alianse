<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Product;
use app\models\LoginForm;
use app\models\UploadedFile as Upload;
use yii\web\UploadedFile;

class MainController extends \yii\web\Controller {
    public function actionIndex(){
	    // проверяем авторизацию
    	if (Yii::$app->user->isGuest){                                                                                  // если не авторизован, авторизация
		    $model = new LoginForm();
		    if ($model->load(Yii::$app->request->post()) && $model->login()) {
			    return $this->goBack();
		    }

		    $model->password = '';
		    return $this->render('login', [
			    'model' => $model,
		    ]);
	    }
	    // показываем форму
	    $model = new Upload();
	    $productProvider = new ActiveDataProvider([
		    'query' => Product::find()->where(['AND','buy_price>100','buy_price<200'])->orderBy(['title' => SORT_ASC]),
		    'pagination' => [
			    'pageSize' => 20,
		    ],
	    ]);

	    return $this->render('index', [
		        'model' => $model,
		        'productProvider' => $productProvider,
		        'uploadsProvider' => null
	    ]);
    }

	public function actionUpload(){
    	$model = new Upload();
		$uploadsProvider = null;
		if (Yii::$app->request->isPost){
			$model->priceFile = UploadedFile::getInstance($model, 'priceFile');
			if ($model->upload()){
				$uploadsProvider = new ArrayDataProvider([
					'allModels' => $model->result,
					'pagination' => [
						'pageSize' => 10,
					],
				]);
			}
		}
		$productProvider = new ActiveDataProvider([
			'query' => Product::find()->where(['AND','buy_price>100','buy_price<200'])->orderBy(['title' => SORT_ASC]),
			'pagination' => [
				'pageSize' => 20,
			],
		]);
		return $this->render('index', [
				'model' => $model,
				'productProvider' => $productProvider,
				'uploadsProvider' => $uploadsProvider
		]);
	}

	/**
	 * Logout action.
	 *
	 * @return Response
	 */
	public function actionLogout(){
		Yii::$app->user->logout();
		return $this->goHome();
	}
}
