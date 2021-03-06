<?php
/**
 * Ice code generator implementation smarty view class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Code\Generator;

use Ice\Core\Code_Generator;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Helper\File;
use Ice\Render\Php;
use Ice\Render\Smarty;

/**
 * Class Render_Smarty
 *
 * View code generator for smarty templates
 *
 * @see Ice\Core\Code_Generator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Code_Generator
 */
class Render_Smarty extends Code_Generator
{
    /**
     * Generate code and other
     *
     * @param  array $data Sended data requered for generate
     * @param  bool $force Force if already generate
     * @return mixed
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function generate(array $data = [], $force = false)
    {
        $class = $this->getInstanceKey();

        $filePath = Loader::getFilePath($class, Smarty::TEMPLATE_EXTENSION, Module::RESOURCE_DIR, false, true, true);

        $isFileExists = file_exists($filePath);

        if (!$force && $isFileExists) {
            $this->getLogger()->info(['Template {$0} {$1} already created', ['Smarty', $class]]);
            return file_get_contents($filePath);
        }

        $classString = Php::getInstance()->fetch(__CLASS__, ['class' => $class]);

        File::createData($filePath, $classString, false);

        $message = $isFileExists
            ? 'Template {$0} {$1}" recreated'
            : 'Template {$0} {$1}" created';

        if ($isFileExists) {
            $this->getLogger()->info([$message, ['Smarty', $class]], Logger::SUCCESS);
        }

        return $classString;
    }
}
