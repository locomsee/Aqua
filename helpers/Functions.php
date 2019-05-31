<?php

namespace app\helpers;

use app\models\Settings;
use app\models\Tblfratcfarmers;
use app\models\Tblsuagriseason;
use app\models\Tbluservisitlog;
use app\models\UserLevels;
use app\models\Users;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use kartik\select2\Select2;
use Yii;
use yii\db\Expression;
use yii\helpers\Html;
use yii\helpers\Url;

class Functions
{
    public static function canShowTertiaryMenu()
    {
        if (\Yii::$app->user->isGuest) {
            return false;
        }

        /* @var $user Users */
        $user = \Yii::$app->user->identity;

        return in_array($user->user_level, Constants::TERTIARY_MENU_LEVELS);
    }

    public static function getDebug()
    {
        return true;
    }

    public static function getFilePath()
    {
        return Url::toRoute(['/'], true) . 'main_code/files/';
    }

    public static function getFilePathAbsolute()
    {
        return dirname(__DIR__) . '/files/';
    }

    public static function checkUserHasPermission($permissionsArray)
    {
        if (!is_array($permissionsArray)) {
            var_dump("<br><br><hr>Check permission needs array input<hr><br><br>");
        }

        if (count($permissionsArray) == 0) {
            return true;
        }

        foreach ($permissionsArray as $permission) {
            if (\Yii::$app->user->can($permission)) {
                return true;
            }
        }

        return false;
    }

    public static function getCurrentSeason()
    {
        $season = Tblsuagriseason::find()->where('active = 1')->one();
        if ($season) {
            return $season->season;
        }

        return "SEASON NOT SET";
    }

    public static function getCurrentSeasonId()
    {
        $season = Tblsuagriseason::find()->where('active = 1')->one();
        if ($season) {
            return $season->season_id;
        }

        return NULL;
    }

    public static function getLoginLockAttempts()
    {
        $lock = Settings::find()->where('`key` = :lock', [':lock' => 'lock'])->one();
        if ($lock) {
            return $lock->value;
        }

        return 10;
    }

    public static function checkUserLocked($username)
    {
        $sum = 0;
        $visit = Tbluservisitlog::find()->where('username_used=:username_used', [':username_used' => $username])
            ->orderBy('user_log_id DESC')->limit(Functions::getLoginLockAttempts())->all();

        $i = 0;
        foreach ($visit as $value) {
            ++$i;
            $sum = $value['status'] + $sum;
        }
        if (0 == $sum && $i == Functions::getLoginLockAttempts()) {
            return true;
        }

        return false;
    }

    public static function getUserOrganization()
    {
        if (\Yii::$app->user->isGuest) {
            return "USER ORGANISATION NOT SET";
        }

        /* @var $user \app\models\Users */
        $user = \Yii::$app->user->identity;
        return $user->organization->full_name;
    }

    public static function getClass($object)
    {
        $class = get_class($object);
        $class = explode("\\", $class);

        return end($class);
    }

    public static function stringContains($hayStack, $needle, $caseSensitive = false)
    {
        if ($caseSensitive) {
            return !empty(strstr($hayStack, $needle));
        }

        return !empty(stristr($hayStack, $needle));
    }

    public static function getAlertClass($message)
    {
        if (self::stringContains($message, 'error')) {
            return 'danger alert-dismissible';
        }

        return 'success alert-dismissible';
    }

    public static function getStartValFromSql($sql, $params)
    {
        return \Yii::$app->db->createCommand($sql, $params)->queryScalar();
    }

    public static function sanitizeInputToRender($inputString)
    {
        $name = ucwords(str_replace("_", " ", $inputString));

        return str_replace(" ", " ", $name);
    }

    public static function renderDropDownSearchField($form, $model, $attribute, $label, $data, $dependsOnId = null, $dataLoadUrl = null, $loadingText = 'Loading ...', $holderClass = 'col-sm-3', $enabled = true)
    {
        /* @var $form \yii\bootstrap\ActiveForm */
        $val = @$_GET[self::getClass($model)][$attribute];

        if ($dependsOnId) {
            $dropDown = $form->field($model, $attribute)->widget(DepDrop::classname(), [
                'data' => $data,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => 'Select One',
                    'value' => $val,
                    'disabled' => !$enabled
                ],
                'type' => DepDrop::TYPE_SELECT2,
                'select2Options' => [
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ],
                'pluginOptions' => [
                    'depends' => [$dependsOnId],
                    'url' => $dataLoadUrl,
                    'loadingText' => $loadingText,
                    'initialize' => true,
                ]
            ])->label(false);
        } else {
            $dropDown = $form->field($model, $attribute)->widget(Select2::classname(), [
                'data' => $data,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => 'Select One',
                    'value' => $val,
                    'disabled' => !$enabled
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(false);
        }

        return '<div class="' . $holderClass . '">
            <label for="' . Html::getInputId($model, $attribute) . '" class="label-bold">' . self::sanitizeInputToRender($label) . '</label>
            <div class="">' .
            $dropDown .
            '</div>
        </div>';
    }

    public static function renderDateSearchField($form, $model, $attribute, $label = '', $beforeText = null, $placeholder = 'Choose Date', $holderClass = 'col-sm-3')
    {
        $appendBefore = '<span class="input-group-addon kv-date-picker">
                <span class="input-group-text">' . $beforeText . '</span>
        </span>';
        if (!$beforeText) {
            $appendBefore = '';
        }
        $layout1 = $appendBefore . '{input}{remove}';

        /* @var $form \yii\bootstrap\ActiveForm */
        $html = DatePicker::widget([
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'name' => Html::getInputName($model, $attribute),
            'value' => @$_GET[Functions::getClass($model)][$attribute],
            'options' => [
                'placeholder' => $placeholder,
                "autocomplete" => "off",
                'id' => Html::getInputId($model, $attribute),
            ],
            'layout' => $layout1,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
            ]
        ]);

        return '<div class="' . $holderClass . '">
            <label for="' . Html::getInputId($model, $attribute) . '" class="label-bold">' . self::sanitizeInputToRender($label) . '&nbsp;</label>
            <div class="">' .
            $html .
            '</div>
        </div>';
    }

    public static function renderSubmitSearchField($label, $buttonClass, $holderClass = 'col-sm-3', $name = null)
    {
        return '<div class="' . $holderClass . '">
            <label for="submit" class="label-bold">&nbsp;</label>
            <div class="">' .
            '<button type="submit" name="' . $name . '" class="' . $buttonClass . '">' . $label . '</button>' .
            '</div>
        </div>';
    }

    public static function renderResetSearchField($label, $buttonClass, $holderClass = 'col-sm-3', $submitAfterReset = false)
    {
        if ($submitAfterReset) {
            $submitAfterReset = 'yes';
        } else {
            $submitAfterReset = '';
        }

        return '<div class="' . $holderClass . '">
            <label for="reset" class="label-bold">&nbsp;</label>
            <div class="">' .
            '<button submitonreset="' . $submitAfterReset . '" type="reset" class="searchFormResetClick ' . $buttonClass . '">' . $label . '</button>' .
            '</div>
        </div>';
    }

    public static function getCwacToProvinceJoinSql($selectField, $whereCondition = '')
    {
        if (!empty($whereCondition)) {
            $whereCondition = " WHERE $whereCondition";
        }

        return "SELECT $selectField FROM `tblsucwac` 
        LEFT JOIN `tblsuacc` ON `tblsuacc`.`acc_id` = `tblsucwac`.`acc_id` 
        LEFT JOIN `tblsuwards` ON `tblsuwards`.`ward_id` = `tblsuacc`.`ward_id` 
        LEFT JOIN `tblsuconstituency` ON `tblsuconstituency`.`constituency_id` = `tblsuwards`.`constituency_id` 
        LEFT JOIN `tblsudistricts` ON `tblsudistricts`.`district_id` = `tblsuconstituency`.`district_id` 
        LEFT JOIN `tblsuprovinces` ON `tblsuprovinces`.`province_id` = `tblsudistricts`.`province_id` 
        $whereCondition";
    }

    public static function getYearJoinSql($fields, $whereCondition = '')
    {
        if (!empty($whereCondition)) {
            $whereCondition = " WHERE $whereCondition";
        }
        return "SELECT $fields FROM  `tblscurbcdvmonths`
            LEFT JOIN `tblsuagriyear`
                ON (`tblscurbcdvmonths`.`year_id` = `tblsuagriyear`.`agri_year_id`)
            INNER JOIN `tblscurbcdv` 
                ON (`tblscurbcdvmonths`.`urb_cdv_id` = `tblscurbcdv`.`urb_cdv_id`) $whereCondition ";
    }

    public static function getCwacToProvinceJoinSqlWithoutSelect()
    {
        return " LEFT JOIN `tblsuacc` ON `tblsuacc`.`acc_id` = `tblsucwac`.`acc_id` 
        LEFT JOIN `tblsuwards` ON `tblsuwards`.`ward_id` = `tblsuacc`.`ward_id` 
        LEFT JOIN `tblsuconstituency` ON `tblsuconstituency`.`constituency_id` = `tblsuwards`.`constituency_id` 
        LEFT JOIN `tblsudistricts` ON `tblsudistricts`.`district_id` = `tblsuconstituency`.`district_id` 
        LEFT JOIN `tblsuprovinces` ON `tblsuprovinces`.`province_id` = `tblsudistricts`.`province_id`";
    }

    public static function genderData()
    {
        return [
            Constants::GENDER_MALE => "Male",
            Constants::GENDER_FEMALE => "Female"
        ];
    }

    public static function getGenderName(string $gender): string
    {

        return isset(Functions::genderData()[$gender]) ? Functions::genderData()[$gender] : '';
    }

    public static function renderInFormDropDown($form, $model, $attribute, $data, $template, $class = 'col-sm-12', $label = null, $customOnChangeFunction = null, $dependsOnId = null, $dataLoadUrl = null, $loadingText = 'Loading ...', $enabled = true, $multiple = false)
    {
        /* @var $form \yii\bootstrap\ActiveForm */
        $options = [
            'options' => ['class' => $class],
            'template' => $template,
        ];
        $pluginEvents = [];
        if ($customOnChangeFunction) {
            $pluginEvents = [
                "change" => "function() { $customOnChangeFunction(this); }",
            ];
        }

        if ($dependsOnId) {
            return $form->field($model, $attribute, $options)->widget(DepDrop::classname(), [
                'data' => $data,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => 'Select One',
                    'disabled' => !$enabled,
                    'multiple' => $multiple,

                ],
                'type' => DepDrop::TYPE_SELECT2,
                'select2Options' => [
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ],
                'pluginOptions' => [
                    'depends' => [$dependsOnId],
                    'url' => $dataLoadUrl,
                    'loadingText' => $loadingText,
                ],
                'pluginEvents' => $pluginEvents,
            ])->label($label);
        } else {
            return $form->field($model, $attribute, $options)->widget(Select2::classname(), [
                'data' => $data,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => 'Select One',
                    'disabled' => !$enabled,
                    'multiple' => $multiple,

                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
                'pluginEvents' => $pluginEvents,
            ])->label($label);
        }
    }

    public static function renderGridColumnWithSelect2Search($dataProvider, $attribute, $valueAttribute, $label, $filterData, $colWidth = '200px')
    {
        $modelClassName = $dataProvider->query->modelClass;
        $modelClassName = explode("\\", $modelClassName);
        $modelClassName = end($modelClassName);

        return [
            'class' => DataColumn::class,
            'attribute' => $attribute,
            'value' => $valueAttribute,
            'label' => $label,
            'format' => 'html',
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => $filterData,
            'filterWidgetOptions' => [
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ],
            'headerOptions' => [
                'style' => 'min-width: ' . $colWidth . '!important; width: ' . $colWidth . '!important;',
            ],
            'filterInputOptions' => [
                'placeholder' => 'Select One',
                'value' => @$_GET[$modelClassName][$attribute],
            ],
        ];
    }

    public static function getUserAssigned()
    {
        /* @var $user Users */
        $user = \Yii::$app->user->identity;

        if ($user->user_level == UserLevels::LEVEL_DISTRICTME || $user->user_level == UserLevels::LEVEL_DISTRICTME_CERTIFY) {
            return $user->getMonitoringUser()->district->name . " District";
        }

        if ($user->user_level == UserLevels::LEVEL_PROVINCEME || $user->user_level == UserLevels::LEVEL_PROVINCEME_CERTIFY) {
            return $user->getMonitoringUser()->province->name . " Province";
        }

        if ($user->user_level == UserLevels::LEVEL_NATIONALME || $user->user_level == UserLevels::LEVEL_NATIONALME_CERTIFY || $user->user_level == UserLevels::LEVEL_NATIONALME_AGRI_BUSINESS || $user->user_level == UserLevels::LEVEL_NATIONALME_FINANCE_CERTIFY || $user->user_level == UserLevels::LEVEL_NATIONALME_CHIEF_ACCOUNTANT) {
            return "National User";
        }

        return "TO BE DONE";
    }

    public static function showProvinceFilter()
    {
        /* @var $user Users */
        if (!\Yii::$app->user->getIsGuest()) {
            $user = \Yii::$app->user->identity;
            if (UserLevels::LEVEL_DISTRICTME == $user->user_level ||
                UserLevels::LEVEL_DISTRICTME_CERTIFY == $user->user_level ||
                UserLevels::LEVEL_PROVINCEME == $user->user_level ||
                UserLevels::LEVEL_PROVINCEME_CERTIFY == $user->user_level ||
                UserLevels::LEVEL_EXTENSION == $user->user_level) {
                return false;
            }
        }
        return true;
    }

    public static function showDistrictFilter()
    {
        /* @var $user Users */
        if (!\Yii::$app->user->getIsGuest()) {
            $user = \Yii::$app->user->identity;
            if (UserLevels::LEVEL_DISTRICTME == $user->user_level ||
                UserLevels::LEVEL_DISTRICTME_CERTIFY == $user->user_level ||
                UserLevels::LEVEL_EXTENSION == $user->user_level) {
                return false;
            }
        }
        return true;
    }

    public static function isProvinceLevel()
    {
        if (!\Yii::$app->user->getIsGuest()) {
            /* @var $user Users */
            $user = \Yii::$app->user->identity;
            if (UserLevels::LEVEL_PROVINCEME == $user->user_level ||
                UserLevels::LEVEL_PROVINCEME_CERTIFY == $user->user_level) {
                return true;
            }
        }
        return false;
    }

    public static function isDistrictLevel()
    {
        if (!\Yii::$app->user->getIsGuest()) {
            /* @var $user Users */
            $user = \Yii::$app->user->identity;
            if (UserLevels::LEVEL_DISTRICTME == $user->user_level ||
                UserLevels::LEVEL_DISTRICTME_CERTIFY == $user->user_level ||
                UserLevels::LEVEL_EXTENSION == $user->user_level) {
                return true;
            }
        }
        return false;
    }

    public static function getDebugError(\Exception $e)
    {
        return static::getDebug() ? 'Error!! ' . $e->getMessage() : 'Unknown error occured, contact admin';
    }

    public static function getDebugErrorWithError(\Error $e)
    {
        return static::getDebug() ? 'Error!! ' . $e->getMessage() : 'Unknown error occured, contact admin';
    }

    public static function renderInFormMaskedInput($form, $model, $attribute, $mask, $mask_validator, $template, $class = 'col-sm-12', $label = null)
    {
        /* @var $form \yii\bootstrap\ActiveForm */
        if ($mask_validator) {
            $html = $form->field($model, $attribute, [
                'options' => ['class' => $class],
                'template' => $template
            ])->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => $mask,
                'definitions' => $mask_validator,
            ])->label($label);
        } else {
            $html = $form->field($model, $attribute, [
                'options' => ['class' => $class],
                'template' => $template
            ])->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => $mask,
            ])->label($label);
        }

        return $html;
    }

    public static function renderInFormDateField($form, $model, $attribute, $template, $class = 'col-sm-12', $label = null, $beforeText = null, $placeholder = 'Choose Date', $disabled = false, $futureDatesDisabled = false)
    {

        /* @var $form \yii\bootstrap\ActiveForm */
        $appendBefore = '<span class="input-group-addon kv-date-picker"><span class="input-group-text">' . $beforeText . '</span></span>';
        if (!$beforeText) {
            $appendBefore = '';
        }
        $layout1 = $appendBefore . '{input}{remove}';

        $html = $form->field($model, $attribute, [
            'options' => ['class' => $class],
            'template' => $template
        ])->widget(DatePicker::className(), [
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'name' => Html::getInputName($model, $attribute),
            'value' => $model->{$attribute},
            'options' => [
                'placeholder' => $placeholder,
                "autocomplete" => "off",
                'id' => Html::getInputId($model, $attribute),
                'disabled' => $disabled
            ],
            'layout' => $layout1,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
                'endDate' => "2d"
//                'endDate' => $futureDatesDisabled ? "Od" : ""
            ]
        ])->label($label);

        return $html;
    }

    public static function maskDetailFromuser(string $string)
    {
        if (is_numeric($string)) {
            $mask_number = str_repeat("*", strlen($string) - 4) . substr($string, -4);
        } else {
            $mask_number = substr($string, 0, 4) . str_repeat("*", strlen($string) - 12) . substr($string, -8);
        }


        return $mask_number;
    }

    public static function generateRandomCode(int $length, bool $numericOnly = false)
    {
        $code = '';

        if ($numericOnly) {
            for ($x = 0; $x < $length; $x++) {
                $code .= mt_rand(0, 9);
            }
        } else {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[mt_rand(0, $charactersLength - 1)];
            }
        }

        return $code;
    }

    public static function escapeValueQuoted($value)
    {
        if (is_null($value)) {
            return '';
        }

        return \Yii::$app->db->quoteValue($value);
    }

    public static function randomPassword($len = 8)
    {

        //define character libraries - remove ambiguous characters like iIl|1 0oO
        $sets = array();
        $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        $sets[] = '23456789';

        $password = '';

        //append a character from each set - gets first 4 characters
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
        }

        //use all characters to fill up to $len
        while (strlen($password) < $len) {
            //get a random set
            $randomSet = $sets[array_rand($sets)];

            //add a random char from the random set
            $password .= $randomSet[array_rand(str_split($randomSet))];
        }

        //shuffle the password string before returning!
        return str_shuffle($password);
    }


    public static function sendEmail(string $toEmail, string $toNames, string $subject, string $emailMessage, array $attachmentsFileNames = [], $autoGenerated = true)
    {
        $mail = Yii::$app->mailer->compose(['html' => 'layouts/style_layout'], [
            'content' => [
                'email' => $emailMessage,
                'autoGenerated' => $autoGenerated
            ],
        ])
            ->setTo([$toEmail => $toNames])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
            ->setSubject($subject);

        foreach ($attachmentsFileNames as $fileName) {
            $mail->attach($fileName);
        }

        return $mail->send();
    }

    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function getYearsDropdown()
    {
        $currentYear = Date("Y");
        $years = [];
        for ($year = 2000; $year < $currentYear + 1; $year++) {
            $years[$year] = $year;
        }
        return $years;
    }

    public static function findAllKeysInArrayAndNotEmpty(array $data, array $required)
    {
        $anyFailed = [];
        $keys = array_keys($data);
        foreach ($required as $field) {
            if (!in_array($field, $keys) || (array_key_exists($field, $data) && empty($data[$field]) && $data[$field] != 0)) {
                $anyFailed[] = $field;
            }
        }

        return $anyFailed;
    }

    public static function validateUserPassword($password, $hashedPassword)
    {
        return Yii::$app->security->validatePassword($password, $hashedPassword);
    }

    public static function getMySqlNow()
    {
        $expression = new Expression('NOW()');
        $now = (new \yii\db\Query)->select($expression)->scalar();
        return $now;
    }

    /**
     * @param array $actions e.g. ['add-user', 'view-users']
     * @param string $cssClass e.g. 'active danger'
     * @return string
     * @author Eddilber Macharia (edd.cowan@gmail.com)<eddmash.com>
     */
    public static function makeActive(array $actions, $cssClass = 'active')
    {
        $url = Yii::$app->request->url;
        $parts = explode("/", $url);
        $actionPart = end($parts);
        $parts = explode("?", $actionPart);
        $requestAction = $parts[0];
        foreach ($actions as $action) {
            if ($action === $requestAction) {
                return $cssClass;
            }

        }
        return '';
    }

    public static function getUploadsFolder()
    {
        return dirname(dirname(__DIR__)) . '/uploads/';
    }

    public static function getBiometricsFolder($farmer_id = '')
    {
        $path = self::getUploadsFolder() . 'fingerprints/';
        if ($farmer_id) {
            $path .= "{$farmer_id}/";
        }

        try {
            mkdir($path, 0777, true);
        } catch (\Exception $e) {
        }

        return $path;
    }

    public static function formatMoney($value)
    {
        return "ZMK " . number_format($value, 2);
    }

    public static function getDateDiffInSeconds($date1, $date2)
    {
        $format = 'Y-m-d H:i:s';
        $date1 = \DateTime::createFromFormat($format, $date1);
        $date2 = \DateTime::createFromFormat($format, $date2);
        return abs($date1->getTimestamp() - $date2->getTimestamp());
    }

    public static function cdvStatusGenerated($status_id)
    {
        if ($status_id > Constants::CDV_STATUS_CREATED) {
            return true;
        }
        return false;
    }


    public static function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = '';

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = 'MSIE';
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = 'Firefox';
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = 'Chrome';
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = 'Safari';
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = 'Opera';
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = 'Netscape';
        }

        // finally get the correct version number
        $known = ['Version', $ub, 'other'];
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            echo '';
        }

        // see how many we have
        $i = count($matches['browser']);
        if (1 != $i) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, 'Version') < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if (null == $version || '' == $version) {
            $version = '?';
        }

        return [
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern,
        ];
    }

    public static function getUploadFundsManagementPath()
    {
        return Yii::$app->getBasePath() . '/../uploads/fundsmanagement/';
    }

    public static function getUploadFundsManagementUrl()
    {
        return \yii\helpers\Url::base(true) . '/uploads/fundsmanagement/';
    }

    public static function downloadFile($fullpath)
    {
//        return Yii::$app->response->sendFile($fullpath);
        if (!empty($fullpath)) {
            header("Content-type:application/pdf"); //for pdf file
            //header('Content-Type:text/plain; charset=ISO-8859-15');
            //if you want to read text file using text/plain header
            header('Content-Disposition: attachment; filename="' . basename($fullpath) . '"');
            header('Content-Length: ' . filesize($fullpath));
            readfile($fullpath);
            Yii::app()->end();
        }
    }

    public static function getStatus()
    {
        return [1 => "Active", 0 => 'Inactive'];
    }

    public static function getStatusFailSuccess()
    {
        return [1 => "Success", 0 => 'Fail'];
    }

    public static function isValidPhone($phone)
    {
        if (preg_match(Constants::PHONE_REGEX, $phone)) {
            return true;
        }

        return false;
    }

    public static function subArraysToString($ar, $sep = ', ')
    {
        $str = '';
        foreach ($ar as $val) {
            $str .= implode($sep, $val);
            $str .= $sep; // add separator between sub-arrays
        }
        $str = rtrim($str, $sep); // remove last separator
        return $str;
    }


    public static function getLeaderFarmers($id)
    {
        $leaders = Tblfratcfarmers::find()->where('atcid=:atcid AND is_leader=:is_leader',
            [':atcid' => $id, ':is_leader' => 1])->all();

        if (count($leaders) == 2) {
            $l1 = $leaders[0];
            $l2 = $leaders[1];

            if ($leaders[0]->farmer_id < $leaders[1]->farmer_id) {
                $leaders = [$l2, $l1];
            }
            return $leaders;
        }
        return false;
    }

    public static function getNonLeaderFarmers($id)
    {
        $leaders = Tblfratcfarmers::find()->where('atcid=:atcid AND is_leader=:is_leader',
            [':atcid' => $id, ':is_leader' => 0])->all();

        return $leaders;
    }

    public static function getBasePath()
    {
        return dirname(\Yii::$app->request->scriptFile);
    }


    public static function isEmptyOrNull($value)
    {
        if (empty($value) || "''" == $value || is_null($value)) {
            return true;
        }

        return false;
    }


    public static function getGeoinfo($cwac)
    {

        $cwacNames = [];
        $accNames = [];
        $wardNames = [];
        $constNames = [];
        $distNames = [];
        $provNames = [];

        if ($cwac) {
            $id = $cwac->acc->ward->constituency->district->district_id;
            $cwacNames[] = $cwac ? $cwac->name : null;
            $acc = $cwac ? $cwac->acc->name : null;
            $accNames[] = $acc;
            $ward = $acc && $cwac->acc->ward ? $cwac->acc->ward->ward : null;
            $wardNames[] = $ward;
            $constituency = $ward && $cwac->acc->ward->constituency ? $cwac->acc->ward->constituency->constituency : null;
            $constNames[] = $constituency;
            $district = $constituency ? $cwac->acc->ward->constituency->district->name : null;
            $distNames[] = $district;
            $province = $district ? $cwac->acc->ward->constituency->district->province->name : null;
            $provNames[] = $province;
        }


        $html = [
            "<b>CWAC:</b> " . ($cwacNames ? implode(",", array_unique($cwacNames)) : "(not set)"),
            "<b>ACC:</b> " . ($accNames ? implode(",", array_unique($accNames)) : "(not set)"),
            "<b>Ward:</b> " . ($wardNames ? implode(",", array_unique($wardNames)) : "(not set)"),
            "<b>Constituency:</b> " . ($constNames ? implode(",", array_unique($constNames)) : "(not set)"),
            "<b>District:</b> " . ($distNames ? implode(",", array_unique($distNames)) : "(not set)"),
            "<b>Province:</b> " . ($provNames ? implode(",", array_unique($provNames)) : "(not set)"),
        ];

        return $html;
    }

    public static function getBeneficiarybioinfo($farmer_id)
    {
        if (isset($farmer_id) && !empty($farmer_id)) {
            $farmer = Yii::$app->db->createCommand("SELECT * from `tblfrfarmers` where `tblfrfarmers`.`farmer_id` = " . $farmer_id . " LIMIT 1")->queryOne();

            $household_no = isset($farmer['farmer_no']) ? $farmer['farmer_no'] : '';
            $household_id = isset($farmer['farmer_id']) ? $farmer['farmer_id'] : '';
            $gender = isset($farmer['farmer_no']) ? $farmer['farmer_no'] : '';
            $first_name = isset($farmer['first_name']) ? $farmer['first_name'] : '';
            $surname = isset($farmer['surname']) ? $farmer['surname'] : '';
            $middle_name = isset($farmer['middle_name']) ? $farmer['middle_name'] : '';
            $full_name = $surname . ' ' . $first_name . ' ' . $middle_name;
            if (isset($farmer['gender']))
                $gender = Functions::getGenderName($farmer['gender']);
            else
                $gender = '';

            return implode(';', [
                "<span style='font-size: 13px; font-weight: bold;'> $full_name </span>
                <span style='font-size: 12px;'>",
                "<b>Household ID:</b> $household_id",
                "<b>Household No:</b> $household_no",
                "<b>Gender:</b> $gender
                </span>",
            ]);

        } else {
            $bioinfo = null;
        }


        return $bioinfo;
    }

    public static function getYear($date_param)
    {
        $ext_year = '';

        if ($date_param != null) {
            $cln_date = \Yii::$app->db->quoteValue($date_param);

            $date_array = explode(' ', $cln_date);

            if (!empty($date_array[0])) {
                $ex_date_array = explode('-', trim($date_array[0]));

                if (isset($ex_date_array[0]) && !empty($ex_date_array[0]))
                    $ext_year = $ex_date_array[0];
            }

        }

        return str_replace("'", "", $ext_year);
    }
}
