<?php


namespace app\modules\api\controllers;

use Codeception\Util\HttpCode;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

class PartController extends ActiveController
{
    public $modelClass = 'app\modules\api\models\Part';

    private $User;
    private $Part;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->Part = \Yii::$container->get('Part');
        $this->User = \Yii::$container->get('User');
        $this->request = \Yii::$container->get('request');
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::class,
            'auth' => function ($username, $password)
            {
                if ($user = $this->User::find()->where(['username' => $username])->one())
                {
                    if (!empty($password) && $user->validatePassword($password))
                    {
                        return $user;
                    }
                }

                return null;
            },
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ]
            ]
        ];
        return $behaviors;
    }

    /* Declare actions supported by APIs (Added in api/modules/v1/components/controller.php too) */
    public function actions(){
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    /* Declare methods supported by APIs */
    protected function verbs(){
        return [
            'index'=>['GET'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH','POST'],
            'delete' => ['DELETE'],
            'view' => ['GET'],
        ];
    }

    /**
     * Выводит список всех запчастей
     * @return array
     */
    public function actionIndex()
    {
        $params = $this->request->get();
        $dataProvider = $this->Part->search($params);

        return [
            'data' => $dataProvider,
            'totalCount' => $dataProvider->getTotalCount()
        ];
    }

    /**
     * Создаёт новую запчасть.
     * Если создание успешно, возвращает ID созданной запчасти
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $part = new $this->Part;

        if ($part->load($this->request->post())) {
            if ($part->save()) {
                return [
                    'code' => HttpCode::CREATED,
                    'id' => $part->id
                ];
            }
        }

        return [
            'code' => HttpCode::INTERNAL_SERVER_ERROR,
            'errors' => $part->getErrors()
        ];
    }

    /**
     * Обновляет текущую запчасть.
     * Если обновление успешно, возвращает новые данные запчасти
     *
     * @param $id int
     * @throws NotFoundHttpException
     * @return mixed
     */
    public function actionUpdate(int $id)
    {
        $part = $this->Part::findOne($id);

        if (!$part)
        {
            throw new NotFoundHttpException('Part not found');
        }

        if ($part->load($this->request->post())) {
            if ($part->save()) {
                return [
                    'code' => HttpCode::OK,
                    'data' => $part
                ];
            }
        }

        return [
            'code' => HttpCode::NO_CONTENT,
            'errors' => $part->getErrors()
        ];
    }

    /**
     * Удаляет текущую запчасть.
     * Если обновление успешно, возвращает ID удалённой запчасти
     *
     * @param $id int
     * @throws NotFoundHttpException
     * @return mixed
     */
    public function actionDelete(int $id)
    {
        $part = $this->Part::findOne($id);

        if (!$part)
        {
            throw new NotFoundHttpException('Part not found');
        }

        if ($part->delete()) {
            return [
                'code' => HttpCode::OK,
                'id' => $part->id
            ];
        }

        return [
            'code' => HttpCode::INTERNAL_SERVER_ERROR,
            'errors' => $part->getErrors()
        ];
    }

    /**
     * Показывает текущую запчасть.
     *
     * @param $id int
     * @throws NotFoundHttpException
     * @return mixed
     */
    public function actionView(int $id)
    {
        $part = $this->Part::findOne($id);

        if (!$part)
        {
            throw new NotFoundHttpException('Part not found');
        }

        return [
            'code' => HttpCode::OK,
            'part' => $part
        ];
    }
}