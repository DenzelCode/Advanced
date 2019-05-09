<?php 
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soul)
 */

namespace advanced\project;

use advanced\Bootstrap;

/**
* Project class
*/
abstract class Project {

    private static $bootstrap;

    public abstract function init() : void;

    public function __construct() {
        self::$bootstrap = Bootstrap::getInstance();
    }

    public static function getBootstrap() : Bootstrap {
        return self::$bootstrap;
    }

    public static function getConfigPath() : string {
        return PROJECT . 'resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
    }
}
