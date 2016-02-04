<?php
/**
 * Ice bootstrap class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Composer\Autoload\ClassLoader;
use Ice\Core;

/**
 * Class Bootstrap
 *
 * Initialization required components for Ice application
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 */
class Bootstrap extends Container
{
    use Core;

    private $moduleConfigPath = null;

//    /**
//     * Bootstrap constructor.
//     * @param $moduleConfigPath
//     */
//    private function __construct($moduleConfigPath)
//    {
//        $this->moduleConfigPath = $moduleConfigPath;
//    }

    /**
     * @return string
     */
    public function getModuleConfigPath()
    {
        return $this->moduleConfigPath;
    }

    /**
     * Initialization requered parameters, constants and includes core files
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.0
     * @param array $data
     */
    protected function init(array $data)
    {
        setlocale(LC_ALL, 'en_US.UTF-8');
        setlocale(LC_NUMERIC, 'C');

        date_default_timezone_set('UTC');

        Loader::init($data['loader'], !empty($data['force']));
        Logger::init();
    }
}
