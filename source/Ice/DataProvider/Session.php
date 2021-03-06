<?php
/**
 * Ice data provider implementation session class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\DataProvider;

use Ice\Core\DataProvider;
use Ice\Core\Exception;

/**
 * Class Session
 *
 * Data provider for session data
 *
 * @see \Ice\Core\DataProvider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 */
class Session extends DataProvider
{
    const DEFAULT_KEY = 'default';

    /**
     * Return default key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    protected static function getDefaultKey()
    {
        return self::DEFAULT_KEY;
    }

    /**
     * Set data to data provider
     *
     * @param array $values
     * @param  null $ttl
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function set(array $values = null, $ttl = null)
    {
        if ($ttl === -1) {
            return $values;
        }

        foreach ($values as $key => $value) {
            $_SESSION[$this->getKey()][$this->getIndex()][$key] = $value;
        }

        return $values;
    }

    /**
     * Delete from data provider by key
     *
     * @param  string $key
     * @param  bool $force if true return boolean else deleted value
     * @throws Exception
     * @return mixed|boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function delete($key, $force = true)
    {
        if ($force) {
            unset($_SESSION[$this->getKey()][$this->getIndex()][$key]);
            return true;
        }

        $value = $this->get($key);

        unset($_SESSION[$this->getKey()][$this->getIndex()][$key]);

        return $value;
    }

    /**
     * Get data from data provider by key
     *
     * @param  string $key
     * @param null $default
     * @param bool $require
     * @return mixed
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function get($key = null, $default = null, $require = false)
    {
        if (empty($key)) {
            return isset($_SESSION[$this->getKey()][$this->getIndex()])
                ? $_SESSION[$this->getKey()][$this->getIndex()]
                : [];
        }

        return isset($_SESSION[$this->getKey()][$this->getIndex()][$key])
            ? $_SESSION[$this->getKey()][$this->getIndex()][$key]
            : $default;
    }

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function incr($key, $step = 1)
    {
        return $_SESSION[$this->getKey()][$this->getIndex()][$key] += $step;
    }

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function decr($key, $step = 1)
    {
        return $_SESSION[$this->getKey()][$this->getIndex()][$key] -= $step;
    }

    /**
     * Flush all stored data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function flushAll()
    {
        $_SESSION[$this->getKey()][$this->getIndex()] = [];
    }

    /**
     * Return keys by pattern
     *
     * @param  string $pattern
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo    0.4 implements filter by pattern
     * @version 1.1
     * @since   0.0
     */
    public function getKeys($pattern = null)
    {
        return array_keys([$this->getKey()][$this->getIndex()]);
    }

    /**
     * Connect to data provider
     *
     * @param  $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    protected function connect(&$connection)
    {
        if (!isset($_SESSION[$this->getKey()][$this->getIndex()])) {
            $_SESSION[$this->getKey()][$this->getIndex()] = [];
        }

        return true;
    }

    /**
     * Close connection with data provider
     *
     * @param  $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    protected function close(&$connection)
    {
        return true;
    }

    /**
     * Set expire time (seconds)
     *
     * @param  $key
     * @param  int $ttl
     * @return mixed new value
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function expire($key, $ttl)
    {
        // TODO: Implement expire() method.
    }

    /**
     * Check for errors
     *
     * @return void
     *
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    function checkErrors()
    {
        // TODO: Implement checkErrors() method.
    }
}
