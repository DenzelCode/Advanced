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

use advanced\config\Config;
use advanced\session\SessionManager;

class Language{

    public const PATH_ADVANCED = "advanced";
    public const PATH_PROJECT = "project";

    /**
     * @var string
     */
    private $language;

    /**
     * @var string
     */
    private $path;

    /**
     * @var Config
     */
    private $config;

    public function __construct(string $language, string $path = self::PATH_ADVANCED) {
        $this->language = $language;

        $this->path = $path;

        self::updateConfig($language);
    }

    /**
     * Get the language name.
     *
     * @return string
     */
    public function getName() : string {
        return $this->language;
    }

    /**
     * Enable/disable project mode.
     *
     * @param boolean $value
     * @return void
     */
    public function setProjectMode(bool $value = true) : void {
        $path = $this->path;

        $this->path = $value ? self::PATH_PROJECT : self::PATH_ADVANCED;

        if ($path != $this->path) self::updateConfig($this->language);
    }

    /**
     * Get the path where we are going to get the language.
     *
     * @return string
     */
    public function getPath() : string {
        return ($this->path == self::PATH_ADVANCED ? ADVANCED : PROJECT) . "resources" . DIRECTORY_SEPARATOR . "languages" . DIRECTORY_SEPARATOR;
    }

    /**
     * Update language file.
     *
     * @param string $file
     * @return void
     */
    private function updateConfig(string $file) : void {
        $file = $this->getPath() . $file;

        $this->config = new Config($file);
    }

    /**
     * Get translated string from the language files.
     *
     * @param string $key
     * @param string $default
     * @param mixed ...$params
     * @return mixed
     */
    public function get(string $key, string $default = null, ...$params) {
        $value = $this->config->get($key, $default);

        return is_string($value) ? self::filter($value, $params) : $value;
    }

    /**
     * Change app language.
     *
     * @param Language $language
     * @return void
     */
    public static function setLanguage(Language $language) {
        SessionManager::set("language", $language->getName(), true);
    }

    /**
     * Filter string.
     *
     * @param string $text
     * @param mixed $params
     * @return string
     */
    private function filter(string $text, $params) : string {
        foreach ($params as $key => $value) $text = str_replace("{{$key}}", $value, $text);
        
        return $text;
    }
}

