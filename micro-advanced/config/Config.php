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
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 * @link https://github.com/DenzelCode/Advanced
 * 
 */

namespace advanced\config;

use advanced\config\provider\JsonProvider;
use advanced\config\provider\Provider;
use advanced\file\File;

/**
* Config class
*/
class Config {

    public const PROVIDER_JSON = "json";
    public const PROVIDER_YAML = "yaml";

    private $initialData = [];

    private $data = [];

    private $file = null;

    private static $instance;

    private static $files = [];

    private $provider = null;

    public function __construct(string $file, array $default = [], string $provider = Config::PROVIDER_JSON) {
        // Instance
        self::$instance = $this;

        $this->data = $default;

        $this->initialData = $default;

        $this->updateProvider($provider);

        $this->file = new File($file . $this->provider->getExtension());

        if (empty(self::$files[$this->file->getPath()])) $this->update($default); else $this->data = self::$files[$this->file->getPath()];
    }

    public function getInstance() : Config {
        return self::$instance;
    }

    public function set(string $key, $value): Config {
        $values = &$this->data;

        foreach (($properties = explode(".", $key)) as $k => $segment) {
            if ((!isset($values[$segment]) || !is_array($values[$segment])) && $k != count($properties) - 1) $values[$segment] = [];

            $values = &$values[$segment];
        }

        $values = $value;

        self::$files[$this->file->getPath()] = $this->data;

        return $this;
    }

    public function setIfNotExists(string $key, $value) : Config {
        if (!$this->has($key)) $this->set($key, $value);

        return $this;
    }

    public function saveIfModified() : void {
        if ($this->initialData != $this->data) $this->save();
    }

    public function save() : void {
        $this->file->write(json_encode($this->data, JSON_PRETTY_PRINT));

        self::$files[$this->file->getPath()] = $this->data;
    }

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

    public function getAll() : array {
        return $this->data;
    }

    public function has(string $key) : bool {
        return ($this->get($key) !== null);
    }
    
    public function delete(string $key): void {
        $values = &$this->data;

        foreach (($properties = explode(".", $key)) as $k => $segment) {
            if ((!isset($values[$segment]) || !is_array($values[$segment])) && $k != count($properties) - 1) $values[$segment] = [];

            $values = &$values[$segment];
        }

        unset($values);

        self::$files[$this->file->getPath()] = $this->data;
    }

    public function getFile() : ?File {
        return $this->file;
    }

    public function updateProvider(string $provider) : void {
        switch ($provider) {
            case "json":
            default:
                $this->provider = new JsonProvider();
                break;

            case "yaml":
                $this->provider = new JsonProvider();
                break;
        }
    }

    public function getProvider() : ?Provider {
        return $this->provider;
    }

    private function update(array $default = null) : void {
        $this->file->create(!$default ? $this->provider->encode([]) : $this->provider->prettyPrint($default));

        $this->data = $this->provider->decode($this->file->read());

        $this->initialData = $this->data;

        self::$files[$this->file->getPath()] = $this->data;
    }
}
