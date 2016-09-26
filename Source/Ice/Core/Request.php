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
use Ice\Exception\Access_Denied_Request;
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
 */
class Request
{
    use Core;

    /**
     * Return param from request
     *
     * @param  string $paramName Param name
     * @param null $default
     * @return mixed
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     */
    public static function getParam($paramName = null, $default = null)
    {
        if (empty($paramName)) {
            return $_REQUEST;
        }

        if (is_array($paramName)) {
            return array_intersect_key($_REQUEST, array_flip($paramName));
        }

        return array_key_exists($paramName, $_REQUEST) ? $_REQUEST[$paramName] : $default;
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
        $config = Config::getInstance(__CLASS__);

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
            ? urldecode(($withoutQueryString ? strtok($_SERVER['REQUEST_URI'], '?') : $_SERVER['REQUEST_URI']))
            : '';
    }

    /**
     * Return query string from request
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     */
    public static function queryString()
    {
        return isset($_SERVER['REQUEST_URI'])
            ? $_SERVER['REQUEST_URI']
            : (isset($_SERVER['argv'])
                ? implode(' ', $_SERVER['argv'])
                : null
            );
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
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
            return $_SERVER['HTTP_X_REAL_IP'];
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
     * @version 1.3
     * @since   0.0
     */
    public static function agent()
    {
        return isset($_SERVER['HTTP_USER_AGENT'])
            ? $_SERVER['HTTP_USER_AGENT']
            : (isset($_SERVER['SHELL'])
                ? $_SERVER['SHELL']
                : null);
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
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
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
    public static function method()
    {
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    }

    public static function init()
    {
        $cors = Config::getInstance(__CLASS__)->gets('cors');

        if (isset($_SERVER['HTTP_ORIGIN']) && isset($cors[$_SERVER['HTTP_ORIGIN']])) {
            Http::setHeader('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            Http::setHeader('Access-Control-Allow-Methods: ' . implode(', ', $cors[$_SERVER['HTTP_ORIGIN']]['methods']));
            Http::setHeader('Access-Control-Allow-Headers: ' . implode(', ', $cors[$_SERVER['HTTP_ORIGIN']]['headers']));

            $credentials = empty($cors[$_SERVER['HTTP_ORIGIN']]['credentials']) || $cors[$_SERVER['HTTP_ORIGIN']]['credentials'] == 'false'
                ? 'false' : 'true';

            Http::setHeader('Access-Control-Allow-Credentials: ' . $credentials);
        }

        if (Request::isOptions()) {
            exit;
        }
    }

    public static function isOptions()
    {
        return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS';
    }

    public static function checkAccess($requests, $message)
    {
        if (
            !$requests ||
            (Request::isCli() && in_array('cli', (array)$requests)) ||
            (Request::isAjax() && in_array('ajax', (array)$requests)) ||
            in_array('http', (array)$requests)
        ) {
            return;
        }

        throw new Access_Denied_Request($message);
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
     * @version 1.5
     * @since   0.0
     */
    public static function isAjax()
    {
        if (isset($_REQUEST['ajax'])) {
            return (boolean) $_REQUEST['ajax'];
        }

        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    public static function protocol()
    {
        return isset($_SERVER['SERVER_PROTOCOL']) && stripos($_SERVER['SERVER_PROTOCOL'], 'https')
            ? 'https://'
            : 'http://';
    }
}
