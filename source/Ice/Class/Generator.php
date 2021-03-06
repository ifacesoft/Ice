<?php
namespace Ice;

use Ice\Core\Module;
use Ice\Helper\Php;

class Class_Generator
{
    /**
     * Class
     *
     * @var string
     */
    private $class = null;

    /**
     * Base class
     *
     * @var string
     */
    private $baseClass = null;

    /**
     * Class_Generator constructor.
     *
     * @param string $class
     * @param string $baseClass
     */
    private function __construct($class, $baseClass)
    {
        $this->class = $class;
        $this->baseClass = $baseClass;
    }

    public static function create($class, $baseClass = null)
    {
        return new Class_Generator($class, $baseClass);
    }

    public function generate($data)
    {
        $module = Module::getInstance($data['moduleAlias']);

        $filePath = $module->getPath(Module::SOURCE_DIR) . str_replace(['\\', '_'], '/', $this->class) . '.php';

        $code = file_get_contents($filePath);

        $start = 'protected static function config\(\)\n    \{\n        return \[';
        $finish = '\];\n    \}';

        $code = preg_replace(
            '/' . $start . '(.+)' . $finish . '/s',
            str_replace(
                ['\n', '\\', '['],
                ["\n", ''],
                $start
            ) . str_replace(
                "\n",
                "\n\t\t",
                Php::varToPhpString($data, false)
            ) . str_replace(
                ['\n', '\\', '];'],
                ["\n", ''],
                $finish
            ),
            $code,
            1
        );

        file_put_contents($filePath, str_replace("\t", str_repeat(' ', 4), $code));
    }
}
