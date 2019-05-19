<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Aqua Products Available';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-products">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    <div class="w3-container w3-teal">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php

   // echo var_dump($model); exit;


//        foreach ($model  as $value) {
//           $iu= '<div class="w3-row-padding w3-margin-top">';
//      $iu= ' <div class="w3-third">';
//          $iu='  <div class="w3-card">';
//            $iu='   <img src="Aquaproducts/1.png" style="width:100%">';
//            $iu=' <div class="w3-container">';
//        echo    ' <h5> '. $value["product_code"];'</h5>';
//        }
//    ?>




    <div class="w3-row-padding w3-margin-top">
        <div class="w3-third">
            <div class="w3-card">
                <img src="Aquaproducts/1.png" style="width:100%">
                <div class="w3-container">
                    <h5>Vernazza</h5>
                </div>
            </div>
        </div>

        <div class="w3-third">
            <div class="w3-card">
                <img src="Aquaproducts/2.png" style="width:100%">
                <div class="w3-container">
                    <h5>Monterosso</h5>
                </div>
            </div>
        </div>

        <div class="w3-third">
            <div class="w3-card">
                <img src="Aquaproducts/3.png" style="width:100%">
                <div class="w3-container">
                    <h5>Vernazza</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="w3-row-padding w3-margin-top">
        <div class="w3-third">
            <div class="w3-card">
                <img src="Aquaproducts/4.png" style="width:100%">
                <div class="w3-container">
                    <h5>Manarola</h5>
                </div>
            </div>
        </div>

        <div class="w3-third">
            <div class="w3-card">
                <img src="Aquaproducts/5.png" style="width:100%">
                <div class="w3-container">
                    <h5>Corniglia</h5>
                </div>
            </div>
        </div>

        <div class="w3-third">
            <div class="w3-card">
                <img src="Aquaproducts/1.png" style="width:100%">
                <div class="w3-container">
                    <h5>Riomaggiore</h5>
                </div>
            </div>
        </div>
    </div>

?>

</div>
