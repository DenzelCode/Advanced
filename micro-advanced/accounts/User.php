<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */
namespace advanced\accounts;

use advanced\Bootstrap;
use advanced\session\Auth;

/**
 * User class
 */
class User extends BaseUser {

    /**
    * @return bool
    */
    public function setAuth(bool $cookie = false, array $data = []) : bool {
        foreach ($data as $key => $value) $this->setAuthData($key, $value);

        if ($this->exists()) {
            if (empty($this->getAuthDataArray())) return false;

            $this->setAuthData('cookie', $cookie);

            $auth = Auth::attempt($this->getAuthDataArray(), $this);

            return $auth;
        }

        return false;
    }
}

