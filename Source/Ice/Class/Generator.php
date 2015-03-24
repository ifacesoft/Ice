<?php
namespace Ice;


use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Helper\Object;
use Ice\Helper\Php;

class Class_Generator
{
    /**
     * Class
     *
     * @var string
     */
    private $_class = null;

    /**
     * Base class
     *
     * @var string
     */
    private $_baseClass = null;

    /**
     * Class_Generator constructor.
     *
     * @param string $class
     * @param string $baseClass
     */
    private function __construct($class, $baseClass)
    {
        $this->_class = $class;
        $this->_baseClass = $baseClass;
    }

    public static function create($class, $baseClass = null)
    {
        return new Class_Generator($class, $baseClass);
    }

    public function generate($data)
    {
        $namespace = Object::getNamespace($this->_baseClass, $this->_class);

        $module = Module::getInstance(Object::getModuleAlias($this->_class));

        $path = $module->get(Module::SOURCE_DIR);

//        if ($namespace) {
//            $path .= 'Model/';
//        }

        $filePath = $path . str_replace(['\\', '_'], '/', $this->_class) . '.php';

        $code = file_get_contents($filePath);

        $start = 'protected static function config\(\)\n    \{\n        return \[';
        $finish = '\];\n    \}';

//        $matches = [];
//
//        preg_match('/' . $start . '(.+)' . $finish . '/s', $code, $matches);
//
//
//        Debuger::dump($matches);
        $code = preg_replace(
            '/' . $start . '(.+)' . $finish . '/s',
            str_replace(['\n', '\\', '['], ["\n", ''], $start) . str_replace("\n", "\n\t\t", Php::varToPhpString($data, false)) . str_replace(['\n', '\\', '];'], ["\n", ''], $finish),
            $code,
            1
        );

        file_put_contents($filePath, $code);
    }
}