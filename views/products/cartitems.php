<?php

use app\helpers\MyKartikGridView;
use kartik\form\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
rmrevin\yii\fontawesome\AssetBundle::register($this);


$this->title = 'Check-Out Items';
?>


<div class="row">

      <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
<!--            <h4><i class="icon fa fa-check"></i>Saved!</h4>-->
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>


    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
<!--            <h4><i class="icon fa fa-check"></i>Saved!</h4>-->
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>

    <div class="col-md-10">

        <?= app\helpers\MyKartikGridView::widget([
            'dataProvider' => $dataProvider,
             'searchField' =>false,
            'createButton' => [
                'visible' => true,
                'label' => 'Order Items','data-toggle'=>'modal','url' => Url::to(['products/orderitems','user_id'=>\Yii::$app->user->identity->user_id ]), 'modal' => true,
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn',
                ],
                [
                    'label' => 'Email Number',
                    'value' => 'user.email_number',
                ],
                [
                    'label' => 'Product Name',
                    'value' => 'product_name',
                ],
                [
                    'label' => 'Product Size',
                    'value' => 'product_size',

                ],
                [
                    'label' => 'Product Price Per Item',
                    'value' => 'product_price',
                ],
                [
                    'class'=>'kartik\grid\EditableColumn',
                    'attribute'=>'quantity',
                    'editableOptions'=>[
                        'header'=>'Buy Amount',
                        'inputType'=>\kartik\editable\Editable::INPUT_SPIN,
                        'options'=>['pluginOptions'=>['min'=>0, 'max'=>5000]]
                    ],
                    'hAlign'=>'right',
                    'vAlign'=>'middle',
                    'width'=>'100px',
                    'format'=>['integer'],
                    'pageSummary'=>true
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{detailbank}<br>{DeleteUser}',//{delete}
                    'visibleButtons' => [
                        'update' =>true,
                        'Delete'=>false,
                    ],
                    'buttons' => [
                        'detailbank' => function ($url, $model) {

                            $url = Url::to(['products/deleteitem', 'product_id' => $model->product_id]);
                            return Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> Remove Item', $url, [
                                'title' => 'Finalize',
                                'class' => 'always_open_link',
                                'data' => [
                                    'confirm' => 'Are you sure you want to remove this item?',
                                    'method' => 'post',
                                ]
                            ]);
                        },

                    ],],

            ],

            'exportConfig' => [
                MyKartikGridView::CSV => ['label' => 'Export as CSV', 'filename' => 'My Items to order -' . date('d-M-Y')],
                MyKartikGridView::HTML => ['label' => 'Export as HTML', 'filename' => 'My Items to order -' . date('d-M-Y')],
                MyKartikGridView::PDF => ['label' => 'Export as PDF', 'filename' => 'My Items to order -' . date('d-M-Y')],
                MyKartikGridView::EXCEL => ['label' => 'Export as EXCEL', 'filename' => 'My Items to order -' . date('d-M-Y')],
                MyKartikGridView::TEXT => ['label' => 'Export as TEXT', 'filename' => 'My Items to order -' . date('d-M-Y')],
            ],

        ]); ?>
    </div>
</div>