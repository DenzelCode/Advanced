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

class SessionManager implements IManager{

    /**
     * Init session.
     *
     * @return void
     */
    public static function init() : void {
        session_start();
    }
    
    /**
     * Get data feom session.
     *
     * @param string $name
     * @return mixed
     */
    public static function get(string $name) {
        if (!empty($_SESSION[$name])) return $_SESSION[$name]; else return null; 
    }

    /**
     * Get data from session or cookie.
     *
     * @param string $name
     * @return mixed
     */
    public static function getFromSessionOrCookie(string $name) {
        return self::get($name) ?? CookieManager::get($name);
    }

    /**
     * Set data to session.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function set(string $name, $value) : void {
        $_SESSION[$name] = $value;
    }

    /**
     * Set data to session by array.
     *
     * @param array $sessions
     * @return void
     */
    public static function setByArray(array $sessions) : void {
        foreach ($sessions as $key => $value) self::set($key, $value);
    }

    /**
     * Delete session by name.
     *
     * @param string $name
     * @return void
     */
    public static function delete(string $name) : void {
        unset($_SESSION[$name]);
    }

    /**
     * Delete session by array
     * 
     * @param array $sessions
     * @return void
     */
    public static function deleteByArray(array $sessions) : void {
        foreach ($sessions as $session) unset($_SESSION[$session]);
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

