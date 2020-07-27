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

use advanced\config\provider\IProvider;
use advanced\config\provider\JsonProvider;
use advanced\exceptions\ConfigException;
use advanced\file\File;

/**
* Config class
*/
class Config implements IConfig {

    public const PROVIDER_JSON = "json";
    public const PROVIDER_YAML = "yaml";

    /**
     * @var array
     */
    private $initial = [];

    /**
     * @var array|null
     */
    private $data = [];

    /**
     * @var File
     */
    private $file = null;

    /**
     * @var Config
     */
    private static $instance;

    /**
     * @var array
     */
    private static $files = [];

    /**
     * @var IProvider
     */
    private $provider = null;

    /**
     * New config file.
     *
     * @param string $file
     * @param array $default
     * @param string $provider
     */
    public function __construct(string $file, array $default = [], string $provider = Config::PROVIDER_JSON) {
        // Instance
        self::$instance = $this;

        $this->data = $default;

        $this->initial = $default;

        $this->updateProvider($provider);

        $this->file = new File($file . $this->provider->getExtension());

        if (empty(self::$files[$this->file->getPath()])) $this->update($default); else $this->data = self::$files[$this->file->getPath()];
    }

    /**
     * @return Config
     */
    public function getInstance() : IConfig {
        return self::$instance;
    }

    /**
     * Set config data.
     * 
     * @param string $key
     * @param mixed $value
     * @return Config
     */
    public function set(string $key, $value): IConfig {
        $values = &$this->data;

        foreach (($properties = explode(".", $key)) as $k => $segment) {
            if ((!isset($values[$segment]) || !is_array($values[$segment])) && $k != count($properties) - 1) $values[$segment] = [];

            $values = &$values[$segment];
        }

        $values = $value;

        self::$files[$this->file->getPath()] = $this->data;

        return $this;
    }

    /**
     * Set config data if not exists.
     *
     * @param string $key
     * @param mixed $value
     * @return Config
     */
    public function setIfNotExists(string $key, $value) : IConfig {
        if (!$this->has($key)) $this->set($key, $value);

        return $this;
    }

    /**
     * Save if file has been modified.
     *
     * @return void
     */
    public function saveIfModified() : void {
        if ($this->initial != $this->data) $this->save();
    }

    /**
     * Save file.
     *
     * @return void
     */
    public function save() : void {
        $this->file->write($this->provider->prettyPrint($this->data));

        $this->initial = $this->data;

        self::$files[$this->file->getPath()] = $this->data;
    }

    /**
     * Get config field.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null) {
        if (!is_array($this->data)) $this->data = [];

        if (array_key_exists($key, $this->data)) return $this->data[$key];

        if (strpos($key, ".") === false) return $default;

        $values = $this->data;

        foreach (explode(".", $key) as $segment) {
            if (!is_array($values) || !array_key_exists($segment, $values)) return $default;

            $values = $values[$segment];
        }

        return $values;
    }

    /**
     * Get all data.
     *
     * @return array
     */
    public function getAll() : array {
        return $this->data;
    }

    /**
     * Check if config has method.
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key) : bool {
        return ($this->get($key) !== null);
    }
    
    /**
     * Delete a config field.
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key): void {
        $values = &$this->data;

        foreach (($properties = explode(".", $key)) as $k => $segment) {
            if ((!isset($values[$segment]) || !is_array($values[$segment])) && $k != count($properties) - 1) $values[$segment] = [];

            $values = &$values[$segment];
        }

        unset($values);

        self::$files[$this->file->getPath()] = $this->data;
    }

    /**
     * @return File|null
     */
    public function getFile() : ?File {
        return $this->file;
    }

    /**
     * @var string
     */
    public function updateProvider(string $provider) : void {
        switch ($provider) {
            case self::PROVIDER_JSON:
            default:
                $this->provider = new JsonProvider();
                break;

            case self::PROVIDER_YAML:
                $this->provider = new JsonProvider();
                break;
        }
    }

    /**
     * @return IProvider|null
     */
    public function getProvider() : ?IProvider {
        return $this->provider;
    }

    /**
     * Update config.
     *
     * @param array $default
     * @return void
     */
    private function update(array $default = null) : void {
        $this->file->create(!$default ? $this->provider->encode([]) : $this->provider->prettyPrint($default));

        $this->data = $this->provider->decode($this->file->read());

        if ($this->data === null) throw new ConfigException(0, "exception.config.invalid_format", $this->file->getPath());

        $this->initial = $this->data;

        self::$files[$this->file->getPath()] = $this->data;
    }
}
