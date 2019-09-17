<?php

namespace Ice\Action;

use Ice\Core\Action;

class Code_Generator extends Action{

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
               'ttl' => -1,
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
     */
    public function run(array $input)
    {
        switch ($input['baseClass']) {
            case 'action':
                Action::getCodeGenerator()->generate(Action::getClass($input['class']));
                break;
            case 'view':
                break;
            default;
        }
    }
}