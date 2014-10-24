<?php
/**
 * Ice view render implementation cli class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\View\Render;

use Ice\Core\Config;
use Ice\Core\Response;
use Ice\Core\View_Render;

/**
 * Class Cli
 *
 * Implementation view render cli "template"
 *
 * @see Ice\Core\View_Render
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage View_Render
 *
 * @version stable_0
 * @since stable_0
 */
class Cli extends View_Render
{
    /**
     * Constructor of cli view render
     *
     * @param Config $config
     */
    protected function __construct(Config $config)
    {
    }

    /**
     * Display rendered view in standard output
     *
     * @param $template
     * @param array $data
     * @param string $templateType
     */
    public function display($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE)
    {
        Response::send($this->fetch($template, $data, $templateType));
    }

    /**
     * Render view via current view render
     *
     * @param $template
     * @param array $data
     * @param string $templateType
     * @return mixed
     */
    public function fetch($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE)
    {
        return $data['cli'];
    }
}