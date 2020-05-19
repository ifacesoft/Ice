<?php

namespace Ice;

use Composer\Script\Event;
use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Profiler;
use Ice\Core\Request;
use Ice\Core\Response;
use Ice\Core\Session;
use Ice\DataProvider\Cli as DataProvider_Cli;
use Ice\DataProvider\Request as DataProvider_Request;
use Ice\DataProvider\Router as DataProvider_Router;
use Ice\Exception\Error;
use Ice\Exception\Http_Bad_Request;
use Ice\Exception\Http_Forbidden;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Http_Redirect;
use Ice\Widget\Http_Status;

class App
{
    private static $response = null;

    private static $context = null;

    public static function run($throwException = false)
    {
        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        $dataProvider = null;
        $actionClass = null;
        $params = [];

        $result = [];

        /** @var Action $actionClass */
        try {
            if (Request::isCli()) {
                $dataProvider = DataProvider_Cli::getInstance();

                $actionClass = $dataProvider->get('actionClass');
                $params = (array)$dataProvider->get('params');
            } else {
                Request::init();

                $dataProvider = Request::isAjax()
                    ? DataProvider_Request::getInstance()
                    : DataProvider_Router::getInstance();

                $actionClass = $dataProvider->get('actionClass');

                if ($dataProvider instanceof DataProvider_Router) {
                    $routeParams = (array)$dataProvider->get('routeParams');

                    if (isset($routeParams['params'])) {
                        $params = $routeParams['params'];
                    }

                    if ($response = $dataProvider->get('response')) {
                        if (isset($response['contentType'])) {
                            App::getResponse()->setContentType($response['contentType']);
                        }

                        if (isset($response['statusCode'])) {
                            App::getResponse()->setStatusCode($response['statusCode']);
                        }
                    }
                } else {
                    $params = (array)$dataProvider->get();
                }
            }

            if (!$actionClass) {
                throw new Error('Action class is empty');
            }

            $actionClass = Action::getClass($actionClass);

            if (!Loader::load($actionClass)) {
                throw new Error('Action class not load (Expected: ' . $actionClass . ')');
            }

            $result = $actionClass::call($params);
        } catch (\Exception $e) {
            if (Request::isCli()) {
                Logger::getInstance(__CLASS__)->error('Application (App): run action failure', __FILE__, __LINE__, $e);

                $result = ['error' => Logger::getInstance(__CLASS__)->info($e->getMessage(), Logger::DANGER)];
            } else {
                if ($throwException) {
                    throw $e;
                }

                try {
                    throw $e;
                } catch (Http_Redirect $e) {
                    $result['redirect'] = $e->getRedirectUrl();
                } catch (Http_Bad_Request $e) {
                    $result = ['content' => Http_Status::getInstance('app', null, ['code' => 400, 'message' => $e->getMessage(), 'stackTrace' => $e->getTraceAsString()])];
                } catch (Http_Forbidden $e) {
                    $result = ['content' => Http_Status::getInstance('app', null, ['code' => 403, 'message' => $e->getMessage(), 'stackTrace' => $e->getTraceAsString()])];
                } catch (Http_Not_Found $e) {
                    $result = ['content' => Http_Status::getInstance('app', null, ['code' => 404, 'message' => $e->getMessage(), 'stackTrace' => $e->getTraceAsString()])];
                } catch (\Exception $e) {
                    Logger::getInstance(__CLASS__)->error('Application (Http): run action failure', __FILE__, __LINE__, $e);
                    $result = [
                        'content' => Http_Status::getInstance('app', null, ['message' => $e->getMessage(), 'stackTrace' => $e->getTraceAsString()]),
                        'error' => Logger::getInstance(__CLASS__)->info($e->getMessage(), Logger::DANGER)
                    ];
                }
            }
        } catch (\Throwable $e) {
            if (Request::isCli()) {
                Logger::getInstance(__CLASS__)->error('Application (App): run action failure', __FILE__, __LINE__, $e);

                $result = ['error' => Logger::getInstance(__CLASS__)->info($e->getMessage(), Logger::DANGER)];
            } else {
                if ($throwException) {
                    throw $e;
                }

                try {
                    throw $e;
                } catch (Http_Redirect $e) {
                    $result['redirect'] = $e->getRedirectUrl();
                } catch (Http_Bad_Request $e) {
                    $result = ['content' => Http_Status::getInstance('app', null, ['code' => 400, 'message' => $e->getMessage(), 'stackTrace' => $e->getTraceAsString()])];
                } catch (Http_Forbidden $e) {
                    $result = ['content' => Http_Status::getInstance('app', null, ['code' => 403, 'message' => $e->getMessage(), 'stackTrace' => $e->getTraceAsString()])];
                } catch (Http_Not_Found $e) {
                    $result = ['content' => Http_Status::getInstance('app', null, ['code' => 404, 'message' => $e->getMessage(), 'stackTrace' => $e->getTraceAsString()])];
                } catch (\Exception $e) {
                    Logger::getInstance(__CLASS__)->error('Application (Http): run action failure', __FILE__, __LINE__, $e);
                    $result = [
                        'content' => Http_Status::getInstance('app', null, ['message' => $e->getMessage(), 'stackTrace' => $e->getTraceAsString()]),
                        'error' => Logger::getInstance(__CLASS__)->info($e->getMessage(), Logger::DANGER)
                    ];
                }
            }
        }

        if (Request::isCli()) {
            if (isset($result['error'])) {
                fwrite(STDERR, $result['error'] . "\n");
            } else {
                try {
                    if (isset($result['content'])) {
                        fwrite(STDOUT, $result['content'] . "\n");
                    }
                } catch (\Exception $e) {
                    fwrite(STDERR, Logger::getInstance(__CLASS__)->error('Application (App): render content failure', __FILE__, __LINE__, $e) . "\n");
                }
            }
        } else {
            try {
                App::getResponse()->send($result);
            } catch (\Exception $e) {
                echo Logger::getInstance(__CLASS__)->error('Application (Http): render content failure', __FILE__, __LINE__, $e);
            }
        }

        Profiler::setPoint($actionClass, $startTime, $startMemory);

        Logger::fb(Profiler::getReport(__CLASS__), __CLASS__, 'LOG');

//        if (!headers_sent() && !Request::isCli() && function_exists('fastcgi_finish_request')) {
//            fastcgi_finish_request();
//        }
    }

    /**
     * Return http response
     *
     * @return Response
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getResponse()
    {
        if (App::$response) {
            return App::$response;
        }

        return App::$response = Response::create();
    }

    /**
     * Return application context
     *
     * @return Action_Context
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getContext()
    {
        if (App::$context) {
            return App::$context;
        }

        return App::$context = Action_Context::create();
    }

    public static function update(Event $event)
    {
//        $composer = $event->getComposer();
//
//        /** @var Config $composerConfig */
//        $composerConfig = $composer->getConfig();

        system(realpath(__DIR__ . '/../../bin/') . '/ice Ice:Upgrade');
    }
}