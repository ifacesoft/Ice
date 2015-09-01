<?php
/**
 * Ice core request class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Data\Provider\Request as Data_Provider_Request;
use Ice\Helper\Http;
use Locale;

/**
 * Class Request
 *
 * Core request class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since   0.0
 */
class Request
{
    use Core;

    /**
     * Return param from request
     *
     * @param  string $paramName Param name
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getParam($paramName)
    {
        $params = self::getParams();
        return isset($params[$paramName]) ? $params[$paramName] : null;
    }

    /**
     * Return all params from request
     *
     * @param  array $filterParams
     * @return mixed
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getParams(array $filterParams = [])
    {
        return Data_Provider_Request::getInstance()->get($filterParams);
    }

    /**
     * Return current locale (en|us|?)
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function locale()
    {
        $config = Request::getConfig();

        if (!$config->get('multiLocale')) {
            return $config->get('locale');
        }

        if (isset($_SESSION['locale'])) {
            return $_SESSION['locale'];
        }

        $locale = class_exists('Locale', false) && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])
            ? Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE'])
            : $config->get('locale');

        $strPos = strpos($locale, '_');

        return $strPos !== false ? substr($locale, 0, $strPos) : $locale;
    }

    /**
     * Return uri from request
     *
     * @param  bool $withoutQueryString
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function uri($withoutQueryString = false)
    {
        return isset($_SERVER['REQUEST_URI'])
            ? ($withoutQueryString ? strtok($_SERVER['REQUEST_URI'], '?') : $_SERVER['REQUEST_URI'])
            : '';
    }

    /**
     * Return query string from request
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function queryString()
    {
        return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    }

    /**
     * Return current host name from request
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public static function host()
    {
        if (!isset($_SERVER['HTTP_HOST'])) {

            $_SERVER['HTTP_HOST'] = gethostname();
            $_SERVER['SERVER_NAME'] = gethostname();
        }

        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Return real ip of client from request
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function ip()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var(
                $_SERVER['HTTP_X_FORWARDED_FOR'],
                FILTER_VALIDATE_IP
            )
        ) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
//        } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
//            return $_SERVER['HTTP_X_REAL_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return '0.0.0.0';
    }

    /**
     * Return browser agent from request
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function agent()
    {
        return isset($_SERVER['HTTP_USER_AGENT'])
            ? $_SERVER['HTTP_USER_AGENT']
            : (isset($_SERVER['SHELL'])
                ? $_SERVER['SHELL']
                : 'unknown');
    }

    /**
     * Return referrer info if not empty
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function referer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }

    /**
     * Check request type (is running via console)
     *
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function isCli()
    {
        return php_sapi_name() == 'cli';
    }

    /**
     * Check request type (is ajax request)
     *
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.0
     */
    public static function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    /**
     * Return request method
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getMethod()
    {
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
    }

    public static function init()
    {
        $cors = Request::getConfig()->gets('cors');

        if (isset($_SERVER['HTTP_ORIGIN']) && isset($cors[$_SERVER['HTTP_ORIGIN']])) {
            Http::setHeader('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            if (!empty($cors[$_SERVER['HTTP_ORIGIN']]['cookie'])) {
                Http::setHeader('Access-Control-Allow-Credentials: true');
            }
            Http::setHeader(
                'Access-Control-Allow-Methods: ' . implode(', ', $cors[$_SERVER['HTTP_ORIGIN']]['methods'])
            );
            Http::setHeader(
                'Access-Control-Allow-Headers: ' . implode(', ', $cors[$_SERVER['HTTP_ORIGIN']]['headers'])
            );
        }
    }

    public static function isOptions()
    {
        return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS';
    }
}
