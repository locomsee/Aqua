<?php

/* @var $this yii\web\View */

use yii\bootstrap\Button;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Aqua Products Available';
$this->params['breadcrumbs'][] = $this->title;
?>
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
<div class="site-products">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    <div class="w3-container w3-teal">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="items">
        <button  type="button" class="btn btn-primary">
            Items in Cart <span class="badge badge-light" id="CartNo">0</span>
        </button>

    </div>
    <br><br><br>
    <div class="check">
        <?= Html::a('View Items In Cart', ['products/cartitems'], ['class'=>'btn btn-primary']) ?>
    </div>
    <div class="w3-row-padding w3-margin-top">
        <?php foreach ($model as  $value) {//Opening foreach.

            ?>

            <div class="w3-third">
                <div class="w3-card">

                    <?= Html::img('@web/Aquaproducts/'.$value['product_location']);?>
                    <div class="w3-container">
                        <h5 class="id" ><span class="label label-primary" style="color:white"><?= $value['product_id']?></span></h5>
                        <h5 class="name"><span class="label label-primary" style="color:white"><?= $value['product_name']?></span></h5>
                        <h5 class="price"><span class="label label-warning" style="color:blue"><?= $format='Ksh. '. $value['product_price']?></span></h5>
                        <h5 class="code" hidden><span class="label label-warning" style="color:blue"><?= $value['product_code']?></span></h5>
                        <h5 class="size" hidden><span class="label label-warning" style="color:blue"><?= $value['product_size']?></span></h5>
                        <div class="eset">
                            <?=  Button::widget([
                                'label' => 'Add Cart',
                                'id'=>'haha',
                                'options' => ['class' => 'btn btn-danger'],
                            ]);?>
                        </div>
                    </div>

                </div>
            </div>
        <?php }//closing foreach?>
    </div><!-- .w3-row-padding -->

</div>


</div>

<style>
    .items{
        position: absolute;
        right: 105px;
    }
    .check{
        position: absolute;
        right: 105px;
    }

</style>


<script src="https://code.jquery.com/jquery-3.4.1.min.js"> </script>
<script>
    $(document).ready(function () {
        //Add cart items to database
        $('.eset').click(function () {
            var cripe = $(this).parent().find('.price').text();
            var price=cripe.replace('Ksh. ',' ');
            $.ajax({
                method: 'POST',
                url: '<?php echo Yii::$app->request->baseUrl. '/products/savecart' ?>',
                data:{product_id:$(this).parent().find('.id').text(), product_name:$(this).parent().find('.name').text(), product_price:price, product_code:$(this).parent().find('.code').text(),product_size:$(this).parent().find('.size').text()},
                success: function(data) {
                    alert(data);
                },
            });
            $.ajax({
                method: 'GET',
                url: '<?php echo Yii::$app->request->baseUrl. '/products/countcart' ?>',
                success: function (data) {
                    if(data >= 1){
                        $('.check').show()
                    }else
                        $('.check').hide()
                },
            }).done(function (data) {
                $('#CartNo').html(data);
            });
        });
    });
</script>

<script>

    $.ajax({
        method: 'GET',
        url: '<?php echo Yii::$app->request->baseUrl. '/products/countcart' ?>',
        success: function (data) {
            // alert('It has worked');
        },
    }).done(function (data) {
        $('#CartNo').html(data);
        if(data >= 1){
            $('.check').show()
        }else
            $('.check').hide()
    });

</script>





