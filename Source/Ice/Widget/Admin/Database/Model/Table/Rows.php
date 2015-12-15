<?php

namespace Ice\Widget;

use Ice\Core\Config;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Core\Security;
use Ice\Exception\Http_Forbidden;
use Ice\Exception\Http_Not_Found;

class Admin_Database_Model_Table_Rows extends Table_Rows
{

    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Table_Rows::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'schemeName' => ['providers' => 'router'],
                'tableName' => ['providers' => 'router']
            ],
            'output' => [],
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     * @throws Http_Forbidden
     * @throws Http_Not_Found
     */
    protected function build(array $input)
    {
        $module = Module::getInstance();

        $currentDataSourceKey = $module->getDataSourceKeys()[$input['schemeName']];

        $config = Config::getInstance(Admin_Database_Database::getClass());

        if (!isset($config->gets()[$currentDataSourceKey])) {
            throw new Http_Not_Found(['Scheme {$0} not found', $currentDataSourceKey]);
        }

        $scheme = Config::create($currentDataSourceKey, $config->gets()[$currentDataSourceKey]);

        $security = Security::getInstance();

        if (!$security->check($scheme->gets('roles'))) {
            throw new Http_Forbidden('Access denied: scheme not allowed');
        }

        /** @var Model $modelClass */
        $modelClass = $this->getInstanceKey();

        $currentTableName = $modelClass::getTableName();

        if (!$scheme->gets('tables/' . $currentTableName)) {
            throw new Http_Not_Found(['Table {$0} not found', $currentTableName]);
        }

        $table = Config::create($currentTableName, $scheme->gets('tables/' . $currentTableName));

        if (!$security->check($table->gets('roles'))) {
            throw new Http_Forbidden('Access denied: table not allowed');
        }

        $pkFieldName = $modelClass::getPkFieldName();

        $this->a(
            $pkFieldName,
            [
                'route' => 'ice_admin_database_row',
                'name' => 'pk',
                'params' => $input
            ]
        );

        foreach ($table->gets('columns') as $columnName => $column) {
            $column = $table->getConfig('columns/' . $columnName);

            $this->span(
                $columnName,
                array_merge(['access' => ['roles' => $column->gets('roles')]], $column->gets('options', false))
            );
        }
    }
}