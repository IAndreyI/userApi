<?php 
namespace app\controllers;

use Yii;
use app\models\User;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\filters\auth\HttpBasicAuth;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::class,
            'except' => ['create', 'login'],
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update'], $actions['delete'], $actions['view']);
        return $actions;
    }

    public function actionCreate()
    {
        $model = new User();
        $model->load(Yii::$app->request->getBodyParams(), '');
        if ($model->save()) {
            return $model;
        } else {
            Yii::$app->response->setStatusCode(422);
            return $model->errors;
        }
    }

    public function actionUpdate($id)
    {
        $model = User::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('User not found');
        }
        $model->load(Yii::$app->request->getBodyParams(), '');
        if ($model->save()) {
            return $model;
        } else {
            Yii::$app->response->setStatusCode(422);
            return $model->errors;
        }
    }

    public function actionDelete($id)
    {
        $model = User::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('User not found');
        }

        if ($model->delete()) {
            Yii::$app->response->setStatusCode(204);
        } else {
            Yii::$app->response->setStatusCode(422);
            return $model->errors;
        }
    }

    public function actionLogin()
    {
        $username = Yii::$app->request->getBodyParam('username');
        $password = Yii::$app->request->getBodyParam('password');

        $user = User::findOne(['username' => $username]);

        if ($user && $user->validatePassword($password)) {
            return ['access_token' => $user->access_token];
        } else {
            throw new UnauthorizedHttpException('Invalid credentials');
        }
    }

    public function actionView($id)
    {
        $model = User::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('User not found');
        }

        return $model;
    }

}