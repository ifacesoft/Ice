<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Code\Generator\Action as CodeGenerator_Action;

class Code_Generator extends Action
{

    /**
     * Action config
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    protected static function config()
    {
        return [
            'view' => ['template' => ''],
            'actions' => [],
            'input' => [
                'baseClass',
                'class',
            ],
            'output' => [],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'access' => [
                'roles' => [],
                'request' => 'cli',
                'env' => null
            ]
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     * @throws \Ice\Core\Exception
     */
    public function run(array $input)
    {
        switch ($input['baseClass']) {
            case 'action':
                CodeGenerator_Action::getInstance(Action::getClass($input['class']))->generate();
                break;
            case 'view':
                break;
            default;
        }
    }
}