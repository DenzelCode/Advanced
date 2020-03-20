<?php

namespace advanced\user\auth;

use advanced\user\User;
use advanced\user\IUser;

/**
* IAuth class
*/
interface IAuth {

    /**
     * Use this method to know if the password matches with the user.
     *
     * @param string $password
     * @param User $user
     * @param boolean $cookie True if you wanna remember the password.
     * @return boolean
     */
    public static function attempt(string $password, User $user, bool $cookie = false) : bool;

    /**
     * Use this method to know if the browser got an account logged in.
     *
     * @return boolean
     */
    public static function check() : bool;

    /**
     * @return IUser|null
     */
    public static function getUser() : ?IUser;

    /**
     * Check if session is authenticated.
     *
     * @return boolean
     */
    public static function isAuthenticated() : bool;

    /**
     * Destroy session.
     *
     * @return void
     */
    public static function destroy() : void;

    /**
     * Create session.
     *
     * @param array $data
     * @return void
     */
    public static function create(array $data) : void;

    /**
     * Hash passsword.
     *
     * @param string $password
     * @return void
     */
    public static function hash(string $password) : string;

    /**
     * Verify if a password match with the hash.
     *
     * @param string $password
     * @param string $hash
     * @return boolean
     */
    public static function verify(string $password, string $hash) : bool;
}

