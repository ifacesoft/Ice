<?php

return [
    'default' => 'Ice\Render\Php',
    'Ice\Render\Cli' => [],
    'Ice\Render\Json' => [],
    'Ice\Render\Php' => [],
    'Ice\Render\Replace' => [],
    'Ice\Render\Smarty' => [
        'dataProviderKey' => 'Ice:Registry/render',
        'vendor' => 'smarty/smarty',
        'templates_c' => 'smarty/templates_c/',
        'plugins' => [VENDOR_DIR . 'Smarty/plugins']
    ],
    'Ice\Render\Twig' => [
        'vendor' => 'twig/twig',
        'cache' => 'twig/cache/',
    ],
];