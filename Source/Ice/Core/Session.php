<?php
/**
 * Ice core session class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;

/**
 * Class Session
 *
 * Core session handler class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class Session
{
    use Core;

    /**
     * Initialization session handler
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function init()
    {
        session_set_save_handler(
            'Ice\Core\Session::open',
            'Ice\Core\Session::close',
            'Ice\Core\Session::read',
            'Ice\Core\Session::write',
            'Ice\Core\Session::destroy',
            'Ice\Core\Session::gc'
        );

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * PHP >= 5.4.0<br/>
     * Close the session
     *
     * @link   http://php.net/manual/en/sessionhandlerinterafce.close.php
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function close()
    {
        return true;
    }

    /**
     * PHP >= 5.4.0<br/>
     * Destroy a session
     *
     * @link   http://php.net/manual/en/sessionhandlerinterafce.destroy.php
     * @param  int $session_id The session ID being destroyed.
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function destroy($session_id)
    {
        Session::getDataProvider('session')->delete($session_id);
    }

    /**
     * PHP >= 5.4.0<br/>
     * Cleanup old sessions
     *
     * @link   http://php.net/manual/en/sessionhandlerinterafce.gc.php
     * <p>
     * Sessions that have not updated for
     * the last maxlifetime seconds will be removed.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function gc()
    {
        return true;
    }

    /**
     * PHP >= 5.4.0<br/>
     * Initialize session
     *
     * @link   http://php.net/manual/en/sessionhandlerinterafce.open.php
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function open()
    {
        return true;
    }

    /**
     * PHP >= 5.4.0<br/>
     * Read session data
     *
     * @link   http://php.net/manual/en/sessionhandlerinterafce.read.php
     * @param  string $session_id The session id to read data for.
     * @return string <p>
     * Returns an encoded string of the read data.
     * If nothing was read, it must return an empty string.
     * Note this value is returned internally to PHP for processing.
     * </p>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function read($session_id)
    {
        return Session::getDataProvider('session')->get($session_id);
    }

    /**
     * PHP >= 5.4.0<br/>
     * Write session data
     *
     * @link   http://php.net/manual/en/sessionhandlerinterafce.write.php
     * @param  string $session_id The session id.
     * @param  string $session_data <p>
     * The encoded session data. This data is the
     * result of the PHP internally encoding
     * the $_SESSION superglobal to a serialized
     * string and passing it as this parameter.
     * Please note sessions use an alternative serialization method.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function write($session_id, $session_data)
    {
        Session::getDataProvider('session')->set($session_id, $session_data);
    }

    public static function id() {
        return session_id();
    }
}
