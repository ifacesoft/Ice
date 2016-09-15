<?php

namespace Ice\Security;

use Ice\Core\Config;
use Ice\Core\Debuger;
use Ice\Core\Model;
use Ice\Core\Model_Account;
use Ice\Core\Security;
use Ice\Exception\Error;
use Ice\Exception\Security_Auth;
use Ice\Model\Account_Password_Phone;
use Ice\Model\User;

class Ice extends Security
{
    const SESSION_USER_CLASS = 'userClass';
    const SESSION_USER_KEY = 'userKey';
    const SESSION_ACCOUNT_CLASS = 'accountClass';
    const SESSION_ACCOUNT_KEY = 'accountKey';

    private $user = null;

    /**
     * Check access by roles
     *
     * @param array $roles
     * @return bool
     */
    public function check(array $roles)
    {
        return array_intersect($roles, $this->getRoles()) || in_array('ROLE_ICE_SUPER_ADMIN', $this->getRoles());
    }

    /**
     * All user roles
     *
     * @return string[]
     */
    public function getRoles()
    {
        return $this->isAuth() ? ['ROLE_ICE_USER'] : ['ROLE_ICE_GUEST'];
    }

    /**
     * Check logged in
     *
     * @return bool
     */
    public function isAuth()
    {
        return (bool)$this->getDataProviderSession('auth')->get(Ice::SESSION_ACCOUNT_KEY);
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    protected function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @param Model_Account $account
     * @return Model_Account
     */
    public function login(Model_Account $account)
    {
        try {
            $user = $account->getUser();

//            session_regenerate_id();
//
//            if (ini_get("session.use_cookies")) {
//                $params = session_get_cookie_params();
//                session_regenerate_id();
//
//                setcookie(session_name(), session_id(), time() + $params["lifetime"],
//                    $params["path"], $params["domain"],
//                    $params["secure"], $params["httponly"]
//                );
//            }

            $this->getDataProviderSession('auth')->set([Ice::SESSION_USER_CLASS => get_class($user)]);
            $this->getDataProviderSession('auth')->set([Ice::SESSION_USER_KEY => $user->getPkValue()]);
            $this->getDataProviderSession('auth')->set([Ice::SESSION_ACCOUNT_CLASS => get_class($account)]);
            $this->getDataProviderSession('auth')->set([Ice::SESSION_ACCOUNT_KEY => $account->getPkValue()]);

            $this->setUser($user);
        } catch (\Exception $e) {
            $this->logout();

            return $this->getLogger()->exception('Ice security login failed', __FILE__, __LINE__, $e);
        }

        return $account;
    }

    public function logout()
    {
        $this->setUser(null);

        $this->getDataProviderSession()->flushAll();

        $this->autologin();
    }

    protected function autologin()
    {
        /** @var Model $userModelClass */
        $userModelClass = $this->getDataProviderSession('auth')->get(Ice::SESSION_USER_CLASS);

        if (!$userModelClass) {
            $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');
        }

        $userKey = $this->getDataProviderSession('auth')->get(Ice::SESSION_USER_KEY);

        if (!$userKey) {
            $userKey = 1;
            $user = $userModelClass::getModel($userKey, '*');
            $this->login(Account_Password_Phone::create(['user' => $user]));
        } else {
            $user = $userModelClass::getModel($userKey, '*');
        }

        if (!$user) {
            throw new Security_Auth(['AutoLogin failed. User {$0} with key {$1} not found', [$userModelClass, $userKey]]);
        }

        $this->setUser($user);

        return true;
    }
}