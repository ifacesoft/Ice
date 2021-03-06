<?php

namespace Ice\Widget;

use Ice\Action\Security_Password_Email_ChangePassword_Submit;
use Ice\Core\Model_Account;
use Ice\DataProvider\Request;

class Account_Password_Email_ChangePassword extends Account_Form
{
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => ''],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
    }

    protected function build(array $input)
    {
        $this
            ->widget(
                'header',
                [
                    'widget' => $this
                        ->getWidget(Header::class)
                        ->h1('Change password', ['valueResource' => true])
                ]
            )
            ->text(
                'password',
                [
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'password' => [
                            'providers' => [Request::class, 'default'],
                            'validators' => ['Ice:Length_Min' => 5]
                        ]
                    ]
                ]
            )
            ->password(
                'new_password',
                [
                    'required' => true,
                    'placeholder' => true,
                    'params' => [
                        'new_password' => [
                            'providers' => [Request::class, 'default'],
                            'validators' => ['Ice:Length_Min' => 5]
                        ]
                    ]
                ]
            )
            ->password(
                'confirm_password',
                [
                    'placeholder' => true,
                    'required' => true,
                    'params' => [
                        'confirm_password' => [
                            'providers' => [Request::class, 'default'],
                            'validators' => [
                                'Ice:Equal' => [
                                    'value' => $this->get('new_password'),
                                    'message' => 'Passwords must be equals'
                                ]
                            ]
                        ]
                    ]
                ]
            )
            ->divMessage()
            ->button(
                'change_password',
                [
                    'route' => 'ice_security_change_password_request',
                    'submit' => Security_Password_Email_ChangePassword_Submit::class
                ]
            );

        return [];
    }

    /**
     * @return Model_Account
     */
    public function getAccount()
    {
        return null;
    }
}