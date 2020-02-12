<?php

use advanced\Bootstrap;
use advanced\utils\ExecutionTime;

/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */


class environment{

    /**
     * Autoload function
     * @return  require, autoload.php
     */
    public function autoload() : void{
        require "autoload.php";

        require "vendor/autoload.php";
    }

    public function init(string $dir) : void{
        define('MAIN', __DIR__ . DIRECTORY_SEPARATOR);
        
        define('PROJECT', dirname($dir) . DIRECTORY_SEPARATOR);

        self::autoload();

        $version = "7.2.0";

        if (!(version_compare(PHP_VERSION, $version) >= 0)) die(Bootstrap::getMainLanguage()->get("exceptions.version", null, PHP_VERSION, $version));
        
        advanced\session\SessionManager::init();

        (new Bootstrap());

        if (file_exists(PROJECT . 'Project.php')) {
            $project = new project\Project();

            $project->init();
        }
    }

}
