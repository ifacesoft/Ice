<?php
/**
 * Ice view render implementation php class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Render;

use Ice\Core\Environment;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Core\Render;
use Ice\Helper\Emmet;
use Ice\Code\Generator\Render_Php as CodeGenerator_Render_Php;

/**
 * Class Php
 *
 * Implementation view render php template
 *
 * @see \Ice\Core\Render
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Render
 *
 * @version 0.0
 * @since   0.0
 */
class Php extends Render
{
    const TEMPLATE_EXTENTION = '.tpl.php';

    /**
     * Return php view render
     *
     * @param  mixed $instanceKey
     * @param  int $ttl
     * @param array $params
     * @return Php|Render
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public static function getInstance($instanceKey = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($instanceKey, $ttl, $params);
    }

    /**
     * Render view via current view render
     *
     * @param string $template
     * @param  array $data
     * @param null $layout
     * @param string $templateType
     * @return mixed
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function fetch($template, array $data = [], $layout = null, $templateType = Render::TEMPLATE_TYPE_FILE)
    {
        if (empty($template)) {
            return $template;
        }

        if ($templateType == Render::TEMPLATE_TYPE_STRING) {
            ob_start();

            extract($data);
            unset($data);

            eval($template . ';');

            return $layout
                ? Emmet::translate($layout . '{{$content}}', ['content' => ob_get_clean()])
                : ob_get_clean();
        }

        $templateFilePath = Loader::getFilePath($template, Php::TEMPLATE_EXTENTION, Module::RESOURCE_DIR, false);

        if (!file_exists($templateFilePath)) {
            if (Environment::getInstance()->isDevelopment()) {
                $this->getLogger()->info(
                    [
                        Php::getClassName() . ': View {$0} not found. Trying generate template {$1}...',
                        [$template, Php::getClassName()]
                    ],
                    Logger::WARNING
                );

                return CodeGenerator_Render_Php::getInstance($template)->generate();
            } else {
                return $this->getLogger()->error(
                    ['Render error in template "{$0}" "{$1}"', [$templateFilePath, ob_get_clean()]],
                    __FILE__,
                    __LINE__
                );
            }
        }

        ob_start();

        try {
            extract($data);
            unset($data);

            include $templateFilePath;
        } catch (\Exception $e) {
            ob_clean();
            throw $e;
        }

        return $layout
            ? Emmet::translate($layout . '{{$content}}', ['content' => ob_get_clean()])
            : ob_get_clean();
    }
}
