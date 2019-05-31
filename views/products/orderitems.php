<?php

use app\helpers\Functions;
use app\models\Location;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderProducts */
/* @var $form yii\widgets\ActiveForm */
$template = '{label}{input}{error}';
$this->title = 'Final order details';
//if ($model->pos_device_id) {
//    $this->title = "Update Mobile Device";
//}
?>

<div class="modal-content">

    <?php $form = ActiveForm::begin([
        'enableClientValidation' => true,
        'options' => [
            'class' => 'modal-dialog',
        ],]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?= $this->title ?></h4>
    </div>
    <div class="modal-dialog">

        <div class="modal-dialog">
            <div class="md-form mb-3">
                <?= $form->field($model, 'quantityy')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="md-form mb-3">
                <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>
            </div>
            <br>
            <div class="md-form mb-3">
                <?= $form->field($model, 'county_name')->dropDownList(
                    ArrayHelper::map(Location::find()->all(),'county_id','county_name'),
                    ['prompt' => 'Select']
                ) ?>
            </div>


        </div>

        <div class="row">
            <?= Functions::renderSubmitSearchField(
                'Save',
                'btn btn-primary',
                'col-sm-6'
            ) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"> </script>
<script>
    $(document).ready(function(){
        $(".close").click(function(){
            $('.modal-dialog').hide();
        });
    });
</script>


