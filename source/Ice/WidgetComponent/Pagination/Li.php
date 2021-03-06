<?php

namespace Ice\WidgetComponent;

use Ice\Core\Widget as Core_Widget;

class Pagination_Li extends HtmlTag
{
    public function __construct($componentName, array $options, $template, Core_Widget $widget)
    {
        $options['onclick'] = $this->getEvent();

        parent::__construct($componentName, $options, $template, $widget);
    }

    /**
     * WidgetComponent config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'WidgetComponent: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

}