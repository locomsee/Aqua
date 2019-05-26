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
                    [
                        'actions' => ['countcart'],
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

        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try{
        if (Yii::$app->request->isAjax ) {
            $validate=$model::find()->where(['product_id'=>$_POST['product_id']])->all();
            if($validate) {
                echo 'Item already added to cart';
                exit;
            }else
            $model->user_id=Yii::$app->user->identity->user_id;
            $model->product_id=$_POST['product_id'];
            $model->product_name=$_POST['product_name'];
            $model->product_price=$_POST['product_price'];
            $model->product_code=$_POST['product_code'];
            $model->product_size=$_POST['product_size'];
            $model->save(false);
            $transaction->commit();

         echo  "Data inserted";
        }}
        catch(\Exception $e) {

            $transaction->rollBack();

            throw $e;

        }
    }
    public function actionCountcart(){

        if (Yii::$app->request->isAjax) {
            $user_id = Yii::$app->user->identity->user_id;
            $model = new CartProducts();
            $result = $model::find()->where(['user_id' => $user_id])->count();


            return $result;

        }

    }

}
