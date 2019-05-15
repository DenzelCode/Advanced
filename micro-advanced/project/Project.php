<?php 
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace advanced\project;

use advanced\Bootstrap;

/**
* Project class
*/
abstract class Project {

    private static $instance;

    private static $bootstrap;

    public abstract function init() : void;

    public function __construct() {
        self::$instance = $this;
        
        self::$bootstrap = Bootstrap::getInstance();
    }

    public static function getInstance() : Project {
        return self::$instance;
    }

    public static function getBootstrap() : Bootstrap {
        return self::$bootstrap;
    }

    public static function getConfigPath() : string {
        return PROJECT . 'resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
    }
}
