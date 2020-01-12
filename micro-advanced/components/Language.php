<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace advanced\components;

use advanced\data\Config;

class Language{

    private static $instance;

    private $language;

    private $path;

    private $config;

    public function __construct(string $language) {
        $this->language = $language;

        self::$instance = $this;

        self::updateConfig($language);
    }   

    public function setPath(string $path) : void {
        $different = $path != $this->path;

        $this->path = $path;

        if ($different) self::updateConfig($this->language);
    }

    public function getPath() : string {
        return ($this->path == 'advanced' ? ADVANCED : PROJECT) . 'resources' . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR;
    }

    private function updateConfig(string $file) : void {
        $file = $this->getPath() . $file;

        $this->config = new Config($file);
    }

    public function get(string $key, string $default = null, ...$params) {
        $value = $this->config->get($key, $default);

        return is_string($value) ? self::filter($value, $params) : $value;
    }

    private function filter(string $text, $params) : string {
        foreach ($params as $key => $value) $text = str_replace("{{$key}}", $value, $text);
        
        return $text;
    }
}

