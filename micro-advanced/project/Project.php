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

namespace advanced\project;

use advanced\Bootstrap;
use advanced\language\Language;
use advanced\config\Config;
use advanced\config\IConfig;
use advanced\data\Database;
use advanced\sql\ISQL;
use advanced\sql\MySQL;
use advanced\exceptions\RouterException;
use advanced\http\router\Request;
use advanced\http\router\Router;

/**
* Project class
*/
abstract class Project {

    /**
     * @var Project
     */
    protected static $instance;

    /**
     * @var Bootstrap
     */
    protected static $bootstrap;

    /**
     * @var Database
     */
    protected static $database;

    /**
     * @var ISQL
     */
    protected static $sql;

    /**
     * @var Config
     */
    protected static $config;

    /**
     * @var Router
     */
    protected static $router;

    /**
     * @var string
     */
    protected static $resourcesDirectory = PROJECT . "resources";

    /**
     * @var string
     */
    protected static $configDirectory = PROJECT . "resources" . DIRECTORY_SEPARATOR . "config";

    /**
     * @var string
     */
    protected static $configPath = PROJECT . "resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config";

    /**
     * @var string
     */
    protected static $publicDirectory = PROJECT . "public";

    /**
     * Initialize project.
     *
     * @return void
     */
    public abstract function init() : void;

    /**
     * Get project name.
     *
     * @return string
     */
    public abstract function getName() : string;

    public function __construct() {
        self::$instance = $this;
        
        self::$bootstrap = Bootstrap::getInstance();

        self::$config = new Config(self::$configPath);
    }

    /**
     * @return Project
     */
    public static function getInstance() : Project {
        return self::$instance;
    }

    /**
     * @return Bootstrap
     */
    public static function getBootstrap() : Bootstrap {
        return self::$bootstrap;
    }

    /**
     * Get resources directory.
     *
     * @return string
     */
    public static function getResourcesDirectory() : string {
        return self::$resourcesDirectory;
    }

    /**
     * Get config directory.
     *
     * @return string
     */
    public static function getConfigDirectory() : string {
        return self::$configDirectory;
    }

    /**
     * Get public directory.
     *
     * @return string
     */
    public static function getPublicDirectory() : string {
        return self::$publicDirectory;
    }

    /**
     * Get config file path.
     *
     * @return string
     */
    public static function getConfigPath() : string {
        return self::$configPath;
    }

    /**
     * Get language.
     *
     * @return Language
     */
    public static function getLanguage() : Language {
        return Bootstrap::getLanguage();
    }

    /**
     * Get database connection object.
     *
     * @return Database
     */
    public static function getDatabase() : ?Database {
        return self::$database;
    }

    /**
     * Set database connection object.
     *
     * @param Database|null $database
     * @return void
     */    
    public static function setDatabase(?Database $database) : void {
        self::$database = $database;

        Bootstrap::setDatabase($database);

        if (!self::$sql instanceof ISQL) self::setSQL(MySQL::fromDatabase($database));
    }

    /**
     * Get SQL connection object.
     *
     * @return ISQL|null
     */
    public static function getSQL() : ?ISQL {
        return self::$sql;
    }

    /**
     * Set SQL connection object.
     *
     * @param ISQL|null $sql
     * @return void
     */
    public static function setSQL(?ISQL $sql) {
        self::$sql = $sql;

        Bootstrap::setSQL($sql);

        if (!self::$database instanceof Database) self::setDatabase(Database::fromMySQL($sql));
    }

    /**
     * Get project config file.
     *
     * @return IConfig|null
     */
    public static function getConfig() : ?IConfig {
        return self::$config;
    }

    /**
     * Set project config file.
     *
     * @param IConfig|null $config
     * @return void
     */
    public static function setConfig(?IConfig $config) {
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
