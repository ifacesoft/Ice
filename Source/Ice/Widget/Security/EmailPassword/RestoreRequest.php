<?php

namespace Ice\Widget;

use Ice\Action\Security_EmailPassword_RestoreRequest_Submit;
use Ice\Core\Widget_Security;

class Security_EmailPassword_RestoreRequest extends Widget_Security
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        $this
            ->setRedirect('ice_main', 1000)
            ->setHorizontal()
            ->text('email')
            ->div('ice-message', ['label' => '&nbsp;'])
            ->button(
                'request',
                [
                    'classes' => 'btn-primary',
                    'submit' => [
                        'action' => Security_EmailPassword_RestoreRequest_Submit::class,
                        'url' => 'ice_security_restore_request'
                    ]
                ]
            );
    }
}