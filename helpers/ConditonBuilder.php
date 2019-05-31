<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/23/18
 * Time: 12:19 PM
 */

namespace app\helpers;


class ConditonBuilder
{
    private $condition = [];
    private $params = [];

    public static function instance()
    {
        return new self();
    }

    public function addCondition(string $sql, array $params): self
    {
        if ($this->condition) {
            $this->condition[] = "AND";
        }
        $this->condition[] = $sql;

        $this->params = array_merge($this->params, $params);
        return $this;
    }


    public function getConditionString(): string
    {
        return implode(' ', $this->condition);
    }

    public function getConditionParams(): array
    {
        return $this->params;
    }

}