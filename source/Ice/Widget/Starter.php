<?php

namespace Ice\Widget;

use Ice\Action\Header;

class Starter extends Block
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => null, 'class' => 'Ice:Php', 'layout' => 'div.container>div.starter-template', 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => ''],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'resource' => ['js' => null, 'css' => true, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     * @throws \Ice\Core\Exception
     */
    protected function build(array $input)
    {
        $text = 'Use this document as a way to quickly start any new project.<br>' .
            'All you get is this text and a mostly barebones HTML document.';
        $this
            ->widget('header', ['widget' => $this->getWidget(Header::getClass())->h1('Bootstrap starter template')])
            ->p('text', ['classes' => 'lead', 'label' => $text]);
    }
}