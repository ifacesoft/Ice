<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 16.12.15
 * Time: 21:16
 */

namespace Ice\Action;

use Ice\Core\Debuger;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Exception\Error;
use Ice\Widget\Admin_Database_Form;

class Admin_Database_Form_Submit extends Widget_Event
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'view' => ['template' => ''],
            'access' => ['roles' => 'ROLE_ICE_ADMIN', 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [
                'widget' => ['default' => null, 'providers' => 'request'],
                'widgets' => ['default' => [], 'providers' => ['default', 'request']],
            ],
            'output' => []
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Admin_Database_Form $form */
        $form = $input['widget'];

        try {
            $form->validate();

            $module = Module::getInstance();

            $currentDataSourceKey = $module->getDataSourceKeys()[$form->getValue('schemeName')];

            /** @var Model $modelClass */
            $modelClass = Module::getInstance()->getModelClass($form->getValue('tableName'), $currentDataSourceKey);

            $pkFieldName = $modelClass::getPkFieldName();

            $model = $modelClass::create();

            $model->set($pkFieldName, $form->getValue($pkFieldName));

            $manyToMany = [];

            foreach ($form->getParts() as $partName => $part) {
                $value = $form->getValue($partName);

                if ($value === null) {
                    continue;
                }

                if (isset($part['options']['manyToMany'])) {
                    $manyToMany[$partName] = $value;
                } else {
                    $model->set($partName, $value);
                }
            }

            $model->save();

            return [
                'success' => $form->getLogger()->info('Запись сохранена', Logger::SUCCESS),
//                'redirect' => ['ice_admin_database_table' , ['schemeName' => $form->getValue('schemeName'), 'tableName' => $form->getValue('tableName')]]
            ];
        } catch (\Exception $e) {
            return [
                'error' => $this->getLogger()->info(['Сохранение не удалось: {$0}', $e->getMessage()], Logger::DANGER)
            ];
        }
    }
}