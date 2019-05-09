<?php
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
        define('MAiN', __DIR__ . DIRECTORY_SEPARATOR);
        
        define('PROJECT', dirname($dir) . DIRECTORY_SEPARATOR);
        
        self::autoload();

        advanced\session\SessionManager::init();

        $request = new advanced\http\router\Request($_SERVER['REQUEST_URI']);

        if (file_exists(PROJECT . 'Project.php')) {
            $project = new project\Project();

            $project->init();
        }

        try {
            advanced\http\router\Router::run($request);
        } catch (advanced\exceptions\RouterException $e) {
            echo "Exception: {$e->getMsg()}";
        }
    }

}
