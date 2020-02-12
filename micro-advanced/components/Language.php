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

namespace advanced\components;

use advanced\data\Config;

class Language{

    public const PATH_ADVANCED = "advanced";
    public const PATH_PROJECT = "project";

    private $language;

    private $path;

    private $config;

    public function __construct(string $language, string $path = self::PATH_PROJECT) {
        $this->language = $language;

        $this->path = $path;

        self::updateConfig($language);
    }   

    public function setPath(string $path) : void {
        $different = $path != $this->path;

        $this->path = $path;

        if ($different) self::updateConfig($this->language);
    }

    public function getPath() : string {
        return ($this->path == self::PATH_ADVANCED ? ADVANCED : PROJECT) . 'resources' . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR;
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

