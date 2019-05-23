<?php

/* @var $this yii\web\View */

use yii\bootstrap\Button;
use yii\helpers\Html;

$this->title = 'Aqua Products Available';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-products">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    <div class="w3-container w3-teal">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="items">
        <button type="button" class="btn btn-primary">
            Items in Cart <span class="badge badge-light">0</span>
        </button>

    </div>
    <div class="w3-row-padding w3-margin-top">
        <?php foreach ($model as  $value) {//Opening foreach.
           // var_dump(dirname(\Yii::$app->request->scriptFile)); exit;
            $imagelocation = dirname(\Yii::$app->request->scriptFile) . '/'. 'Aquaproducts/';
              // var_dump($model); exit;

            ?>

            <div class="w3-third">
                <div class="w3-card">
<!--                    <img src="--><?//= $imagelocation.$value['product_location']?><!--" style="width:100%">-->
                    <?= Html::img('@web/Aquaproducts/'.$value['product_location']);?>
                    <div class="w3-container">

                        <h5 id="name"><span class="label label-primary" style="color:white"><?= $value['product_name']?></span></h5>
                        <h5 id="price"><span class="label label-warning" style="color:blue"><?= $value['product_price']?></span></h5>

                        <?=  Button::widget([
                            'label' => 'Add to Cart',
                            'id'=>'addcart',
                            'options' => ['class' => 'btn btn-danger'],
                        ]);?>
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


</style>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"> </script>

<script>
    $(document).ready(function () {
        $('#addcart').click(function (e) {

           // alert('You clicked me');


            e.preventDefault();
            var name=$('#name').val();
            var price=$('#price').val();

            $.ajax({
                method: 'POST',
                url: '<?php echo Yii::$app->request->baseUrl. '/products/savecart' ?>',
                data: "product_name=" + name+ "&product_price=" + price,
                success: function(data) {
                    alert("success");
                }

            })


        });
        
    });
</script>

