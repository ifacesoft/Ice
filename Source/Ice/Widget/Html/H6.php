<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 04.08.16
 * Time: 11:04
 */

namespace Ice\Widget;

class Html_H6 extends Html_Div
{
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = __CLASS__;

        return $config;
    }

}