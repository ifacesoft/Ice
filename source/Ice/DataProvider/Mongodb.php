<?php

namespace Ice\DataProvider;

use Ice\Core\DataProvider;
use Ice\Core\Exception;
use Ice\Core\Logger;

/**
 * Class Mongodb
 *
 * Data provider for mongodb
 *
 * @see \Ice\Core\DataProvider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 */
class Mongodb extends DataProvider
{
    const DEFAULT_KEY = 'default';

    protected $options = [
        'host' => 'localhost',
        'port' => '27017'
    ];

    /**
     * Return default key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected static function getDefaultKey()
    {
        return self::DEFAULT_KEY;
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
     * @version 1.2
     * @since   0.4
     */
    public function get($key = null, $default = null, $require = false)
    {
        // TODO: Implement get() method.
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
     * @since   0.4
     */
    public function set(array $values = null, $ttl = null)
    {
        if ($ttl === -1) {
            return $values;
        }

        // TODO: Implement set() method.
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
     * @version 0.4
     * @since   0.4
     */
    public function delete($key, $force = true)
    {
        // TODO: Implement delete() method.
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
     * @version 0.4
     * @since   0.4
     */
    public function incr($key, $step = 1)
    {
        // TODO: Implement inc() method.
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
     * @version 0.4
     * @since   0.4
     */
    public function decr($key, $step = 1)
    {
        // TODO: Implement dec() method.
    }

    /**
     * Flush all stored data
     *
     * @author anonymous <email>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function flushAll()
    {
        // TODO: Implement flushAll() method.
    }

    /**
     * Return keys by pattern
     *
     * @param  string $pattern
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getKeys($pattern = null)
    {
        // TODO: Implement getKeys() method.
    }

    /**
     * Return connection of mongodb
     *
     * @return \Mongo
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    /**
     * Connect to data provider
     *
     * @param  $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.13
     * @since   0.4
     */
    protected function connect(&$connection)
    {
        $options = $this->getOptions();

        $user = $options->get('username');
        $host = $options->get('host');
        $port = $options->get('port');

        try {
            $connection = new \MongoClient('mongodb://' . $host . ':' . $port);
        } catch (\Exception $e) {
            Logger::getInstance(__CLASS__)
                ->info(
                    [
                        'mongodb - #' . $e->getCode() . ': {$0}',
                        $e->getMessage() . ' (' . $user . '@' . $host . ':' . $port . ')'
                    ],
                    Logger::WARNING
                );
            return false;
        }

        return (bool)$connection;
    }

    /**
     * Close connection with data provider
     *
     * @param $connection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    protected function close(&$connection)
    {
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
