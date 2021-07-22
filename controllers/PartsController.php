<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Part;
use app\models\LoginForm;
use app\models\UploadedFile as Upload;
use yii\web\UploadedFile;

class PartsController extends \yii\web\Controller {

    public function __construct()
    {
        $this->Part = \Yii::$container->get('Part');
        $this->User = \Yii::$container->get('User');
        $this->request = \Yii::$container->get('request');
    }

    public function behaviors()
    {
        return [
            'ghost-access'=> [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }

    /**
     * Чтобы работали ошибки
     *
     * @return array|\string[][]
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex(){
	    // проверяем авторизацию
    	if ($this->User->isGuest){                                                                                  // если не авторизован, авторизация
		    $model = new LoginForm();
		    if ($model->load($this->request->post()) && $model->login()) {
			    return $this->goBack();
		    }

		    $model->password = '';
		    return $this->render('login', [
			    'model' => $model,
		    ]);
	    }

        $params = $this->request->get();
        $searchModel = new $this->Part();
        $dataProvider = $searchModel->search($params);


        return $this->render('index', [
            'model' => $searchModel,
            'partProvider' => $dataProvider,
        ]);
    }

	public function actionUpload(){
    	$model = new Upload();
		$uploadsProvider = null;
		if ($this->request->isPost){
			$model->priceFile = $this->Part::getInstance($model, 'priceFile');
			if ($model->upload()){
				$uploadsProvider = new ArrayDataProvider([
					'allModels' => $model->result,
					'pagination' => [
						'pageSize' => 10,
					],
				]);
			}
		}
		$partProvider = new ActiveDataProvider([
			'query' => $this->Part::find()->where(['AND','price>100','price<200'])->orderBy(['title' => SORT_ASC]),
			'pagination' => [
				'pageSize' => 20,
			],
		]);
		return $this->render('index', [
				'model' => $model,
				'partProvider' => $partProvider,
				'uploadsProvider' => $uploadsProvider
		]);
	}

	/**
	 * Logout action.
	 *
	 * @return Response
	 */
	public function actionLogout(){
        $this->User->logout();
		return $this->goHome();
	}
}
