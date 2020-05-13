<?php
/**
 * Ice helper object class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core\Debuger;
use Ice\Core\Loader;
use Ice\Core\Logger as Core_Logger;
use Ice\Core\Module;

/**
 * Class object
 *
 * Helper for objects and classes
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 */
class Class_Object
{
    private static $selfCache = [];

    /**
     * Return namespace by base class
     *
     * @param  $baseClass
     * @param  $className
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getNamespace($baseClass, $className)
    {
        $class = self::getClass($baseClass, $className);
        return strstr($class, Class_Object::getClassName($class), true);
    }

    /**
     * Return class by base class
     *
     * @param  $baseClass
     * @param  $class
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public static function getClass($baseClass, $class)
    {
        if (isset(Class_Object::$selfCache['class'][$baseClass][$class])) {
            return Class_Object::$selfCache['class'][$baseClass][$class];
        }

        if ($baseClass == $class) {
//            return $class;
            Core_Logger::getInstance(__CLASS__)->exception(['Base class and class {$0} are equal', $class], __FILE__, __LINE__);
        }

        if (!self::isShortName($class)) {
            return Class_Object::$selfCache['class'][$baseClass][$class] = $class;
        }

        list($moduleAlias, $className) = explode(':', $class);
        $module = Module::getInstance($moduleAlias);
        $namespace = $module->getNamespace();

        return Class_Object::$selfCache['class'][$baseClass][$class] = $namespace . '\\' . str_replace('_', '\\', Class_Object::getClassName($baseClass)) . '\\' . $className;
    }

    /**
     * Check is short name (Ice:Class_Name)
     *
     * @param  $shortName
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function isShortName($shortName)
    {
        return (bool)strpos($shortName, ':');
    }

    /**
     * Get module name of object
     *
     * 'Ice/Model/Ice/User' => 'Ice'
     *
     * @param  $class
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @deprecated
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getModuleAlias($class, $baseClass = null)
    {
        $pos = strpos(ltrim($class, '\\'), '\\');
        return $pos ? substr($class, 0, $pos) : Module::getInstance()->getAlias();
    }

    /**
     * Return class name (without namespace)
     *
     * @param  string $class
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getClassName($class)
    {
//        $reflect = new ReflectionClass($class);
//
//        return $reflect->getShortName();

        if (!strpos(ltrim($class, '\\'), '\\')) {
            return $class;
        }

        return substr($class, strrpos($class, '\\') + 1);
    }

    /**
     * Return alias of namespace class
     *
     * @param  $class
     * @param  $shortName
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getPrefixByClassShortName($class, $shortName)
    {
        return self::getModuleAlias(self::getClass($class, $shortName));
    }

    /**
     * Return short name of class (Ice:Class_Name)
     *
     * @param  $class
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getShortName($class)
    {
        return self::getModuleAlias($class) . ':' . self::getClassName($class);
    }

    /**
     * Return base class (class extends Container)
     *
     * @param  $class
     * @param string $markerClass
     * @return mixed
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   0.0
     */
    public static function getBaseClass($class, $markerClass = 'Ice\Core\Container')
    {
        if (!Class_Object::isClass($class)) {
            return $class;
        }

        foreach (class_parents($class) as $parentClass) {
            if ($parentClass == $markerClass) {
                return $class;
            }

            $class = $parentClass;
        }

        return $class;
    }

    /**
     * @param $class
     * @return bool
     */
    public static function isClass($class)
    {
        if (!class_exists($class, false)) {
            if (!Loader::load($class, false)) {
                return false;
            }
        }

        return true;
    }
}
