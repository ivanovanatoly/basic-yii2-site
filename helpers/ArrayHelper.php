<?php

namespace app\helpers;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
    public static function aggregate($array, $columns)
    {
        if (empty($columns)) {
            return [];
        }

        $initial = [];
        foreach ($columns as $column) {
            $initial[$column] = 0;
        }

        return array_reduce($array, function ($aggregates, \ArrayAccess $item) {
            foreach ($aggregates as $key => &$aggregate) {
                if (isset($item[$key]) && is_numeric($item[$key])) {
                    $aggregate =+ $item[$key];
                }
            }
            return $aggregates;
        }, $initial);
    }
}
