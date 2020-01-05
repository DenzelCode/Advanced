<?php
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
        require 'autoload.php';
    }

    public function init(string $dir) : void{
        define('MAIN', __DIR__ . DIRECTORY_SEPARATOR);
        
        define('PROJECT', dirname($dir) . DIRECTORY_SEPARATOR);

        self::autoload();

        advanced\session\SessionManager::init();

        if (file_exists(PROJECT . 'Project.php')) {
            $project = new project\Project();

            $project->init();
        }
    }

}
