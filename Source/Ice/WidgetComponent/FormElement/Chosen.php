<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 15.04.16
 * Time: 11:56
 */

namespace Ice\WidgetComponent;

class FormElement_Chosen extends Form_ListBox
{
    /**
     * WidgetComponent config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => __CLASS__, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'WidgetComponent: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }
}