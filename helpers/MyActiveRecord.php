<?php

namespace app\helpers;

use ReflectionClass;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;

class MyActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @param int $pageSize
     * @param string $orderBy
     * @param ConditonBuilder|null $condition
     * @param string $groupBy
     * @param array $with e.g. ['user', 'permission.role'] is turned to ->with('user', 'permission.role)
     * @return ActiveDataProvider
     * @author Eddilber Macharia (edd.cowan@gmail.com)<eddmash.com>
     */
    public static function searchModel($pageSize = 50, $orderBy = '', ConditonBuilder $condition = null, $groupBy = '',
                                       array $with = null, ActiveQuery $query=null)
    {
        /* @var $condition ConditonBuilder */

        if(!$query) {
            $query = static::find();
        }
        if ($with) {
            $query = $query->with(...$with);
        }

        if ($condition) {
            $query->where($condition->getConditionString(), $condition->getConditionParams());
        }
        $query->orderBy($orderBy);
        $query->groupBy($groupBy);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        return $dataProvider;
    }

    public static function getDropDownData($value_column, $label_column, $orderBy = '', $condition = '', $params = [],
                                           $add_default = false, $concat_field = '')
    {
        $data = [];
        if ($add_default) {
            $data[''] = 'Select One';
        }

        $tmp = self::find()->where($condition, $params)->orderBy($orderBy)->all();
        foreach ($tmp as $record) { 
            if($concat_field !=''){
                $data[$record->{$value_column}] = $record->{$label_column}.' - '.$record->{$concat_field};
            }else{
                $data[$record->{$value_column}] = $record->{$label_column};
            }
            
        }

        return $data;
    }

    /**
     * Use this when performing ['in', 'col', 'val'] else use @see MyActiveRecord::getDropDownData()
     * @param $value_column
     * @param $label_column
     * @param string $orderBy
     * @param string $conditionInQuery
     * @param bool $add_default
     * @return array
     * @author Eddilber Macharia (edd.cowan@gmail.com)<eddmash.com>
     */
    public static function getDropDownDataInQuery($value_column, $label_column, $orderBy = '', $conditionInQuery = '',
                                                  $add_default = false)
    {
        $data = [];
        if ($add_default) {
            $data[''] = 'Select One';
        }

        $tmp = self::find()->where($conditionInQuery)->orderBy($orderBy)->all();
        foreach ($tmp as $record) {
            $data[$record->{$value_column}] = $record->{$label_column};
        }

        return $data;
    }

    public function loadAll($data, $formName = null)
    {
        $scope = $formName === null ? $this->formName() : $formName;
        if ($scope === '' && !empty($data)) {
            $this->setAttributesAll($data);

            return true;
        } elseif (isset($data[$scope])) {
            $this->setAttributesAll($data[$scope]);

            return true;
        }

        return false;
    }

    public function setAttributesAll($values, $safeOnly = true)
    {
        if (is_array($values)) {
            $attributes = array_flip($this->attributesAll());
            foreach ($values as $name => $value) {
                if (isset($attributes[$name])) {
                    $this->$name = $value;
                } elseif ($safeOnly) {
                    $this->onUnsafeAttribute($name, $value);
                }
            }
        }
    }

    public function attributesAll()
    {
        $class = new ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }

        return array_merge($names, $this->attributes());
    }

    public static function model()
    {
        return new static();
    }

    public function get($primaryKeyValue, $columnToGet)
    {
        $record = static::findOne($primaryKeyValue);

        if ($record) {
            return $record->{$columnToGet};
        }

        return null;
    }

    public function getDataAsArray()
    {
        $attributes = $this->attributes();
        $data = [];

        foreach ($attributes as $attribute) {
            $data[$attribute] = $this->{$attribute};
        }

        return $data;
    }

    public function getScalar($column, $conditions = '', $params = []) {
        $query = new Query();
        $query->select($column)
            ->from($this->tableName())
            ->where($conditions, $params)
            ->limit(1);
           // ->queryScalar();
    }
}
