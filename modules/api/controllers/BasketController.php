<?php


namespace app\modules\api\controllers;

use Codeception\Util\HttpCode;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\rest\ActiveController;
use yii\web\ConflictHttpException;
use yii\web\NotFoundHttpException;

class BasketController extends ActiveController
{
    public $modelClass = 'app\modules\api\models\Basket';

    private $User;
    private $Basket;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->Basket = \Yii::$container->get('Basket');
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
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    /* Declare methods supported by APIs */
    protected function verbs(){
        return [
            'view' => ['GET'],
            'update' => ['PUT', 'PATCH','POST'],
            'delete' => ['DELETE'],
        ];
    }

    /**
     * Показывает корзину текущего пользователя
     *
     * @param $id int
     * @throws NotFoundHttpException
     * @return array
     */
    public function actionView(int $id): array
    {
        return $this->getUserBasket($id);
    }

    /**
     * Возвращает корзину текущего пользователя
     *
     * @param $userId int
     * @throws NotFoundHttpException
     * @return array
     */
    private function getUserBasket(int $userId): array
    {
        $basketParts = $this->Basket::find()->joinWith('parts')->where(['userId' => $userId])->asArray()->all();

        if (empty($basketParts))
        {
            throw new NotFoundHttpException("Basket for user $userId not found");
        }

        $parts = [];
        foreach ($basketParts as $basketPart)
        {
            $parts[] = $basketPart['parts'][0]['title'];
        }

        $user = $this->User::findOne($userId);
        if (empty($user))
        {
            throw new NotFoundHttpException("User $userId not found");
        }

        return [
            'code' => HttpCode::OK,
            'user' => $user->username,
            'basket' => $parts,
        ];
    }

    /**
     * Добавляет запчасть в корзину
     * Если добавление успешно, возвращает новую корзину
     *
     * @param $id int ID пользователя
     * @throws ConflictHttpException
     * @return array
     */
    public function actionUpdate(int $id): array
    {
        $post = \Yii::$app->request->post();

        $existPart = $this->Basket::find() ->joinWith('parts')
                                    ->where(['partId' => $post['Basket']['partId'], 'userId' => $post['Basket']['userId']])
                                    ->asArray()
                                    ->one();

        if ($existPart)
        {
            throw new ConflictHttpException("Part '{$existPart['parts'][0]['title']}' already in user's {$post['Basket']['userId']} basket");
        }

        $BasketPart = new $this->Basket();

        if ($BasketPart->load($post)) {
            if ($BasketPart->save()) {
                return $this->getUserBasket($id);
            }
        }

        return [
            'code' => HttpCode::NO_CONTENT,
            'errors' => $BasketPart->getErrors()
        ];
    }

    /**
     * Удаляет запчасть из корзины пользователя
     * Если обновление успешно, возвращает ID удалённой запчасти
     *
     * @param $id int
     * @throws NotFoundHttpException
     * @return mixed
     */
    public function actionDelete(int $id)
    {
        $post = \Yii::$app->request->post();

        $BasketPart = $this->Basket::find() ->joinWith('parts')
            ->where(['partId' => $post['Basket']['partId'], 'userId' => $post['Basket']['userId']])
            ->one();

        if (!$BasketPart)
        {
            throw new NotFoundHttpException("Part '{$post['Basket']['partId']}' doesnt exist in user's {$post['Basket']['userId']} basket");
        }

        if ($BasketPart->delete()) {
            return $this->getUserBasket($id);
        }

        return [
            'code' => HttpCode::NO_CONTENT,
            'errors' => $BasketPart->getErrors()
        ];
    }
}