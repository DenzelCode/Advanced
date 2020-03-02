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

namespace advanced\project;

use advanced\body\template\TemplateProvider;
use advanced\Bootstrap;
use advanced\components\Language;
use advanced\config\Config;
use advanced\data\Database;
use advanced\data\MySQL;
use advanced\data\sql\ISQL;
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

    protected static $sql;

    protected static $config;

    protected static $router;

    protected static $resourcesDirectory = PROJECT . "resources";

    protected static $configDirectory = PROJECT . "resources" . DIRECTORY_SEPARATOR . "config";

    protected static $configPath = PROJECT . "resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config";

    public abstract function init() : void;

    public abstract function getName() : string;

    public function __construct() {
        self::$instance = $this;
        
        self::$bootstrap = Bootstrap::getInstance();

        self::$config = new Config(self::$configPath);
    }

    public static function getInstance() : Project {
        return self::$instance;
    }

    public static function getBootstrap() : Bootstrap {
        return self::$bootstrap;
    }

    public static function getResourcesDirectory() : string {
        return self::$resourcesDirectory;
    }

    public static function getConfigDirectory() : string {
        return self::$configDirectory;
    }

    public static function getConfigPath() : string {
        return self::$configPath;
    }

    public static function getLanguage() : Language {
        return Bootstrap::getLanguage();
    }

    public static function getDatabase() : ?Database {
        return self::$database;
    }

    public static function setDatabase(?Database $database) {
        self::$database = $database;

        Bootstrap::setDatabase($database);

        if (!self::$sql instanceof ISQL) self::setSQL(MySQL::fromDatabase($database));
    }

    public static function getSQL() : ?ISQL {
        return self::$sql;
    }

    public static function setSQL(?ISQL $sql) {
        self::$sql = $sql;

        Bootstrap::setSQL($sql);

        if (!self::$database instanceof Database) self::setDatabase(Database::fromMySQL($sql));
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
