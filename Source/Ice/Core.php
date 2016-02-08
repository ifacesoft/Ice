<?php
/**
 * Ice common core trait
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice;

use Ice\Core\Code_Generator;
use Ice\Core\Data_Provider;
use Ice\Core\Debuger;
use Ice\Core\Environment;
use Ice\Core\Logger;
use Ice\Data\Provider\Cacher;
use Ice\Data\Provider\Registry;
use Ice\Data\Provider\Repository;
use Ice\Helper\Object;

/**
 * Trait Core
 *
 * Common static methods for containers or others
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 */
trait Core
{
    /**
     * Return short name of class (Ice:Class_Name)
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getShortName()
    {
        return Object::getShortName(self::getClass());
    }

    /**
     * Return class by base class
     *
     * @param  string|null $className
     * @return Core
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getClass($className = null)
    {
        return empty($className)
            ? get_called_class()
            : Object::getClass(get_called_class(), $className);
    }

    /**
     * Return dat provider for self class
     *
     * @param  string|null $key
     * @return Data_Provider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getDataProvider($key)
    {
        return Environment::getInstance()->getProvider(self::getClass(), $key);
    }

    /**
     * Return class name (without namespace)
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getClassName()
    {
        return Object::getClassName(self::getClass());
    }

    /**
     * Return base class for self class (class extends Container)
     *
     * @return Core
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getBaseClass()
    {
        return Object::getBaseClass(self::getClass());
    }

    /**
     * Return code generator for self class type
     *
     * @param $class
     * @return Code_Generator
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public static function getCodeGenerator($class)
    {
        $baseClass = self::getBaseClass();

        $codeGeneratorClass = $baseClass == self::getClass()
            ? $baseClass
            : $baseClass . '_' . self::getClassName();

        return Code_Generator::getInstance($codeGeneratorClass . '/' . $class);
    }

    /**
     * Get module name of object
     *
     * 'Ice/Model/Ice/User' => 'Ice'
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getModuleAlias()
    {
        return Object::getModuleAlias(self::getClass());
    }

    /**
     * Return namespace by base class
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getNamespace()
    {
        return Object::getNamespace(self::getBaseClass(), self::getClass());
    }

    /**
     * Return registry storage for class
     *
     * @param  string $index
     * @return Registry
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public static function getRegistry($index = 'default')
    {
        return Registry::getInstance(self::getClass(), $index);
    }

    /**
     * Return repository storage for class
     *
     * @param  string $index
     * @return Repository
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.4
     */
    public static function getRepository($index = 'default')
    {
        return Repository::getInstance(self::getClass(), $index);
    }

    /**
     * Return cacher storage for class
     *
     * @param  string $index
     * @return Repository
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getCacher($index = 'default')
    {
        return Cacher::getInstance(self::getClass(), $index);
    }

    public function dumpDie()
    {
        Debuger::dumpDie($this);
        return $this;
    }

    public function dump()
    {
        Debuger::dump($this);
        return $this;
    }
}
