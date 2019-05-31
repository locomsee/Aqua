<?php

namespace app\controllers;

use app\models\CartProducts;
use app\models\OrderProducts;
use app\models\Products;
use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;
use app\helpers\ConditonBuilder;

class ProductsController extends  SiteController
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
                    [
                        'actions' => ['cartitems','deleteitem','orderitems'],
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
          //  var_dump($_POST['product_id']);die;
            if($validate) {
                echo 'Item already added to cart: ID=> '.$_POST['product_id'];
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


    public function actionCartitems()
    {    $model = new CartProducts();
        $condition = new ConditonBuilder();
        $gt = Yii::$app->user->identity->user_id;
        $conditionrt = $model::find()->where(['user_id' => $gt])->asArray()->all();
        $condition->addCondition('user_id IN(select user_id from cart_products where user_id=:id)', [':id' => $gt]);
        // var_dump($condition); exit;




        if (Yii::$app->request->post('hasEditable')) {
            // instantiate your book model for saving
            $bookId = Yii::$app->request->post('editableKey');
            $model = CartProducts::findOne($bookId);

            // store a default json response as desired by editable
            $out = Json::encode(['output'=>'', 'message'=>'']);

            // fetch the first entry in posted data (there should only be one entry
            // anyway in this array for an editable submission)
            // - $posted is the posted data for Book without any indexes
            // - $post is the converted array for single model validation
            $posted = current($_POST['CartProducts']);
            $post = ['CartProducts' => $posted];

            // load model like any single model validation
            if ($model->load($post)) {
                // can save model or do something before saving model
                $model->save();

                // custom output to return to be displayed as the editable grid cell
                // data. Normally this is empty - whereby whatever value is edited by
                // in the input by user is updated automatically.
                $output = '';

                // specific use case where you need to validate a specific
                // editable column posted when you have more than one
                // EditableColumn in the grid view. We evaluate here a
                // check to see if buy_amount was posted for the Book model
                if (isset($posted['quantity'])) {
                    $output = Yii::$app->formatter->asInteger($model->quantity);
                }

                // similarly you can check if the name attribute was posted as well
                // if (isset($posted['name'])) {
                // $output = ''; // process as you need
                // }
                $out = Json::encode(['output'=>$output, 'message'=>'']);
            }
            // return ajax json encoded response and exit
            echo $out;
            return;
        }















        return $this->render('cartitems',
            array('dataProvider'=> CartProducts::searchModel(10,'cart_id',$condition),
                 'model'=>$model,
                ));
    }


    public function actionDeleteitem($product_id){

       // var_dump($product_id); exit;
        $rem= new CartProducts();
        $countrem=$rem::find()->where(['user_id'=>\Yii::$app->user->identity->user_id])->count();
        $connection= \Yii::$app->db;
        $transaction=$connection->beginTransaction();
        $model=CartProducts::find()->where(['product_id'=>$product_id])->one();
        if($model){
            $model->delete();
            \Yii::$app->session->setFlash('success','Item Removed');
            $transaction->commit();
            if($countrem >= 1){
//                var_dump($countrem); exit;
            return   $this->redirect(['products/cartitems']);
            }else{
           return  $this->redirect(['products/products']);
            \Yii::$app->session->setFlash('error','Dear user select products items');}

        }else{
            $transaction->rollBack();
        \Yii::$app->session->setFlash('error','Item cannot be removed');
      return $this->redirect(['products/cartitems']);}

    }
    public function actionOrderitems($user_id){
        $model=CartProducts::find()->where(['user_id'=>$user_id])->all();
        $final=new OrderProducts();
        if($model){
            $connection= \Yii::$app->db;
            $transaction=$connection->beginTransaction();
            try{
           if($final->load(\Yii::$app->request->post())){

               $final->product_code=$model->product_code;
               $final->product_name=$model->product_name;
               $final->product_size=$model->product_size;
               $final->product_price=$model->product_price;
               $final->save(false);
               $transaction->commit();
               return $this->redirect(['products/products']);

           }} Catch (\Exception $e){
                $transaction->rollBack();
                \Yii::$app->session->setFlash('error', 'Abort Abort!!! '. $e );
                return $this->redirect(['products/cartitems']);
            }

        }

   return $this->render('orderitems',['model'=>$final]);

    }


}
