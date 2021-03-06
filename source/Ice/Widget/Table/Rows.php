<?php
namespace Ice\Widget;

use Ice\Core\Exception;
use Ice\Core\QueryBuilder;
use Ice\Core\QueryResult;
use Ice\Core\Widget;
use Ice\WidgetComponent\Table_Row_A;
use Ice\WidgetComponent\Table_Row_Checkbox;
use Ice\WidgetComponent\Table_Row_Td;

class Table_Rows extends Widget
{
    private $isShowCount = false;
    private $columnCount = 0;

    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => __CLASS__, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => ''],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
    }

    /**
     * @param boolean $isShowCount
     * @return Table_Rows
     */
    public function setShowCount($isShowCount = true)
    {
        $this->isShowCount = $isShowCount;

        return $this;
    }

    public function setQueryResult(QueryResult $queryResult)
    {
        parent::setQueryResult($queryResult);

        $queryBuilder = $queryResult->getQuery()->getQueryBuilder();

        $limitQueryPart = $queryBuilder->getSqlParts()[QueryBuilder::PART_LIMIT];

        $limit = $this->getAll('limit');

        if (!$limit) {
            $limit = $limitQueryPart['limit'];
        }

        $page = $this->getAll('page');

        $offset = $page
            ? $limit * ($page - 1)
            : $limitQueryPart['offset'];

        if ($limit && $limit < $queryResult->getNumRows()) {
            $this->setRows(array_slice($queryResult->getRows(), $offset, $limit));
        } else {
            $this->setRows($queryResult->getRows());
        }

        $this->setOffset($offset);
    }

    /**
     * Build checkbox tag part
     *
     * @param $columnName
     * @param  array $options
     * @param null $template
     * @return $this
     * @throws Exception
     */
    public function checkbox($columnName, array $options = [], $template = null)
    {
        return $this->addPart(new Table_Row_Checkbox($columnName, $options, $template, $this));
    }

    /**
     * Build span tag part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     * @throws Exception
     */
    public function td($columnName, array $options = [], $template = null)
    {
        if (isset($options['route']) || isset($options['href'])) {
            return $this->a($columnName, $options, $template);
        }
        // todo: шаблон должен быть td, пока так
        return $this->addPart(new Table_Row_Td($columnName, $options, $template, $this));
    }

    /**
     * Build a tag part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     * @throws Exception
     */
    public function a($columnName, array $options = [], $template = null)
    {
        // todo: шаблон должен быть td, пока так
        return $this->addPart(new Table_Row_A($columnName, $options, $template, $this));
    }

    /**
     * Build checkbox tag part
     *
     * @param $columnName
     * @param  array $options
     * @param null $template
     *
     * @deprecated 1.3 Use td
     *
     * @return $this
     * @throws Exception
     */
    public function span($columnName, array $options = [], $template = null)
    {
        return $this->td($columnName, $options, $template);
    }

    /**
     * @param $columnName
     * @param array $options
     * @param null $template
     * @return $this
     * @throws Exception
     */
    public function number($columnName, array $options = [], $template = null)
    {
        return $this->td($columnName, $options, $template);
    }

    /**
     * @param $columnName
     * @param array $options
     * @param null $template
     * @return $this
     * @throws Exception
     */
    public function text($columnName, array $options = [], $template = null)
    {
        return $this->td($columnName, $options, $template);
    }

    /**
     * @param $columnName
     * @param array $options
     * @param null $template
     * @return $this
     * @throws Exception
     */
    public function date($columnName, array $options = [], $template = null)
    {
        return $this->td($columnName, $options, $template);
    }

    /**
     * @param $columnName
     * @param array $options
     * @param null $template
     * @return $this
     * @throws Exception
     */
    public function oneToMany($columnName, array $options = [], $template = null)
    {
        return $this->td($columnName, $options, $template);
    }

    /**
     * @param $columnName
     * @param array $options
     * @param null $template
     * @return $this
     * @throws Exception
     */
    public function manyToMOne($columnName, array $options = [], $template = null)
    {
        return $this->td($columnName, $options, $template);
    }

    /**
     * @param $columnName
     * @param array $options
     * @param null $template
     * @return $this
     * @throws Exception
     */
    public function manyToMany($columnName, array $options = [], $template = null)
    {
        $options['valueKey'] = $columnName . '_titles';

        return $this->td($columnName, $options, $template);
    }

    /**
     * @param $columnName
     * @param array $options
     * @param null $template
     * @return $this
     * @throws Exception
     */
    public function oneToManyToMany($columnName, array $options = [], $template = null)
    {
        return $this->td($columnName, $options, $template);
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        return [];
    }

    protected function getCompiledResult()
    {
        return array_merge(
            parent::getCompiledResult(),
            [
                'isShowCount' => $this->isShowCount(),
                'columnCount' => $this->getColumnCount()
            ]
        );
    }

    /**
     * @return boolean
     */
    public function isShowCount()
    {
        return $this->isShowCount;
    }

    /**
     * @return int
     */
    public function getColumnCount()
    {
        return $this->columnCount ? $this->columnCount : count($this->getParts());
    }

    /**
     * @param int $columnCount
     * @return Table_Rows
     */
    public function setColumnCount($columnCount)
    {
        $this->columnCount = $columnCount;
        return $this;
    }
}