<?php

namespace Ice\WidgetComponent;

use Ice\Core\Widget as Core_Widget;
use Ice\Core\WidgetComponent;

class Widget extends WidgetComponent
{
    private $widget = null;

    public function __construct($componentName, array $options, $template, Core_Widget $widget)
    {
        parent::__construct($componentName, $options, $template, $widget);

        $this->widget = $widget->getWidget($options['widget']);

//        try {
//            Access::check($options['widget']::getConfig()->gets('access'));
//        } catch (Access_Denied $e) {
//            return $this;
//        }

        if ($this->widget->getResource() === null) {
            $this->widget->setResourceClass($this->getResource());
        }
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

    /**
     * @return Core_Widget
     */
    public function getWidget()
    {
        return $this->widget;
    }

    /**
     * @param $partName
     * @return WidgetComponent|Widget
     */
    public function getComponent($partName)
    {
        return $this->getWidget()->getPart($partName);
    }

    /**
     * @param $widget
     */
    public function setWidget($widget)
    {
        $this->widget = $widget;
    }
}