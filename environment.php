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

use advanced\Bootstrap;
use advanced\exceptions\FileException;

class environment{

    public const VERSION = "2.0.3.1";
    public const REQUIRED_PHP_VERSION = "7.2.0";

    /**
     * @var autoload
     */
    private static $autoload;

    /**
     * @var boolean
     */
    private static $initialized = false;
    
    /**
     * Run autoloads.
     *
     * @return void
     */
    private static function autoload() : void{
        require "autoload.php";

        try {
            self::$autoload = new autoload();

            self::$autoload->addNamespace("", MAIN);
            self::$autoload->addNamespace("advanced", ADVANCED);
            self::$autoload->addNamespace("project", PROJECT);
            self::$autoload->addNamespace("tests", TESTS);

            self::$autoload->register();
        } catch (FileException $e) {
            die($e->getMessage());
        }

        @include "vendor/autoload.php";
    }

    public static function getAutoload() : autoload {
        return self::$autoload;
    }

    /**
     * Init application.
     *
     * @param string $dir
     * @param boolean $test
     * @return void
     */
    public static function init(string $dir, bool $test = false) : void{
        if (self::$initialized) return;

        error_reporting(E_ALL);

        define("MAIN", __DIR__ . DIRECTORY_SEPARATOR);
        define("ADVANCED", MAIN . "micro-advanced" . DIRECTORY_SEPARATOR);
        define("TESTS", MAIN . "tests" . DIRECTORY_SEPARATOR);
        define("PROJECT", dirname($dir) . DIRECTORY_SEPARATOR);
        define("PUBLIC", PROJECT . "public");

        if (!$test) self::autoload();

        advanced\session\SessionManager::init();

        (new Bootstrap());

        if (!(version_compare(PHP_VERSION, self::REQUIRED_PHP_VERSION) >= 0)) die(Bootstrap::getMainLanguage()->get("exception.version", null, PHP_VERSION, self::REQUIRED_PHP_VERSION));
        
        if (file_exists(PROJECT . "Project.php")) {
            $project = new project\Project();

            $project->init();
        }

        self::$initialized = true;
    }

    /**
     * Get Advanced version.
     *
     * @return string
     */
    public static function getVersion() : string {
        return self::REQUIRED_PHP_VERSION;
    }
}
