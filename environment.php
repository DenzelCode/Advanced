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

use advanced\Bootstrap;
use advanced\exceptions\FileException;

class environment
{

    public const VERSION = "2.0.6";

    /**
     * @var boolean
     */
    private static $initialized = false;

    /**
     * Run autoload.
     *
     * @return void
     */
    private static function autoload(): void
    {
        require_once "vendor/autoload.php";
    }

    /**
     * Init application.
     *
     * @param string $dir
     * @param boolean $test
     * @return void
     */
    public static function init(string $dir, bool $test = false): void
    {
        if (self::$initialized) return;
        
        if (!$test) self::autoload();

        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        define("MAIN", __DIR__ . DIRECTORY_SEPARATOR);
        define("ADVANCED", MAIN . "micro-advanced" . DIRECTORY_SEPARATOR);
        define("TESTS", MAIN . "tests" . DIRECTORY_SEPARATOR);
        define("PROJECT", dirname($dir) . DIRECTORY_SEPARATOR);
        define("PROJECT_PUBLIC", PROJECT . "public" . DIRECTORY_SEPARATOR);

        \advanced\session\SessionManager::init();

        (new Bootstrap());

        if (file_exists(PROJECT . "Project.php")) {
            $project = new project\Project();

            $project->init();
        }

        self::$initialized = true;
    }
}
