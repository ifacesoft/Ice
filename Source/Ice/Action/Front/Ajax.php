<?php
/**
 * Ice action front ajax class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\View;
use Ice\Helper\Object;

/**
 * Class Front_Ajax
 *
 * Action front for ajax request
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Action
 *
 * @version 0.0
 * @since   0.0
 */
class Front_Ajax extends Action
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
     *      'ttl' => 3600,
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
            'view' => ['viewRenderClass' => 'Ice:Json', 'layout' => ''],
            'input' => [
                'call' => [
                    'providers' => 'request',
                    'validators' => 'Ice:Not_Empty'
                ],
                'params' => [
                    'providers' => 'request',
                ],
                'back' => [
                    'providers' => 'request',
                ]
            ]
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
        if (empty($input['params'])) {
            $input['params'] = [];
        }

        if (is_string($input['params'])) {
            parse_str($input['params'], $input['params']);
        }

        $this->addAction($input['call'], $input['params'], 'result');

        return [
            'back' => $input['back']
        ];
    }

    /**
     * Flush action context.
     *
     * Modify view after flush
     *
     * @param  View $view
     * @return View
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function flush(View $view)
    {
        $view = parent::flush($view);

        $params = $view->getParams();

        if ($params['result'] instanceof View) {
            $params['result'] = [
                'actionName' => Object::getName($params['result']->getActionClass()),
                'data' => isset($params['result']->getParams()['data']) ? $params['result']->getParams()['data'] : [],
                'error' => isset($params['result']->getParams()['error']) ? $params['result']->getParams()['error'] : '',
                'success' => isset($params['result']->getParams()['success']) ? $params['result']->getParams()['success'] : '',
                'redirect' => isset($params['result']->getParams()['redirect']) ? $params['result']->getParams()['redirect'] : '',
                'html' => $params['result']->getContent()
            ];
        } else {
            $params['result'] = [
                'actionName' => '',
                'data' => [],
                'error' => $params['result'],
                'success' => '',
                'redirect' => '',
                'html' => ''
            ];
        }

        $view->setParams($params);

        return $view;
    }
}
