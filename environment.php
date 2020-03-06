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

define("MAIN", __DIR__ . DIRECTORY_SEPARATOR);
define("ADVANCED", MAIN . "micro-advanced" . DIRECTORY_SEPARATOR);
define("TESTS", MAIN . "tests" . DIRECTORY_SEPARATOR);

class environment{

    /**
     * @var autoload
     */
    private static $autoload;
    
    /**
     * Run autoloads.
     *
     * @return void
     */
    public function autoload() : void{
        require "autoload.php";

        try {
            self::$autoload = new autoload();

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
     * @return void
     */
    public static function init(string $dir) : void{
        define("PROJECT", dirname($dir) . DIRECTORY_SEPARATOR);

        self::autoload();

        $version = "7.2.0";

        if (!(version_compare(PHP_VERSION, $version) >= 0)) die(Bootstrap::getMainLanguage()->get("exception.version", null, PHP_VERSION, $version));
        
        advanced\session\SessionManager::init();
        
        if (file_exists(PROJECT . "Project.php")) {
            $project = new project\Project();

            $project->init();
        }

        (new Bootstrap());
    }

}
