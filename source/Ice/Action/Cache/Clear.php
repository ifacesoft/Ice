<?php
/**
 * Ice action cache clear class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Container;
use Ice\Core\Environment;
use Ice\Core\Logger;

/**
 * Class Cache_Clear
 *
 * Clear all data provider storage
 *
 * @see \Ice\Core\Action
 * @see \Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Action
 */
class Cache_Clear extends Action
{
    /**
     * Action config
     *
     * example:
     * ```php
     *  $config = [
     *      'actions' => [
     *          ['Ice:Title', ['title' => 'page title'], 'title'],
     *          ['Ice:Another_Action, ['param' => 'value']
     *      ],
     *      'view' => [
     *          'layout' => Emmet::PANEL_BODY,
     *          'template' => _Custom,
     *          'viewRenderClass' => Ice:Twig,
     *      ],
     *      'input' => [
     *          Request::DEFAULT_DATA_PROVIDER_KEY => [
     *              'paramFromGETorPOST => [
     *                  'default' => 'defaultValue',
     *                  'validators' => ['Ice:PATTERN => PATTERN::LETTERS_ONLY]
     *                  'type' => 'string'
     *              ]
     *          ]
     *      ],
     *      'output' => ['Ice:Resource/Ice\Action\Index'],
     *      'cache' => ['ttl' => -1, 'count' => 1000],
     *      'roles' => []
     *  ];
     * ```
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    protected static function config()
    {
        return [
            'view' => ['template' => ''],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    /**
     * Run action
     *
     * @param  array $input
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function run(array $input)
    {
        $logger = $this->getLogger();

        /**
         * @var Container $class
         * @var  $dataProviderKeys
         */
        foreach (Environment::getInstance()->gets('dataProviderKeys') as $class => $dataProviderKeys) {
            foreach ($dataProviderKeys as $key => $dataProviderKey) {
                $class::getDataProvider($key)->flushAll();

                if (is_array($dataProviderKey)) {
                    $dataProviderKey = reset($dataProviderKey);
                }

                $logger->info(['{$0}: {$1} - {$2}... cleared', [$class, $key, $dataProviderKey]], Logger::SUCCESS, true);
            }
        }
    }
}
