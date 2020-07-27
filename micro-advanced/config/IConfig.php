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

namespace advanced\config;


/**
* IConfig interface
*/
interface IConfig {

    /**
     * Set config data.
     * 
     * @param string $key
     * @param mixed $value
     * @return IConfig
     */
    public function set(string $key, $value): IConfig;

    /**
     * Set config data if not exists.
     *
     * @param string $key
     * @param mixed $value
     * @return IConfig
     */
    public function setIfNotExists(string $key, $value) : IConfig;

    /**
     * Save if file has been modified.
     *
     * @return void
     */
    public function saveIfModified() : void;

    /**
     * Save file.
     *
     * @return void
     */
    public function save() : void;

    /**
     * Get config field.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Get all data.
     *
     * @return array
     */
    public function getAll() : array;

    /**
     * Check if config has method.
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key) : bool;
    
    /**
     * Delete a config field.
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;
}
