<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Check-Out Items';
?>


<div class="row">

    <div class="col-md-10">

        <?= \kartik\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'searchFieldPlaceholder' => 'Search By Agrodealer code',
            'createButton' => [
                'visible' => true,
                'label' => 'Submit', 'url' => Url::to(['order']), 'modal' => false,

            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn',
                ],
                [
                    'label' => 'User Name',
                    'value' => 'email_number',
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
                    'label' => 'Product Price',
                    'value' => 'Product size',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{DeleteUser}',//{delete}
                    'visibleButtons' => [
                        'update' =>true,
                        'Delete'=>false,
                    ],
                    'buttons' => [

                        'DeleteItem' => function ($url, $model) {
                            /* @var $model \app\models\Tblscdistrictbanks */
                            $url = Url::to(['products/deleteitem', 'id' => $model->cart_id]);
                            return Html::a('<span class="fa fa-pencil"></span> Remove ', $url, [
                                'title' => 'Remove Item',
                                'class' => 'always_open_link',
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