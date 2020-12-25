<?php

namespace app\modules\v1\controllers;

use app\models\User;
use HttpRequestMethodException;
use yii\filters\auth\HttpBasicAuth;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use Yii;
use yii\web\MethodNotAllowedHttpException;
use yii\web\UnauthorizedHttpException;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
            /**
             * @codeCoverageIgnore
             */
            'auth' => function ($username, $password) {
                /** @var User $user */
                $user = User::findByUsername($username);
                if ($user && $user->validatePassword($password)) {
                    return $user;
                }
            }
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();

        unset($actions['view'], $actions['update']);

        return $actions;
    }

    /**
     * updates the model.
     *
     * @param integer $id the id of the model to update
     *
     * @return bool
     *
     * @throws ForbiddenHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function actionUpdate($id)
    {
        if (! Yii::$app->request->isPut) {
            throw new MethodNotAllowedHttpException('Please use PUT');
        }
        
        $post = Yii::$app->request->post();

        if (!empty($post['username'])) {
            throw new ForbiddenHttpException('You cannot change your own username');
        }

        /** @var User $user */
        $user = User::findIdentity($id);

        if (!empty($post['password'])) {
            $user->setPassword(Yii::$app->request->post('password'));
        }

        if (!empty($post['authkey'])) {
            $user->authkey = Yii::$app->request->post('authkey');
        }

        return $user->save();
    }

    /**
     * Shows the user's information stored in the db.
     *
     * @param integer $id the id of the user to show
     *
     * @throws \yii\web\ForbiddenHttpException
     * @return User
     */
    public function actionView($id)
    {
        if ($id == Yii::$app->user->getId()) {
            return User::findOne($id);
        }
        throw new ForbiddenHttpException;
    }

    /**
     * @param $username
     *
     * @return User
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionSearch($username)
    {
        /** @var User $user */
        $user = User::findByUsername($username);
        if ($user && $user->id === Yii::$app->user->getId()) {
            return $user;
        }
        throw new ForbiddenHttpException;
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        if (\Yii::$app->user->isGuest) {
            throw new UnauthorizedHttpException;
        }
    }
}
