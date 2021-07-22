<?php


namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;
//use webvimark\modules\UserManagement\controllers\AuthController as BaseController;
use app\models\LoginForm;

class AuthController extends \yii\web\Controller
{
    /**
     * Заменяем Action для рендеринга родной yii2-формы
     *
     * @return string
     */
    public function actionLogin()
    {
        if ( !Yii::$app->user->isGuest )
        {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ( Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) )
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ( $model->load(Yii::$app->request->post()) AND $model->login() )
        {
            return $this->goBack();
        }

        //return $this->renderIsAjax('login', compact('model'));
        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Рендерим шаблон yii2 по умолчанию
     *
     * @return string|null
     */
    public function getViewPath()
    {
        // нужно для того, чтобы использовать layouts/main
        $this->layout = null;

        return __DIR__.'/../views/parts';
    }
}