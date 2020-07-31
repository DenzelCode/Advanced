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

namespace advanced;

use advanced\config\Config;
use advanced\http\Response;
use advanced\http\router\Request;
use advanced\language\Language;
use advanced\config\IConfig;
use advanced\data\Database;
use advanced\sql\ISQL;
use advanced\exceptions\AdvancedException;
use advanced\exceptions\ConfigException;
use advanced\user\UserFactory;
use advanced\project\Project;
use environment;

/**
* Bootstrap class
*/
class Bootstrap{

    /**
     * @var Bootstrap
     */
    private static $instance;

    /**
     * @var array
     */
    private static $classes = [];

    public function __construct() {
        // Classes
        self::$instance = $this;

        self::$classes = [
            "request" => new Request(empty($_SERVER["REQUEST_URI"]) ? "" : $_SERVER["REQUEST_URI"]),
            "response" => new Response() 
        ];

        try {
            self::$classes["config"] = new Config(Project::getConfigPath());

            self::$classes["mainConfig"] = ($config = new Config(ADVANCED . "resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config"));
        } catch (ConfigException $e) {
            die($e->getMessage());
        }

        Language::init($config->get("language.default"));

        self::$classes["mainLanguage"] = new Language(Language::getCurrentLanguage(), Language::PATH_ADVANCED);
        
        self::$classes["language"] = clone self::$classes["mainLanguage"];

        self::$classes["language"]->setProjectMode();
    }

    /**
     * Instance Bootstrap.
     * 
     * @return Bootstrap
     */
    public static function getInstance() : Bootstrap {
        if (!self::$instance) self::$instance = new Bootstrap();

        return self::$instance;
    }

    /**
     * Get request object, access to route arguments and more.
     * 
     * @return Request
     */
    public static function getRequest() : Request {
        return self::$classes["request"];
    }

    /**
     * Get project config.
     * 
     * @return IConfig
     */
    public static function getConfig() : IConfig {
        return self::$classes["config"];
    }

    /**
     * Get Advanced config.
     * 
     * @return IConfig
     */
    public static function getMainConfig() : IConfig {
        return self::$classes["mainConfig"];
    }

    /**
     * Get project language.
     * 
     * @return Language
     */
    public static function getLanguage() : Language {
        return self::$classes["language"];
    }

    /**
     * Get Advanced language.
     * 
     * @return Language
     */
    public static function getMainLanguage() : Language {
        return self::$classes["mainLanguage"];
    }

    /**
     * Get response.
     * 
     * @return Response
     */
    public static function getResponse() : Response {
        return self::$classes["response"];
    }

    /**
     * Set SQL connection (OLD).
     * 
     * @return Database
     */
    public static function setDatabase(Database $database) : void {
        self::$classes["database"] = $database;
    }

    /**
     * Get SQL connection (OLD).
     * 
     * @return Database
     */
    public static function getDatabase(): ?Database {
        return self::$classes["database"] ?? null;
    }

    /**
     * Set SQL connection.
     * 
     * @param ISQL $sql
     * @return void
     */
    public static function setSQL(ISQL $sql) : void {
        self::$classes["sql"] = $sql;
    }

    /**
     * Get SQL connection.
     * 
     * @return ISQL|null
     */
    public static function getSQL(): ?ISQL {
        return self::$classes["sql"] ?? null;
    }

    /**
     * Get users factory.
     * 
     * @return UserFactory
     */
    public static function getUserFactory() : UserFactory {
        if (empty(self::$classes["users"])) self::$classes["users"] = new UserFactory();
        
        return self::$classes["users"];
    }

    /**
     * Set a class in the list of classes.
     * 
     * @param string $name
     * @param mixed $object
     * @return void
     */
    public static function setClass(string $name, $object) : void {
        self::$classes[$name] = $object;
    }

    /**
     * Get a class in the list of classes.
     * 
     * @param string $name
     * @return mixed
     */
    public static function getClass(string $name) {
        return self::$classes[$name];
    }

}
