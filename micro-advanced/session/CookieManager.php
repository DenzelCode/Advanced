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

class CookieManager implements IManager{

    /**
     * Init session.
     *
     * @return void
     */
    public static function init() : void {
        
    }
    
    /**
     * Get data from cookie.
     *
     * @param string $name
     * @param boolean $cookie
     * @return mixed
     */
    public static function get(string $name) {
        if (!empty($_COOKIE[$name])) return $_COOKIE[$name]; else return null;
    }

    /**
     * Get data from session or cookie.
     *
     * @param string $name
     * @return mixed
     */
    public static function getFromSessionOrCookie(string $name) {
        return self::get($name) ?? SessionManager::get($name);
    }

    /**
     * Set data to cookie.
     *
     * @param string $name
     * @param mixed $value
     * @param integer $time
     * @param string $directory
     * @return void
     */
    public static function set(string $name, $value, int $time = 3600 * 24 * 365, string $directory = "/") : void {
        setcookie($name, $value, time() + $time, $directory);
    }

    /**
     * Set data to cookie by array.
     *
     * @param array $sessions
     * @param integer $time
     * @param string $directory
     * @return void
     */
    public static function setByArray(array $sessions, int $time = 3600 * 24 * 365, string $directory = "/") : void {
        foreach ($sessions as $key => $value) self::set($key, $value, $cookie, $time, $directory);
    }

    /**
     * Delete cookie by name.
     *
     * @param string $name
     * @param string $directory
     * @return void
     */
    public static function delete(string $name, string $directory = "/") : void {
        setcookie($name, null, time() - 1000, $directory);
    }

    /**
     * Delete cookie by array
     * 
     * @param array $cookies
     * @param string $directory
     * @return void
     */
    public static function deleteByArray(array $cookies, string $directory = "/") : void {
        foreach ($sessions as $session) setcookie($session, null, time() - 1000, $directory);
    }
}

