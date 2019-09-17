<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Data_Scheme;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Exception\DataSource_TableNotFound;
use Ice\Helper\Arrays;
use Ice\Helper\Json;
use Ice\Model\Scheme;

class Orm_Sync_DataScheme extends Action
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
            'view' => ['template' => '', 'viewRenderClass' => null],
            'actions' => [],
            'input' => ['force' => ['default' => 0]],
            'output' => [],
            'ttl' => -1,
            'roles' => []
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
        $module = Module::getInstance();

        $dataSchemeTables = Data_Scheme::getTables($module);

        foreach ($module->getDataSourceTables() as $dataSourceKey => $tables) {
            try {
                $schemes = Scheme::createQueryBuilder()->getSelectQuery('*', [], $dataSourceKey)->getRows();
            } catch (DataSource_TableNotFound $e) {
                Scheme::createTable($dataSourceKey);
                $schemes = [];
            }

            $schemeTables = &$dataSchemeTables[$dataSourceKey];

            foreach ($tables as $tableName => $table) {
                if (!isset($schemeTables[$tableName])) {
                    if (!array_key_exists($tableName, $schemes)) {
                        $this->createModel(
                            $module->getModelClass($tableName, $dataSourceKey),
                            $table,
                            $input['force'],
                            $dataSourceKey
                        );
                    }

                    continue;
                }

                $isModelSchemeUpdated = $this->updateModelScheme(
                    $table['scheme'],
                    $schemeTables[$tableName]['scheme'],
                $tableName,
                    $schemeTables[$tableName]['modelClass'],
                    $dataSourceKey
                );

                $isModelIndexesUpdated = $this->updateModelIndexes(
                    $table['indexes'],
                    $schemeTables[$tableName]['indexes'],
                    $tableName,
                    $schemeTables[$tableName]['modelClass'],
                    $dataSourceKey
                );

                $isModelReferencesUpdated = $this->updateModelReferences(
                    $table['references'],
                    $schemeTables[$tableName]['references'],
                    $tableName,
                    $schemeTables[$tableName]['modelClass'],
                    $dataSourceKey
                );

                $isModelRelationsOneToManyUpdated = $this->updateModelRelationsOneToMany(
                    $table['relations']['oneToMany'],
                    $schemeTables[$tableName]['relations']['oneToMany'],
                    $schemeTables[$tableName]['modelClass'],
                    $schemeTables,
                    $module,
                    $dataSourceKey
                );

                $isModelRelationsManyToOneUpdated = $this->updateModelRelationsManyToOne(
                    $table['relations']['manyToOne'],
                    $schemeTables[$tableName]['relations']['manyToOne'],
                    $schemeTables[$tableName]['modelClass'],
                    $schemeTables,
                    $module,
                    $dataSourceKey
                );

                $isModelRelationsManyToManyUpdated = $this->updateModelRelationsManyToMany(
                    $table['relations']['manyToMany'],
                    $schemeTables[$tableName]['relations']['manyToMany'],
                    $schemeTables[$tableName]['modelClass'],
                    $schemeTables,
                    $module,
                    $dataSourceKey
                );

                $isModelFieldsUpdated = false;

                $dataSchemeColumns = $schemeTables[$tableName]['columns'];

                foreach ($table['columns'] as $columnName => $column) {
                    if (!isset($schemeTables[$tableName]['columns'][$columnName])) {
                        $this->createModuleField(
                            $schemeTables[$tableName]['columns'][$columnName],
                            $column['scheme'],
                            $schemeTables[$tableName]['modelClass'],
                            $dataSourceKey
                        );
                        $isModelFieldsUpdated = true;
                        continue;
                    }

                    $isModelFieldUpdated = $this->updateModelField(
                        $column['scheme'],
                        $schemeTables[$tableName]['columns'][$columnName]['scheme'],
                        $schemeTables[$tableName]['columns'][$columnName]['fieldName'],
                        $schemeTables[$tableName]['modelClass'],
                        $dataSourceKey
                    );

                    if (!$isModelFieldsUpdated) {
                        $isModelFieldsUpdated = $isModelFieldUpdated;
                    }

                    unset($dataSchemeColumns[$columnName]);
                }

                foreach ($dataSchemeColumns as $columnName => $column) {
                    Data_Scheme::getLogger()->info([
                        'Remove field {$0} for model {$1}',
                        [$column['fieldName'], $schemeTables[$tableName]['modelClass']]
                    ]);
                    unset($schemeTables[$tableName]['columns'][$columnName]);
                    $isModelFieldsUpdated = true;
                }

                if ($isModelFieldsUpdated) {
                    Scheme::createQueryBuilder()
                        ->pk($tableName)
                        ->updateQuery(['columns__json' => Json::encode($table['columns'])], $dataSourceKey)
                        ->getQueryResult();
                }

                $isUpdated = $isModelSchemeUpdated ||
                    $isModelIndexesUpdated ||
                    $isModelReferencesUpdated ||
                    $isModelRelationsOneToManyUpdated ||
                    $isModelRelationsManyToOneUpdated ||
                    $isModelRelationsManyToManyUpdated ||
                    $isModelFieldsUpdated;

                if ($isUpdated) {
                    Model::getCodeGenerator()->generate($schemeTables[$tableName]['modelClass'], $table, $input['force']);
                }

                unset($schemeTables[$tableName]);
            }
        }

        foreach ($dataSchemeTables as $dataSourceKey => $schemeTables) {
            $schemes = Scheme::createQueryBuilder()->getSelectQuery('*', [], $dataSourceKey)->getRows();

            foreach ($schemeTables as $tableName => $table) {
                if (array_key_exists($tableName, $schemes)) {
                    $this->deleteModel(
                        $module->get(Module::SOURCE_DIR) . $table['modelPath'],
                        $tableName,
                        $schemeTables,
                        $dataSourceKey
                    );
                }
            }
        }
    }

    private function createModel($modelClass, $table, $force, $dataSourceKey)
    {
        Model::getCodeGenerator()->generate($modelClass, $table, $force);

        Scheme::createQueryBuilder()->getInsertQuery(
            [
                'table_name' => $table['scheme']['tableName'],
                'table__json' => Json::encode($table['scheme']),
                'columns__json' => Json::encode($table['columns']),
                'indexes__json' => Json::encode($table['indexes']),
                'references__json' => Json::encode($table['references']),
                'revision' => $table['revision']
            ],
            true,
            $dataSourceKey
        )->getQueryResult();

        Data_Scheme::getLogger()->info(['{$0}: Model {$1} successfully created', [$dataSourceKey, $modelClass]]);
    }

    private function deleteModel($modelFilePath, $tableName, $schemeTables, $dataSourceKey)
    {
        if (file_exists($modelFilePath)) {
            unlink($modelFilePath);
        }

        Scheme::createQueryBuilder()->deleteQuery($tableName, $dataSourceKey)->getQueryResult();

        Data_Scheme::getLogger()->info(
            ['{$0}: Model {$1} successfully deleted', [$dataSourceKey, $schemeTables[$tableName]['modelClass']]]
        );
    }

    private function updateModelScheme(array $tableScheme, array &$modelScheme, $tableName, $modelClass, $dataSourceKey)
    {
        $tableSchemeJson = Json::encode($tableScheme);

        if (crc32($tableSchemeJson) == crc32(Json::encode($modelScheme))) {
            return false;
        }

        $diffScheme = Json::encode(array_diff($tableScheme, $modelScheme));

        $modelScheme = $tableScheme;

        Scheme::createQueryBuilder()
            ->pk($tableName)
            ->updateQuery(['table__json' => $tableSchemeJson], $dataSourceKey)
            ->getQueryResult();

        Data_Scheme::getLogger()->info([
            '{$0}: Scheme of model {$1} successfully updated: {$2}',
            [$dataSourceKey, $modelClass, $diffScheme]
        ]);

        return true;
    }

    private function updateModelIndexes(
        array $tableIndexes,
        array &$modelIndexes,
        $tableName,
        $modelClass,
        $dataSourceKey
    )
    {
        $tableIndexesJson = Json::encode($tableIndexes);

        if (crc32($tableIndexesJson) == crc32(Json::encode($modelIndexes))) {
            return false;
        }

        $addedDiffIndexes = Json::encode(Arrays::diffRecursive($tableIndexes, $modelIndexes));
        $removedDiffIndexes = Json::encode(Arrays::diffRecursive($tableIndexes, $tableIndexes));

        $modelIndexes = $tableIndexes;

        Scheme::createQueryBuilder()
            ->pk($tableName)
            ->updateQuery(['indexes__json' => $tableIndexesJson], $dataSourceKey)
            ->getQueryResult();

        Data_Scheme::getLogger()->info([
            '{$0}: Indexes of model {$1} successfully updated! [added: {$2}; removed: {$3}]',
            [$dataSourceKey, $modelClass, $addedDiffIndexes, $removedDiffIndexes]
        ]);

        return true;
    }

    private function updateModelReferences(
        array $tableReferences,
        array &$modelReferences,
        $tableName,
        $modelClass,
        $dataSourceKey
    )
    {
        $tableReferencesJson = Json::encode($tableReferences);

        if (crc32($tableReferencesJson) == crc32(Json::encode($modelReferences))) {
            return false;
        }

        $addedDiffReferences = Json::encode(Arrays::diffRecursive($tableReferences, $modelReferences));
        $removedDiffReferences = Json::encode(Arrays::diffRecursive($modelReferences, $tableReferences));

        $modelReferences = $tableReferences;

        Scheme::createQueryBuilder()
            ->pk($tableName)
            ->updateQuery(['references__json' => $tableReferencesJson], $dataSourceKey)
            ->getQueryResult();

        Data_Scheme::getLogger()->info([
            '{$0}: References of model {$1} successfully updated! [added: {$2}; removed: {$3}]',
            [$dataSourceKey, $modelClass, $addedDiffReferences, $removedDiffReferences]
        ]);

        return true;
    }

    private function updateModelRelationsOneToMany(
        array $tableOneToMany,
        array &$modelOneToMany,
        $modelClass,
        array $schemeTables,
        Module $module,
        $dataSourceKey
    )
    {
        $tableOneToManyJson = Json::encode($tableOneToMany);

        if (crc32($tableOneToManyJson) == crc32(Json::encode($modelOneToMany))) {
            return false;
        }

        $diffOneToMany = Json::encode(array_diff($tableOneToMany, $modelOneToMany));

        $relations = [];

        foreach ($tableOneToMany as $referenceTableName => $columnName) {
            $referenceClassName = isset($schemeTables[$referenceTableName])
                ? $schemeTables[$referenceTableName]['modelClass']
                : $module->getModelClass($referenceTableName, $dataSourceKey);

            $relations[$referenceClassName] = $columnName;
        }

        $modelOneToMany = $relations;

        Data_Scheme::getLogger()->info([
            '{$0}: OneToMany relations of model {$1} successfully updated: {$2}',
            [$dataSourceKey, $modelClass, $diffOneToMany]
        ]);

        return true;
    }

    private function updateModelRelationsManyToOne(
        array $tableManyToOne,
        array &$modelManyToOne,
        $modelClass,
        array $schemeTables,
        Module $module,
        $dataSourceKey
    )
    {
        $tableManyToOneJson = Json::encode($tableManyToOne);

        if (crc32($tableManyToOneJson) == crc32(Json::encode($modelManyToOne))) {
            return false;
        }

        $diffManyToOne = Json::encode(array_diff($tableManyToOne, $modelManyToOne));

        $relations = [];

        foreach ($tableManyToOne as $referenceTableName => $columnName) {
            $referenceClassName = isset($schemeTables[$referenceTableName])
                ? $schemeTables[$referenceTableName]['modelClass']
                : $module->getModelClass($referenceTableName, $dataSourceKey);

            $relations[$referenceClassName] = $columnName;
        }

        $modelManyToOne = $relations;

        Data_Scheme::getLogger()->info([
            '{$0}: ManyToOne relations of model {$1} successfully updated: {$2}',
            [$dataSourceKey, $modelClass, $diffManyToOne]
        ]);

        return true;
    }

    private function updateModelRelationsManyToMany(
        array $tableManyToMany,
        array &$modelManyToMany,
        $modelClass,
        array $schemeTables,
        Module $module,
        $dataSourceKey
    )
    {
        $tableManyToOneJson = Json::encode($tableManyToMany);

        if (crc32($tableManyToOneJson) == crc32(Json::encode($modelManyToMany))) {
            return false;
        }

        $diffManyToMany = Json::encode(array_diff($tableManyToMany, $modelManyToMany));

        $references = [];
        foreach ($tableManyToMany as $referenceTableName => $linkTableName) {
            $referenceClassName = isset($schemeTables[$referenceTableName])
                ? $schemeTables[$referenceTableName]['modelClass']
                : $module->getModelClass($referenceTableName, $dataSourceKey);

            $linkClassName = isset($schemeTables[$linkTableName])
                ? $schemeTables[$linkTableName]['modelClass']
                : $module->getModelClass($linkTableName, $dataSourceKey);

            $references[$referenceClassName] = $linkClassName;
        }

        $modelManyToMany = $references;

        Data_Scheme::getLogger()->info([
            '{$0}: ManyToMany relations of model {$1} successfully updated: {$2}',
            [$dataSourceKey, $modelClass, $diffManyToMany]
        ]);

        return true;
    }

    private function createModuleField(&$modelField, $modelFieldScheme, $modelClass, $dataSourceKey)
    {
        $modelField = ['scheme' => $modelFieldScheme];

        Data_Scheme::getLogger()->info([
            '{$0}: Field {$1} in model {$2} successfully created',
            [$dataSourceKey, $modelFieldScheme['fieldName'], $modelClass]
        ]);
    }

    private function updateModelField($tableField, &$modelField, $fieldName, $modelClass, $dataSourceKey)
    {
        $tableFieldJson = Json::encode($tableField);

        if (crc32($tableFieldJson) == crc32(Json::encode($modelField))) {
            return false;
        }

        $diffField = Json::encode(array_diff($tableField, $modelField));

        $modelField = $tableField;

        Data_Scheme::getLogger()->info([
            '{$0}: Field {$1} in model {$2} successfully updated: {$3}',
            [$dataSourceKey, $fieldName, $modelClass, $diffField]
        ]);

        return true;
    }
}