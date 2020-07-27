<?php
/**
 * 
 * Advanced microFramework
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * 
 * @copyright Copyright (c) 2019 - 2020 Advanced microFramework
 * @author Advanced microFramework Team (Denzel Code, Soull Darknezz)
 * @link https://github.com/DenzelCode/Advanced
 * 
 */

namespace advanced\session;

class SessionManager{

    /**
     * Init session.
     *
     * @return void
     */
    public static function init() : void {
        session_start();
    }
    
    /**
     * Get data feom session or cookie.
     *
     * @param string $name
     * @param boolean $cookie
     * @return mixed
     */
    public static function get(string $name, bool $cookie = false) {
        if ($cookie) {
            if (!empty($_COOKIE[$name])) return $_COOKIE[$name]; else return null;
        } else { 
            if (!empty($_SESSION[$name])) return $_SESSION[$name]; else return null; 
        }
    }

    /**
     * Get data from session or cookie.
     *
     * @param string $name
     * @return mixed
     */
    public static function getFromSessionOrCookie(string $name) {
        if (!empty($_SESSION[$name])) return $_SESSION[$name]; else if (!empty($_COOKIE[$name])) return $_COOKIE[$name]; else return null;
    }

    /**
     * Set data to session/cookie.
     *
     * @param string $name
     * @param mixed $value
     * @param boolean $cookie
     * @param integer $time
     * @param string $directory
     * @return void
     */
    public static function set(string $name, $value, bool $cookie = false, int $time = 3600 * 24 * 365, string $directory = "/") : void {
        if (!$cookie) {
            $_SESSION[$name] = $value;

            return;
        }

        setcookie($name, $value, time() + $time, $directory);
    }

    /**
     * Set data to session/cookie as array.
     *
     * @param array $sessions
     * @param boolean $cookie
     * @param integer $time
     * @param string $directory
     * @return void
     */
    public static function setByArray(array $sessions, bool $cookie = false, int $time = 3600 * 24 * 365, string $directory = "/") : void {
        foreach ($sessions as $key => $value) self::set($key, $value, $cookie, $time, $directory);
    }

    /**
     * Delete session/cookie by name.
     *
     * @param string $name
     * @param boolean $cookie
     * @param string $directory
     * @return void
     */
    public static function delete(string $name, bool $cookie = false, string $directory = "/") : void {
        if (!$cookie) {
            unset($_SESSION[$name]);

            return;
        }

        setcookie($name, false, time() - 1000, $directory);
    }

    /**
     * Delete session/cookie by array
     * 
     * @param array $sessions
     * @param boolean $cookie
     * @param string $directory
     * @return void
     */
    public static function deleteByArray(array $sessions, bool $cookie = false, string $directory = "/") : void {
        foreach ($sessions as $session) {
            if (!$cookie) {
                unset($_SESSION[$session]);

                continue;
            }

            setcookie($session, false, time() - 1000, $directory);
        }
    }

    /**
     * Destroy all the sessions.
     * 
     * @return void
     */
    public static function destroy() : void {
        session_destroy();
    }
}

