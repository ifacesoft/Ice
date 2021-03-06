<?php

namespace Ice\Widget;

use Ice\Core\Widget;
use Ice\WidgetComponent\HtmlTag;

abstract class Navbar extends Widget
{
    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Navbar
     */
    public function brand($name, array $options = [], $template = 'Ice\Widget\Navbar\Brand')
    {
        return $this->addPart(new HtmlTag($name, $options, $template, $this));
    }

    /**
     * @param string $name
     * @param array $options
     * @param string $template
     * @return Navbar
     */
    public function nav($name, array $options = [], $template = null)
    {
        $options['widget']->addClasses('navbar-nav');

        return $template
            ? $this->widget($name, $options, $template)
            : $this->widget($name, $options);
    }

    /**
     * @param string $name
     * @param array $options
     * @param string $template
     * @return Navbar
     */
    public function form($name, array $options = [], $template = null)
    {
        $options['widget']->addClasses('navbar-form');

        return $template
            ? $this->widget($name, $options, $template)
            : $this->widget($name, $options);
    }
}