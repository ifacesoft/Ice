<?php
/**
 * @file Ice main config
 *
 * Sets default config params for ice application components
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @version 0.0
 * @since 0.0
 */

return [
    'Ice\Core\Model' => [
        'schemeColumnPlugins' => [
            'Ice\Core\Widget_Form',
            'Ice\Core\Validator',
            'Ice\Core\Widget_Data',
        ]
    ],
    'Ice\Core\Environment' => [
        'environments' => [
            '/localhost/' => 'development',
            '/.*/' => 'development'
        ]
    ],
    'Ice\Core\Logger' => [
        'apiHost' => 'http://iceframework.net'
    ],
    'Ice\Core\Security' => [
        'userModelClass' => 'Ice\Model\User'
    ],
    'Ice\Core\Request' => [
        'multiLocale' => 1,
        'locale' => 'en',
//        'locale' => [
//            'language' => 'en',
//            'region' => 'Ru',
//            'encoding' => 'UTF-8'
//        ],
        'cors' => [
//            'host' => [
//                'methods' => [], // Permitted types of request ('POST', 'OPTIONS')
//                'headers' => [], // Describe custom headers ('Origin', 'X-Requested-With', 'Content-Range', 'Content-Disposition', 'Content-Type')
//                'cookie' => 'true' // Allow cookie
//            ]
        ]
    ],
    'Ice\Helper\Api_Client_Yandex_Translate' => [
        'translateKey' => 'trnsl.1.1.20150207T134028Z.19bab9f8d9228706.89067e4f90535d4a934a39fbaf284d8af968c9a9'
    ],
    'defaults' => [
        'Ice\Core\Route' => [
            'params' => [],
            'weight' => 0,
            'request' => []
        ]
    ],
    'Ice\Message\Transport\PHPMailer' => [
        'default' => [
            'debug' => '0',
            'smtpHost' => null, // required
            'smtpPort' => null, // required
            'smtpUser' => null, // required
            'smtpPass' => null, // required
            'fromAddress' => 'robot@iceframework.net',
            'fromName' => 'ice robot :)',
            'replyTo' => null, //['null@iceframework.net' => '/dev/null'],
            'cc' => null,
            'bcc' => null
        ]
    ]
];