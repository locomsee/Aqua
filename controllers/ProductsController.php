<?php

namespace app\controllers;

use app\models\CartProducts;
use app\models\Products;
use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class ProductsController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['products'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['savecart'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionProducts()
    {

        $model=new Products();

        $ter=$model::find()->asArray()->all();
       // var_dump($ter); exit;


        return $this->render('products', [

            'model' => $ter,
        ]);
    }

    public function actionSavecart()
    {
        $model = new CartProducts();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->user_id=Yii::$app->user->identity->user_id;

           $model->save(false);

            \Yii::$app->response->format = Response::FORMAT_JSON;
            //return ['success'=>'Response'];
            return ['success' =>$model->save()];
        }
    }

}
