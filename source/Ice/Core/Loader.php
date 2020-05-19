<?php
/**
 * Ice core loader class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Composer\Autoload\ClassLoader;
use Ice\Core;
use Ice\DataProvider\Repository;
use Ice\Exception\FileNotFound;
use Ice\Helper\Class_Object;

/**
 * Class Loader
 *
 * Register, unregister loaders and ice method load
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class Loader
{
    use Core;

    /** @var null */
    private static $autoloaders = [];

    private static $forceLoading = false;

    /**
     * @var Repository
     */
    private static $repository = null;

    public static function autoload($class)
    {
        $fileName = Loader::getFilePath($class, '.php', Module::SOURCE_DIR, false);

        if (file_exists($fileName)) {
            include_once $fileName;

            if (!Loader::isExistsClass($class)) {
                Logger::getInstance(__CLASS__)->exception(
                    ['File {$0} exists, but class {$1} not found', [$fileName, $class]],
                    __FILE__,
                    __LINE__
                );
            }

            return $fileName;
        }

        return null;
    }

    public static function isExistsClass($class)
    {
        return class_exists($class, false) || interface_exists($class, false) || trait_exists($class, false);
    }

    /**
     * Load class
     *
     * @param  $class
     * @return bool
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.10
     * @since   0.0
     */
    public static function load($class)
    {
        if (class_exists($class, false)) {
            return true;
        }

        if (Loader::$repository) {
            if ($fileName = Loader::$repository->get($class)) {
                include_once $fileName;
                return true;
            }
        }

        foreach (Loader::$autoloaders as $autoLoader) {
            $fileName = null;
            if ($autoLoader[0] instanceof ClassLoader) {
                if ($fileName = $autoLoader[0]->findFile($class)) {
                    include_once $fileName;
                }
            } else {
                $fileName = call_user_func($autoLoader, $class);
            }

            if (is_string($fileName) && !empty($fileName) && Loader::isExistsClass($class)) {
                if (Loader::$repository) {
                    Loader::$repository->set([$class => $fileName]);
                }

                return true;
            }
        }

        // todo: раскомментить
//        Logger::getInstance(__CLASS__)->warning(['Class {$0} not found', $class], __FILE__, __LINE__, null);

        return false;
    }

    /**
     * Return class path
     *
     * @param  $class
     * @param  $ext
     * @param  $path
     * @param  bool $isRequired
     * @param  bool $isNotEmpty
     * @param  bool $isOnlyFirst
     * @param  bool $allMatchedPathes
     * @return null|string
     *
     * @throws Exception
     * @throws FileNotFound
     * @throws \Ice\Exception\Config_Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getFilePath(
        $class,
        $ext,
        $path,
        $isRequired = true,
        $isNotEmpty = false,
        $isOnlyFirst = false,
        $allMatchedPathes = false
    )
    {
        $fileName = null;

        $fullStackPathes = [];
        $matchedPathes = [];

        if ($isOnlyFirst) {
            try {
                $modules = [Module::getInstance(Class_Object::getModuleAlias($class))];
            } catch (\Exception $e) {
                $modules = [Module::getInstance()];
            }
        } else {
            $modules = Module::getAll();
        }

        foreach ($modules as $module) {
            $typePathes = $module->gets('pathes/' . $path, []);

            if (empty($typePathes)) {
                $typePathes = [$path];
            }

            $filePath = str_replace(['_', '\\'], '/', $class);

            foreach ($typePathes as $typePath) {
                $fileName = $typePath . $filePath . $ext;

                $fullStackPathes[] = $fileName;

                if (file_exists($fileName)) {
                    $matchedPathes[] = $fileName;

                    if (!$allMatchedPathes) {
                        return $fileName;
                    }
                }
            }
        }

        if ($isRequired) {
            if (!$allMatchedPathes || empty($matchedPathes)) {
                if (self::$forceLoading) {
                    return null;
                } else {
                    throw new FileNotFound(['Files for {$0} not found', $class], $fullStackPathes);
                }
            }
        }

        if ($allMatchedPathes) {
            return $matchedPathes;
        }

        return $isNotEmpty && !empty($fullStackPathes) ? reset($fullStackPathes) : '';
    }

    public static function init()
    {
        self::$autoloaders = spl_autoload_functions();

        foreach (self::$autoloaders as $autholoader) {
            spl_autoload_unregister($autholoader);
        }

        array_unshift(self::$autoloaders, [__CLASS__, 'autoload']);

        spl_autoload_register('Ice\Core\Loader::load');

        self::$repository = self::getRepository();
    }
}