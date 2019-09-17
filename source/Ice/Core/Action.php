<?php
/**
 * Ice core action abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\App;
use Ice\Core;
use Ice\Data\Provider\Cli as Data_Provider_Cli;
use Ice\Data\Provider\File;
use Ice\Data\Provider\Registry;
use Ice\Data\Provider\Repository;
use Ice\Data\Provider\Request as Data_Provider_Request;
use Ice\Data\Provider\Router as Data_Provider_Router;
use Ice\Data\Provider\Session as Data_Provider_Session;
use Ice\Exception\Http_Bad_Request;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Redirect;
use Ice\Exception\Security_AccessDenied;
use Ice\Helper\Json;
use Ice\Helper\Php;
use Ice\Helper\Validator as Helper_Validator;

/**
 * Class Action
 *
 * Core action abstract class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
abstract class Action implements Cacheable
{
    use Stored;

    /**
     * Child Actions
     *
     * Will be runned after current action
     *
     * @var array
     */
    private $actions = [];

    /**
     * Input params
     *
     * @var array
     */
    private $input = [];

    /**
     * Cache ttl
     *
     * @var null
     */
    private $ttl = null;

    /**
     * Action view
     *
     * @var View
     */
    private $view = null;

    /**
     * Private constructor of action
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    private function __construct()
    {
    }

    /**
     * Return action registry
     *
     * @return Registry
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getRegistry()
    {
        return Registry::getInstance(__CLASS__, self::getClass());
    }

    public static function call(array $input = [], $level = 0)
    {
        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        /**
         * @var Action $actionClass
         */
        $actionClass = self::getClass();

        if (Request::isCli()) {
            Action::getLogger()->info(['{$0}call: {$1}...', [str_repeat("\t", $level), $actionClass]], Logger::MESSAGE);
        }

        Action::checkResponse($input);

        $input = $actionClass::getInput($input);


        $env = $actionClass::getConfig()->get('access/env', false);

        if (isset($input['env'])) {
            $env = $input['env'];
        }

        if ($env && $env != Environment::getInstance()->getName()) {
            return View::create($actionClass);
        }

        $request = $actionClass::getConfig()->get('access/request', false);

        if (isset($input['request'])) {
            $request = $input['request'];
        }

        if ($request) {
            switch ($request) {
                case 'cli':
                    if (!Request::isCli()) {
                        return View::create($actionClass);
                    }
                    break;
                case 'ajax':
                    if (!Request::isAjax()) {
                        return View::create($actionClass);
                    }
                    break;
                default:
            }
        }

        //        $actionCacher = Action::getCacher();

        if ($actionClass::getConfig()->get('ttl', false) == 3600) {
            $actionCacher = File::getInstance($actionClass);
        }

        $inputString = Json::encode($input);
        $hash = crc32($inputString);
        $actionHash = $actionClass . '/' . $hash;

        App::getContext()->initAction($actionClass, $hash);
        $action = null;

        if (isset($actionCacher)) {
            /**
             * @var Action $action
             */
            $action = $actionCacher->get($actionHash);
        }
        if (!$action) {
            $action = $actionClass::create();

            $action->setInput($input);

            $output = (array)$action->run($input);

            Profiler::setPoint($actionClass . ' ' . $hash, $startTime, $startMemory);
            Logger::fb($input, 'action - ' . $actionClass . ' start', 'INFO');
            Logger::fb(Profiler::getReport($actionClass . ' ' . $hash), 'action finish', 'INFO');
            //            if ($content = $actionContext->getContent()) {
            //                App::getContext()->setContent(null);
            //                return $content;
            //            }

            $startTimeAfter = Profiler::getMicrotime();
            $startMemoryAfter = Profiler::getMemoryGetUsage();

            $rawActions = array_merge($action->actions, $actionClass::getConfig()->gets('actions', false));

            /**
             * @var string $actionKey
             * @var array $actionData
             * @var Action $subActionClass
             * @var array $subActionParams
             */
            foreach ($action->getActions($rawActions) as $actionKey => $actionData) {
                $newLevel = $level + 1;

                foreach ($actionData as $subActionClass => $actionItem) {
                    /**
                     * @var Action $subActionClass
                     */
                    list($subActionClass, $subActionParams) = each($actionItem);

                    $subView = null;

                    try {
                        $subView = $subActionClass::call($subActionParams, $newLevel)->getContent();
                    } catch (Redirect $e) {
                        throw $e;
                    } catch (Http_Bad_Request $e) {
                        throw $e;
                    } catch (Http_Not_Found $e) {
                        throw $e;
                    } catch (Security_AccessDenied $e) {
                        $subView = $e->getMessage();
                    } catch (\Exception $e) {
                        $subView = Action::getLogger()->error(
                            ['Calling subAction "{$0}" in action "{$1}" failed', [$subActionClass, $actionClass]],
                            __FILE__,
                            __LINE__,
                            $e
                        );
                    }

                    $output[$actionKey][] = $subView;
                }
            }

            Profiler::setPoint('Action ' . $actionClass . ' (childs)', $startTimeAfter, $startMemoryAfter);

            $action->setOutput($output);

            $action->getView()->render();
            if (isset($actionCacher)) {
                $actionCacher->set($actionHash, $action, $action->getTtl());
            }
            if (Request::isCli()) {
                Action::getLogger()->info(
                    ['{$0}{$1} complete!', [str_repeat("\t", $level), $actionClass::getClassName()]],
                    Logger::MESSAGE
                );
            }
        }

        return $action->getView();
    }

    private static function checkResponse(&$input)
    {
        if (isset($input['response'])) {
            if (isset($input['response']['contentType'])) {
                App::getResponse()->setContentType($input['response']['contentType']);
            }

            if (isset($input['response']['statusCode'])) {
                App::getResponse()->setStatusCode($input['response']['statusCode']);
            }

            unset($input['response']);
        }
    }

    /**
     * @param array $data
     * @return array
     * @throws Redirect
     */
    public static function getInput(array $data = [])
    {
        $params = array_merge(
            self::getConfig()->gets('input', false),
            ['actions', 'template', 'layout', 'viewRenderClass', 'env']
        );

        $input = Action::prepareInput($params, $data);

        if (isset($input['redirectUrl'])) {
            throw new Redirect($input['redirectUrl']);
        }

        return $input;
    }

    /**
     * Get action config
     *
     * @return Config
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getConfig()
    {
        $repository = self::getRepository();

        if ($config = $repository->get('config')) {
            return $config;
        }

        /**
         * @var Action $actionClass
         */
        $actionClass = self::getClass();

        $config = Config::create(
            $actionClass,
            array_merge_recursive(
                $actionClass::config(),
                Config::getInstance($actionClass, null, false, -1)
                    ->gets()
            )
        );

        return $repository->set('config', $config);
    }

    /**
     * Return action repository
     *
     * @return Repository
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getRepository()
    {
        return Repository::getInstance(__CLASS__, self::getClass());
    }

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
        return [];
    }

    private static function prepareInput($params, $data)
    {
        $dataProviderKeyMap = [
            'request' => Data_Provider_Request::DEFAULT_DATA_PROVIDER_KEY,
            'router' => Data_Provider_Router::DEFAULT_DATA_PROVIDER_KEY,
            'session' => Data_Provider_Session::DEFAULT_DATA_PROVIDER_KEY,
            'cli' => Data_Provider_Cli::DEFAULT_DATA_PROVIDER_KEY,
        ];

        $input = [];

        foreach ($params as $name => $param) {
            if (is_int($name)) {
                $name = $param;
                $param = [];
            }

            if (is_string($param)) {
                $input[$name] = $param;
                continue;
            }

            $dataProviderKeys = isset($param['providers'])
                ? (array)$param['providers']
                : ['default'];

            foreach ($dataProviderKeys as $dataProviderKey) {
                if (isset($input[$name])) {
                    continue;
                }

                if (isset($dataProviderKeyMap[$dataProviderKey])) {
                    $dataProviderKey = $dataProviderKeyMap[$dataProviderKey];
                }

                if ($dataProviderKey == 'default') {
                    if (array_key_exists($name, $data)) {
                        $input[$name] = $data[$name];
                        continue;
                    }

                    if (isset($param['default'])) {
                        $input[$name] = $param['default'];
                    }

                    continue;
                }

                $input[$name] = Data_Provider::getInstance($dataProviderKey)->get($name);
            }

            if ($value = Action::getInputParam($name, isset($input[$name]) ? $input[$name] : null, $param)) {
                $input[$name] = $value;
            }
        }

        return $input;
    }

    /**
     * Return valid input value
     *
     * @param  $name
     * @param  $value
     * @param  $param
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getInputParam($name, $value, $param)
    {
        if (empty($param)) {
            return $value;
        }

        if ($value === null && isset($param['default'])) {
            $value = $param['default'];
        }

        if (isset($param['type'])) {
            $value = Php::castTo($param['type'], $value);
        }

        if (isset($param['validators'])) {
            foreach ((array)$param['validators'] as $validatorClass => $validatorParams) {
                if (is_int($validatorClass)) {
                    $validatorClass = $validatorParams;
                    $validatorParams = null;
                }

                Helper_Validator::validate($validatorClass, $validatorParams, $name, $value);
            }
        }

        //        if (isset($param['converter']) && is_callable($param['converter'])) {
        //            $value = $param['converter']($value);
        //        }

        return $value;
    }

    /**
     * Get action object by name
     *
     * @return Action
     * @throws Redirect
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public static function create()
    {
        $actionClass = self::getClass();

        return new $actionClass();
    }

    /**
     * Init input params
     *
     * @param array $input
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public function setInput($input)
    {
        $this->initActions($input);
        $this->initView($input);
        $this->initTtl($input);

        $this->input = $input;
    }

    private function initActions(&$input)
    {
        if (isset($input['actions'])) {
            foreach ((array)$input['actions'] as $key => $action) {
                if (is_string($action)) {
                    $action = [$key => $action];
                }

                $this->addAction($action);
            }

            unset($input['actions']);
        }
    }

    protected function addAction(array $action)
    {
        $this->actions[] = $action;
    }

    private function initView(&$input)
    {
        /**
         * @var Action $actionClass
         */
        $actionClass = get_class($this);

        $view = $this->getView();

        if (array_key_exists('template', $input)) {
            $view->setTemplate($input['template']);
            unset($input['template']);
        } else {
            $view->setTemplate($actionClass::getConfig()->get('view/template', false));
        }

        if (array_key_exists('viewRenderClass', $input)) {
            $view->setViewRenderClass($input['viewRenderClass']);
            unset($input['viewRenderClass']);
        } else {
            $view->setViewRenderClass($actionClass::getConfig()->get('view/viewRenderClass', false));
        }

        if (array_key_exists('layout', $input)) {
            $view->setLayout($input['layout']);
            unset($input['layout']);
        } else {
            $view->setLayout($actionClass::getConfig()->get('view/layout', false));
        }
    }

    /**
     * @return View
     */
    public function getView()
    {
        if ($this->view) {
            return $this->view;
        }

        return $this->view = View::create(get_class($this));
    }

    private function initTtl(&$input)
    {
        if (isset($input['ttl'])) {
            $this->setTtl($input['ttl']);
            unset($input['ttl']);
        } else {
            $this->setTtl(null);
        }
    }

    /**
     * Action config
     *
     * @return array
     *
     *  protected static function config()
     *  {
     *      return [
     *          'view' => ['template' => '', 'viewRenderClass' => null],
     *          'actions' => [],
     *          'input' => [],
     *          'output' => [],
     *          'ttl' => -1,
     *          'access' => [
     *              'roles' => [],
     *              'request' => null,
     *              'env' => null
     *          ]
     *      ];
     *  }
     *
     * /** Run action
     *
     * @param  array $input
     * @return array
     */
    abstract public function run(array $input);

    /**
     * @param array $rawActions
     * @return array
     */
    public function getActions(array $rawActions)
    {
        $actions = [];

        foreach ($rawActions as $key => $action) {
            if (empty($action)) {
                continue;
            }

            list($key, $class, $params) = $this->prepareAction($key, $action);

            if (empty($key) || is_int($key)) {
                $key = $class::getClassName();
            }

            $actions[$key][] = [$class => $params];
        }

        return $actions;
    }

    private function prepareAction($key, $action)
    {
        $params = [];

        if (is_array($action)) {
            list($class, $key) = each($action);

            if (is_int($class)) {
                $class = $key;
                $key = 0;
            }

            $params = count($action) < 2 ? [] : current($action);
        } else {
            if (!is_int($key)) {
                $class = $key;
                $key = $action;
            } else {
                $class = $action;
            }
        }

        $class = $class[0] == '_'
            ? get_class($this) . $class
            : Action::getClass($class);

        return [$key, $class, $params];
    }

    /**
     * @param array $output
     */
    public function setOutput($output)
    {
        foreach (self::getConfig()->gets('output', false) as $name => $dataProviderKey) {
            if (!isset($output[$name])) {
                $output[$name] = Data_Provider::getInstance($dataProviderKey)->get($name);
            }
        }

        $this->getView()->setData($output);
    }

    /**
     * @return null
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * Set action result cache ttl
     *
     * @param integer $ttl
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    protected function setTtl($ttl)
    {
        if ($ttl === null) {
            /**
             * @var Action $actionClass
             */
            $actionClass = get_class($this);
            $ttl = $actionClass::getConfig()->get('ttl', false);
        }

        $this->ttl = $ttl;
    }

    /**
     * Validate cacheable object
     *
     * @param  $value
     * @return Cacheable
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function validate($value)
    {
        return $this;
    }

    /**
     * Invalidate cacheable object
     *
     * @return Cacheable
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function invalidate()
    {
        return $this;
    }
}
