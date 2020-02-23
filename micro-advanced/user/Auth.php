<?php

namespace advanced\user;

use advanced\Bootstrap;
use advanced\user\User;
use advanced\http\router\Request;
use advanced\session\SessionManager;

/**
* Auth class
*/
class Auth {

    private static $instance;

    public function __construct() {
        self::$instance = $this;
    }

    public static function getInstance() : Auth {
        return self::$instance;
    }

    public static function attempt(array $data, User $user) : bool {
        // test: || substr($user->getPassword(), 0, 1) != "$" && $user->getPassword() != $data["password"] || strtolower($data["username"]) != strtolower($user->getName())

        if (!password_verify($data["password"], $user->getPassword())) return false;

        self::create([
            "user_id" => $user->getId(),
            "username" => $user->getName(),
            "hash" => $user->getPassword(),
            "cookie" => $data["cookie"]
        ]);

        return true;
    }

    public static function check() : bool {
        if (!self::get("user_id") && !self::get("username") && !self::get("auth_code")) return false;

        $user = Bootstrap::getUsersFactory()->getUser(self::get("username"));

        if (!$user) return false;

        $auth_code = md5($user->getPassword() . Request::getIP());

        if (self::get("auth_code") == $auth_code) return true;

        // self::destroy();

        return false;
    }

    /**
    * @return IUser|null
    */
    public static function getUser() : ?IUser {
        $guest = UsersFactory::getGuestObject();

        if (!self::check()) return new $guest();
        
        $user = Bootstrap::getUsersFactory()->getUser(self::get("username"));

        if (!$user) return new $guest();

        return $user;
    }

    public static function isAuthenticated() : bool {
        return !self::getUser() instanceof Guest;
    }

    public static function set(array $data, bool $cookie = false, int $time = 3600 * 24 * 365, string $directory = "/") {
        SessionManager::setAll($data, $cookie, $time, $directory);
    }

    public static function get(string $data) {
        return SessionManager::get($data);
    }

    public static function unset(array $data, string $directory = "/") {
        SessionManager::deleteAll($data, $directory);
    }

    public static function destroy() {
        self::unset([ "user_id", "username", "auth_code" ]);
    }

    public static function create(array $data) {
        self::set([
            "user_id" => $data["user_id"],
            "username" => $data["username"],
            "auth_code" => md5($data["hash"] . Request::getIP())
        ], $data["cookie"]);
    }

    public static function hash(string $password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}

