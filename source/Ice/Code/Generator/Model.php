<?php
/**
 * Ice code generator implementation model class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Code\Generator;

use Ice\Class_Generator;
use Ice\Core\Code_Generator;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Model as Core_Model;
use Ice\Core\Module;
use Ice\Helper\Type_Array;
use Ice\Helper\File;
use Ice\Helper\Class_Object;
use Ice\Helper\Php as Helper_Php;
use Ice\Render\Php;

/**
 * Class Model
 *
 * Model code generator
 *
 * @see \Ice\Core\Code_Generator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Code_Generator
 */
class Model extends Code_Generator
{
    /**
     * Generate code and other
     *
     * @param  array $data Sended data requered for generate
     * @param  bool $force Force if already generate
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.10
     * @since   0.0
     */
    public function generate(array $data = [], $force = false)
    {
        $class = $this->getInstanceKey();

        $module = Module::getInstance($data['moduleAlias']);

        $filePath = $module->getPath(Module::SOURCE_DIR) . str_replace(['\\', '_'], '/', $class) . '.php';

        $namespace = $module->getNamespace() . '\Model';

        $isFileExists = file_exists($filePath);

        if ($isFileExists) {
            Class_Generator::create($class, Core_Model::getClass())->generate($data);
            return;
        }

        if (!$force && $isFileExists) {
            $this->getLogger()->info(['Model {$0} already created', $class]);
            return;
        }

        $data = [
            'fields' => Type_Array::column($data['columns'], 'fieldName'),
            'namespace' => $namespace,
            'modelName' => Class_Object::getClassName($class),
            'config' => str_replace("\n", "\n\t\t", Helper_Php::varToPhpString($data, false))
        ];

        $classString = Php::getInstance()->fetch(__CLASS__, $data);

        File::createData($filePath, $classString, false);

        $message = $isFileExists
            ? 'Model {$0} recreated'
            : 'Model {$0} created';

        if ($isFileExists) {
            $this->getLogger()->info([$message, $class], Logger::SUCCESS);
        }

        Loader::load($class);
    }
}
