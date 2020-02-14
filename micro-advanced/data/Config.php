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

namespace advanced\data;

use advanced\file\File;

/**
* Config class
*/
class Config {

    private $data = [];

    private $file = null;

    private static $instance;

    private static $files = [];

    public function __construct(string $file, array $default = []) {
        // Instance
        self::$instance = $this;

        $this->data = $default;

        $this->file = new File($file . '.json');

        if (empty(self::$files[$this->file->getPath()])) $this->getJSON($default); else $this->data = self::$files[$this->file->getPath()];
    }

    public function getInstance() : Config {
        return self::$instance;
    }

    public function set(string $name, $value): void {
        if (strpos($name, '.') === false && !empty($this->data[$name])) {
            $this->data[$name] = $value;

            self::$files[$this->file->getPath()][$name] = $value;

            return;
        }

        $values = &$this->data;

        foreach (($properties = explode(".", $name)) as $key => $segment) {
            if ((!isset($values[$key]) || !is_array($values[$key])) && $key != count($properties) - 1) $values[$segment] = [];

            $values = &$values[$segment];
        }

        $values = $value;

        self::$files[$this->file->getPath()] = $this->data;
    }

    public function save() {
        $this->file->write(json_encode($this->data, JSON_PRETTY_PRINT));

        self::$files[$this->file->getPath()] = $this->data;
    }

    public function get(string $name, $default = null) {
        if (!is_array($this->data)) $this->data = [];

        if (array_key_exists($name, $this->data)) return $this->data[$name];

        if (strpos($name, '.') === false) return $default;

        $values = $this->data;

        foreach (explode('.', $name) as $segment) {
            if (!is_array($values) || !array_key_exists($segment, $values)) return $default;

            $values = $values[$segment];
        }

        return $values;
    }

    public function has(string $name) : bool {
        return ($this->get($name) !== null);
    }
    
    public function delete(string $name): void {
        if (strpos($name, '.') === false && !empty($this->data[$name])) {
            unset($this->data[$name], self::$files[$this->file->getPath()][$name]);

            return;
        }

        $values = &$this->data;

        foreach (($properties = explode(".", $name)) as $key => $segment) {
            if ((!isset($values[$key]) || !is_array($values[$key])) && $key != count($properties) - 1) $values[$segment] = [];

            $values = &$values[$segment];
        }

        unset($values);

        self::$files[$this->file->getPath()] = $this->data;
    }

    private function getJSON(array $default = null) : void {
        $file = $this->file->getPath();

        $this->file->create(!$default ? '{}' : json_encode($default, JSON_PRETTY_PRINT));

        $this->data = json_decode($this->file->read(), true);

        self::$files[$this->file->getPath()] = $this->data;
    }
}
