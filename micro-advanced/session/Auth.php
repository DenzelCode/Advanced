<?php

namespace advanced\session;

use advanced\Bootstrap;
use advanced\accounts\base\Guest;
use advanced\accounts\User;

/**
* Auth class
*/
class Auth {

    private static $data = [];

    public static function attempt(array $data, User $user) : bool {
        if (!password_verify($data['password'], $user->getPassword()) || substr($user->getPassword(), 0, 1) != '$' && $user->getPassword() != $data['password'] || strtolower($data['username']) != strtolower($user->getName())) return false;

        self::create([
            'user_id' => $user->getId(),
            'username' => $user->getName(),
            'hash' => $user->getPassword(),
            'cookie' => $data['cookie']
        ]);

        return true;
    }

    public static function check() : bool {
        if (!self::get('user_id') && !self::get('username') && !self::get('auth_code')) return false;

        $user = Bootstrap::getUsers()->getUser(self::get('username'));

        if (!$user instanceof User) return false;

        $auth_code = md5($user->getPassword() . Request::getIP());

        if (self::get('auth_code') == $auth_code) return true;

        // self::destroy();

        return false;
    }

    /**
    * @return User|null
    */
    public static function getUser() : ?advanced\accounts\base\User {
        if (!self::check()) return new Guest();

        $user = Bootstrap::getUsers()->getUser(self::get('username'));

        if (!$user instanceof User) return new Guest();

        return $user;
    }

    public static function getAuthenticated() : bool {
        return self::getUser() instanceof User;
    }

    public static function set(array $data, bool $cookie = false, int $time = 3600 * 24 * 365, string $directory = '/') {
        SessionManager::setAll($data, $cookie, $time, $directory);
    }

    public static function get(string $data) {
        return SessionManager::get($data);
    }

    public static function unset(array $data, string $directory = '/') {
        SessionManager::deleteAll($data, $directory);
    }

    public static function destroy() {
        self::unset([ 'user_id', 'username', 'auth_code' ]);
    }

    public static function create(array $data) {
        self::set([
            'user_id' => $data['user_id'],
            'username' => $data['username'],
            'auth_code' => md5($data['hash'] . Request::getIP())
        ], $data['cookie']);
    }

    public static function hash(string $password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}

