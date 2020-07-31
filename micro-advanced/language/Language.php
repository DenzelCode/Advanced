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

namespace advanced\language;

use advanced\config\Config;
use advanced\config\IConfig;
use advanced\session\CookieManager;

class Language{

    public const PATH_ADVANCED = "advanced";
    public const PATH_PROJECT = "project";

    public const LANGUAGE_PATH = "resources" . DIRECTORY_SEPARATOR . "languages" . DIRECTORY_SEPARATOR;

    /**
     * @var string
     */
    private $language;

    /**
     * @var string
     */
    private static $defaultLanguage = "en";

    /**
     * @var string
     */
    private $path;

    /**
     * @var IConfig
     */
    private $config;

    /**
     * @param string|null $language
     * @param string $path
     */
    public function __construct(?string $language, string $path = self::PATH_ADVANCED) {
        $this->language = $language === null ? self::$defaultLanguage : $language;

        $this->path = $path;

        self::updateConfig($this->language);
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
        return ($this->path == self::PATH_ADVANCED ? ADVANCED : PROJECT) . self::LANGUAGE_PATH;
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
     * @return IConfig
     */
    public function getConfig() : IConfig {
        return $this->config;
    }

    /**
     * Change app language.
     *
     * @param string $language
     * @return void
     */
    public static function setCurrentLanguage(string $language) {
        if ($language == null) $language = self::$defaultLanguage;

        $language = new Language($language);

        CookieManager::set("language", $language->getName());
    }

    /**
     * Get app language
     *
     * @return string|null
     */
    public static function getCurrentLanguage() : ?string {
        return CookieManager::get("language");
    }

    /**
     * Get default language,
     *
     * @return string
     */
    public static function getDefaultLanguage() : ?string {
        return self::$defaultLanguage;
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

    /**
     * Init current language.
     *
     * @param string $defaultLanguage
     * @return void
     */
    public static function init(string $defaultLanguage = "en") : void {
        self::$defaultLanguage = $defaultLanguage;

        $language = empty($_SERVER["HTTP_ACCEPT_LANGUAGE"]) ? $defaultLanguage : substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2);
    
        self::setCurrentLanguage(file_exists(ADVANCED . self::LANGUAGE_PATH) || !file_exists(PROJECT . self::LANGUAGE_PATH) ? $language : $defaultLanguage);
    }
}

