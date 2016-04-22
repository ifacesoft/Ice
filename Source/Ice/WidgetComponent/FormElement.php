<?php

namespace Ice\WidgetComponent;

use Ice\Core\Debuger;
use Ice\Core\Model;
use Ice\Core\QueryBuilder;
use Ice\Core\Request;
use Ice\Core\Security;
use Ice\Core\Widget as Core_Widget;
use Ice\DataProvider\Router;
use Ice\Helper\Input;

class FormElement extends ValueElement
{
    private $validators = null;
    private $name = null;
    private $horizontal = null;

    /**
     * WidgetComponent config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'WidgetComponent: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    public function __construct($componentName, array $options, $template, Core_Widget $widget)
    {
        parent::__construct($componentName, $options, $template, $widget);

        $this->horizontal = $widget->getOption('horizontal', 0);
    }

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->name !== null) {
            return $this->name;
        }

        return $this->name = $this->getOption('name') ? $this->getOption('name') : $this->getComponentName();
    }

    public function build(array $row)
    {
        /** @var FormElement $component */
        $component = parent::build($row);

        return $component
            ->buildValidators();
    }

    protected function buildParams($values)
    {
        parent::buildParams($values);

        $name = $this->getName();

        if (!isset($this->params[$name])) {
            $this->params[$name] = $this->getFromProviders($this->getName(), $values);
        }

//        if (!isset($this->params[$name])) {
//            $this->params[$name] = $this->value == $this->getComponentName()
//                ? (array_key_exists($this->value, $values) ? $values[$this->value] : null)
//                : (array_key_exists($this->value, $values) ? $values[$this->value] : $this->value);
//        }
    }

    /**
     * @return null
     */
    public function getHorizontal()
    {
        return $this->horizontal;
    }

    /**
     * @return null
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * @param null $validators
     */
    protected function setValidators($validators)
    {
        $this->validators = $validators;
    }

    private function buildValidators()
    {
        $this->setValidators($this->getOption('validators'));
        return $this;
    }

    public function save(Model $model)
    {
        if ($this->getOption('readonly', false) || $this->getOption('disabled', false)) {
            return [];
        }

        $value = $this->getValue();

        return [$this->getName() => is_array($value) ? $value : html_entity_decode($value)];
    }

    public function filter(QueryBuilder $queryBuilder)
    {
        $option = array_merge($this->getOption(), $this->getOption('filter', []));

        if (!isset($option['access']['roles']) || !Security::getInstance()->check((array)$option['access']['roles'])) {
            return;
        }

        $value = $this->getValue();

        if ($value === null || $value === '' || (is_array($value)) && empty($value)) {
            return;
        }

        if (!isset($option['comparison'])) {
            $option['comparison'] = 'like';
        }
        foreach ((array)$value as $val) {
            $val = html_entity_decode($val);

            switch ($option['comparison']) {
                case '=':
                    $queryBuilder->eq([$this->getName() => $val]);
                    break;
                case 'like':
                default:
                    $queryBuilder->like($this->getName(), '%' . $val . '%');
            }
        }
    }
}