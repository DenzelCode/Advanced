<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
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

    public function __construct(string $file, array $default = null) {
        // Instance
        self::$instance = $this;

        $this->data = [];

        $this->file = new File($file . '.json');

        if (empty(self::$files[$this->file->getPath()])) $this->getJSON($default); else $this->data = self::$files[$this->file->getPath()];
    }

    public function getInstance() : Config {
        return self::$instance;
    }

    public function set(string $name, $value) {
        $this->data[$name] = $value;

        self::$files[$this->file][$name] = $value;
    }

    public function save() {
        $this->file->write(json_encode($this->data, JSON_PRETTY_PRINT));

        self::$files[$this->file] = $this->data;
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

    private function getJSON(array $default = null) : void {
        $file = $this->file->getPath();

        $this->file->create(!$default ? '{}' : json_encode($default, JSON_PRETTY_PRINT));

        $this->data = json_decode($this->file->read(), true);

        self::$files[$this->file->getPath()] = $this->data;
    }

    public function delete(string $name) {
        unset($this->data[$name]);
    }
}
