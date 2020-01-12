<?php 
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace advanced\project;

use advanced\body\template\TemplateProvider;
use advanced\Bootstrap;
use advanced\data\Config;
use advanced\data\Database;
use advanced\exceptions\RouterException;
use advanced\http\router\Request;
use advanced\http\router\Router;

/**
* Project class
*/
abstract class Project {

    protected static $instance;

    protected static $bootstrap;

    protected static $database;

    protected static $config;

    protected static $router;

    public abstract function init() : void;

    public function __construct() {
        self::$instance = $this;
        
        self::$bootstrap = Bootstrap::getInstance();

        self::$config = new Config(PROJECT . 'resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config');
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

    public static function getDatabase() : ?Database {
        return self::$database;
    }

    public static function setDatabase(?Database $database) {
        self::$database = $database;

        Bootstrap::setDatabase($database);
    }

    public static function getConfig() : ?Config {
        return self::$config;
    }

    public static function setConfig(?Config $config) {
        self::$config = $config;
    }

    public static function initRouter() : void {
        try {
            Router::run(Bootstrap::getRequest());
        } catch (RouterException $e) {
            echo $e->getMessage();
        }
    }
}
