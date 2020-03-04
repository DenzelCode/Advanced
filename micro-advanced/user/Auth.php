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

    /**
     * @var Auth
     */
    private static $instance = null;

    public function __construct() {
        self::$instance = $this;
    }

    /**
     * @return Auth
     */
    public static function getInstance() : Auth {
        if (self::$instance == null) self::$instance = new Auth(); 

        return self::$instance;
    }

    /**
     * Use this method to know if the password matches with the user.
     *
     * @param array $data
     * @param User $user
     * @return boolean
     */
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

    /**
     * Use this method to know if the browser got an account logged in.
     *
     * @return boolean
     */
    public static function check() : bool {
        if (!Bootstrap::getSQL()) return false;

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

        if (!Bootstrap::getSQL()) return new $guest();

        if (!self::check()) return new $guest();
        
        $user = Bootstrap::getUsersFactory()->getUser(self::get("username"));

        if (!$user) return new $guest();

        return $user;
    }

    /**
     * Check if session is authenticated.
     *
     * @return boolean
     */
    public static function isAuthenticated() : bool {
        return !self::getUser() instanceof Guest;
    }

    /**
     * Set auth session parameters.
     *
     * @param array $data
     * @param boolean $cookie
     * @param integer $time
     * @param string $directory
     * @return void
     */
    public static function set(array $data, bool $cookie = false, int $time = 3600 * 24 * 365, string $directory = "/") : void {
        SessionManager::setByArray($data, $cookie, $time, $directory);
    }

    /**
     * Get auth session parameter.
     *
     * @param string $data
     * @return mixed
     */
    public static function get(string $data) {
        return SessionManager::get($data);
    }

    /**
     * Unset auth session parameter.
     *
     * @param array $data
     * @param string $directory
     * @return void
     */
    public static function unset(array $data, string $directory = "/") {
        SessionManager::deleteByArray($data, $directory);
    }

    /**
     * Destroy session.
     *
     * @return void
     */
    public static function destroy() : void {
        self::unset([ "user_id", "username", "auth_code" ]);
    }

    /**
     * Create session.
     *
     * @param array $data
     * @return void
     */
    public static function create(array $data) : void {
        self::set([
            "user_id" => $data["user_id"],
            "username" => $data["username"],
            "auth_code" => md5($data["hash"] . Request::getIP())
        ], $data["cookie"]);
    }

    /**
     * Hash passsword.
     *
     * @param string $password
     * @return void
     */
    public static function hash(string $password) : string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}

