<?php

namespace Ice\Core;

use Ice\Exception\Access_Denied_Security;

abstract class Security extends Container
{
    private static $defaultClassKey = null;

    public static function checkAccess($roles, $message)
    {
        if (!$roles || Security::getInstance()->check((array)$roles)) {
            return;
        }

        throw new Access_Denied_Security($message);
    }

    abstract protected function autologin();

    /**
     * All user roles
     *
     * @return string[]
     */
    abstract public function getRoles();

    /**
     * @return Security_User
     */
    abstract public function getUser();

    /**
     * @param $account
     * @return bool
     */
    abstract public function login(Security_Account $account);

    abstract public function logout();

    /**
     * Check logged in
     *
     * @return bool
     */
    abstract public function isAuth();

    /**
     * @param null $key
     * @param null $ttl
     * @return Security
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }

    protected static function create($key)
    {
        $class = self::getClass();

        return new $class($key);
    }

    public function init()
    {
        if (Security::$defaultClassKey === null) {
            Security::$defaultClassKey = get_class($this);

            $this->autologin();
            return;
        }

        Security::getLogger()->warning('Security already initialized', __FILE__, __LINE__);
    }

    protected static function getDefaultClassKey()
    {
        return Security::$defaultClassKey;
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }

    /**
     * Check access by roles
     *
     * @param array $roles
     * @return bool
     */
    abstract public function check(array $roles);

//    public static function checkAccess($roles, $permission)
//    {
//        if (empty($roles)) {
//            return true;
//        }
//
//        return in_array($permission, array_merge(array_intersect_key($roles, array_flip(Security::getRoleNames()))));
//    }

//    public static function getRoleNames()
//    {
//        if (isset($_SESSION['roleNames'])) {
//            return $_SESSION['roleNames'];
//        }
//
//        return $_SESSION['roleNames'] = Security::getUser() ? ['Ice:User'] : ['Ice:Guest'];
//    }
//
//    public static function getUser()
//    {
//        if (isset($_SESSION['userPk'])) {
//            return User::getModel($_SESSION['userPk'], '*');
//        }
//
//        return null;
//    }
}
