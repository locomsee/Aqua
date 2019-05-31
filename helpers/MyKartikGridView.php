<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/19/18
 * Time: 9:19 AM
 */

namespace app\helpers;

use kartik\grid\ActionColumn;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class MyKartikGridView extends GridView
{
    //public $options = ['id' => 'my-kartik-grid'];
    //public $export = ['label' => 'Export to'];
    public $exportLabel = 'Export to';
    public $topFilterColumns = [];
    public $pjax = true;
    public $bordered = true;
    public $striped = true;
    public $condensed = true;
    public $responsive = true;
    public $hover = true;
    private $myRefreshGridId = '-pjax';
    public $createButton = ['visible' => false, 'label' => '', 'url' => '', 'modal' => true];
    public $searchField = true;
    public $searchFieldPlaceholder = "Search grid by using common columns...";
    public $refreshButton = true;
    public $showPanel = true;
    public $customSearch = ['visible' => false, 'attribute' => '', 'placeholder' => ''];
    public $formatter = [
        'class' => 'yii\i18n\Formatter',
        'nullDisplay' => '',
    ];

    public $panelFooterTemplate = <<< HTML
    <div class="kv-panel-pager">
        {pager} {summary}
    </div>
    {footer}
    <div class="clearfix"></div>
HTML;

    public $toolbar = [
        '{export}',
        '{toggleData}'
    ];

    protected function initPanel()
    {
        if($this->showPanel) {
            if (!is_array($this->panel) || empty($this->panel)) {
                $this->panel = [];
            }

            $type = ArrayHelper::getValue($this->panel, 'type', null);
            if ($type == null) {
                $this->panel['type'] = MyKartikGridView::TYPE_PRIMARY;
            }

            $heading = ArrayHelper::getValue($this->panel, 'heading', null);
            if ($heading == null) {
                $this->panel['heading'] = $this->getView()->title;
            }
        }

        //Set Export label
        if (!is_array($this->export) || empty($this->export)) {
            $this->export = [];
        }

        $export_label = ArrayHelper::getValue($this->export, 'label', null);
        if ($export_label == null) {
            $this->export['label'] = $this->exportLabel;
        }
        //Set Export label

        return parent::initPanel();
    }

    private function getMyRefreshGridId(): string
    {
        return $this->options['id'] . $this->myRefreshGridId;
    }

    protected function renderToolbar()
    {
        if (!is_array($this->toolbar)) {
            $tmp = $this->toolbar;
            $this->toolbar = [];
            if (!empty($tmp) && is_string($tmp)) {
                $this->toolbar[] = $tmp;
            }
        }
        if (!in_array('{export}', $this->toolbar)) {
            $this->toolbar[] = '{export}';
        }
        if (in_array('{toggleData}', $this->toolbar)) {
            $key = array_search('{toggleData}', $this->toolbar);
            if($key!==false){
                unset($this->toolbar[$key]);
            }
        }

        $toolbar = parent::renderToolbar();

        $refresh = '<script>
                function gridRefreshButtonClicked() {
                    $.pjax.reload({container:\'#' . $this->getMyRefreshGridId() . '\'});
                    listen_my_modal_form_link();
                }  
        </script>';
        if ($this->refreshButton) {
            $refresh .= '<button id="gridRefreshButton" class="btn btn-default" onclick="gridRefreshButtonClicked()">
                <i class="fa fa-refresh"></i> Refresh
            </button>';
        }

        $createButton = '';
        if ($this->createButton['visible']) {
            $modal_class = '';
            if ($this->createButton['modal']) {
                $modal_class = ' show_modal_form';
            }
            $createButton = '<a class="btn btn-default always_open_link' . $modal_class . '" data-grid-id="' . $this->myRefreshGridId . '-pjax" href="' . $this->createButton['url'] . '"><i class="fa fa-plus-circle"></i> ' . $this->createButton['label'] . '</a>';
        }

        return $createButton . $refresh . $toolbar;
    }

    public function getClassFullName(){
        /* @var $dProvider \yii\data\ActiveDataProvider */
        $dProvider = $this->dataProvider;
        $modelClassName = $this->dataProvider->query->modelClass;

        return $modelClassName;
    }

    public function getClassName(){
        $modelClassName = $this->getClassFullName();
        $modelClassName = explode("\\", $modelClassName);
        $modelClassName = end($modelClassName);

        return $modelClassName;
    }

    protected function renderToolbarContainer()
    {
        $toolbar = parent::renderToolbarContainer();
        $search = '';
        $customsearch = '';
        $hiddenParams = '';
        $className = $this->getClassName();
        if(isset($_GET[$className])) {
            foreach ($_GET[$className] as $key => $value) {
                $hiddenParams .= "<input type='hidden' name='{$className}[{$key}]' value='$value'/>";
            }
        }

        $modelClassName = $this->getClassName();
        if($this->searchField) {
            $attribute = "_search";

            $val = '';
            if (isset($_GET[$modelClassName][$attribute])) {
                $val = $_GET[$modelClassName][$attribute];
            }

            $search = "
            <form method='get'>
            {$hiddenParams}
            <div class='btn-toolbar kv-grid-toolbar toolbar-container pull-left' style='margin-left: 5px;'>
                <span style='font-size: 10px;' class='text-info'>{$this->searchFieldPlaceholder}</span><br>
                <div class=\"input-group input-group-grid-search\">
                    <input autocomplete='off' value='{$val}' type='text' name='{$modelClassName}[{$attribute}]' id='{$modelClassName}[{$attribute}]' class='form-control' placeholder='{$this->searchFieldPlaceholder}'>
                    <span class=\"input-group-addon\"><i class=\"fa fa-search\"></i></span>
                </div>
            </div>
            </form>";
        }

        if($this->customSearch['visible']) {
            $attribute = $this->customSearch['attribute'];

            $val = '';
            if (isset($_GET[$modelClassName][$attribute])) {
                $val = $_GET[$modelClassName][$attribute];
            }

            $customsearch = "
            <form method='get'>
            {$hiddenParams}
            <div class='btn-toolbar kv-grid-toolbar toolbar-container pull-left' style='margin-left: 5px;'>
                <span style='font-size: 10px;' class='text-info'>{$this->customSearch['placeholder']}</span><br>
                <div class=\"input-group input-group-grid-search\">
                    <input autocomplete='off' value='{$val}' type='text' name='{$modelClassName}[{$attribute}]' id='{$modelClassName}[{$attribute}]' class='form-control' placeholder='{$this->customSearch['placeholder']}'>
                    <span class=\"input-group-addon\"><i class=\"fa fa-search\"></i></span>
                </div>
            </div>
            </form>";
        }

        return $search . $customsearch . $toolbar;
    }

    public function run()
    {
        if (!is_array($this->pager) || empty($this->pager)) {
            $this->pager = [];
        }
        $firstPageLabel = ArrayHelper::getValue($this->pager, 'firstPageLabel', null);
        if($firstPageLabel == null){
            $this->pager['firstPageLabel'] = 'First';
        }
        $lastPageLabel = ArrayHelper::getValue($this->pager, 'lastPageLabel', null);
        if($lastPageLabel == null){
            $this->pager['lastPageLabel'] = 'Last';
        }
        $maxButtonCount = ArrayHelper::getValue($this->pager, 'maxButtonCount', null);
        if($maxButtonCount == null){
            $this->pager['maxButtonCount'] = 5;
        }

        if(is_array($this->topFilterColumns) && count($this->topFilterColumns) > 0) {
            if(!$this->filterModel){
                $tmpClass = $this->getClassFullName();
                $this->filterModel = new $tmpClass;
            }

            foreach ($this->columns as $column) {
                if ($column instanceof \yii\grid\DataColumn && !in_array($column->attribute, $this->topFilterColumns, true)){
                    $column->filter = false;
                }
            }
        }

        return parent::run(); // TODO: Change the autogenerated stub
    }
}