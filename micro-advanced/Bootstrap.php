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

namespace advanced;

use advanced\config\Config;
use advanced\http\Response;
use advanced\http\router\Request;
use advanced\body\template\TemplateProvider;
use advanced\components\Language;
use advanced\data\Database;
use advanced\user\UsersFactory;
use advanced\user\Auth;
use advanced\project\Project;
use advanced\session\SessionManager;

/**
* Bootstrap class
*/
class Bootstrap{

    private static $instance;

    private static $classes = [];
    
    public function __construct() {
        // Classes
        self::$instance = $this;

        self::$classes = [
            "request" => new Request($_SERVER["REQUEST_URI"]),
            "auth" => new Auth(),
            "response" => new Response(),
            "config" => new Config(Project::getConfigPath()),
            "mainConfig" => ($config = new Config(ADVANCED . "resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config"))
        ];

        if (!SessionManager::get("language")) SessionManager::set("language", substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2), true);

        if (!in_array(SessionManager::get("language"), $config->get("languages"))) SessionManager::set("language", "en", true);

        self::$classes["mainLanguage"] = new Language(SessionManager::get("language"), Language::PATH_ADVANCED);
        
        self::$classes["language"] = new Language(SessionManager::get("language"), Language::PATH_PROJECT);

        self::$classes["templateProvider"] = new TemplateProvider();

        /*
        $handler = function ($exception) {
            if (!$exception instanceof \Exception) {
                die($exception);
                
                return;
            }

            die($this->getMainLanguage()->get("exception.exception", null, ($exception instanceof AdvancedException ? $exception->getTranslatedMessage() : $exception->getMessage()), $exception->getFile(), $exception->getLine()));
        };

        set_exception_handler($handler);
        set_error_handler($handler, -1 & ~E_NOTICE & ~E_USER_NOTICE);
        */
    }

    /**
    * @return Bootstrap
    */
    public static function getInstance() : Bootstrap {
        return self::$instance;
    }

    /**
    * @return Request
    */
    public static function getRequest() : Request {
        return self::$classes["request"];
    }

    /**
    * @return Config
    */
    public static function getConfig() : Config {
        return self::$classes["config"];
    }

    /**
    * @return Config
    */
    public static function getMainConfig() : Config {
        return self::$classes["mainConfig"];
    }

    /**
    * @return TemplateProvider
    */
    public static function getTemplateProvider() : TemplateProvider {
        self::$classes["templateProvider"]->setPath("project");
        
        return self::$classes["templateProvider"];
    }

    /**
    * @return TemplateProvider
    */
    public static function getMainTemplateProvider() : TemplateProvider {
        self::$classes["templateProvider"]->setPath("advanced");
        
        return self::$classes["templateProvider"];
    }

    /**
    * @return LanguageProvider
    */
    public static function getLanguage() : Language {
        return self::$classes["language"];
    }

    /**
    * @return LanguageProvider
    */
    public static function getMainLanguage() : Language {
        return self::$classes["mainLanguage"];
    }

    /**
    * @return Response
    */
    public static function getResponse() : Response {
        return self::$classes["response"];
    }

    /**
    * @return Auth
    */
    public static function getAuth() : Auth {
        return self::$classes["auth"];
    }

    /**
     * @return Database
     */
    public static function setDatabase(Database $database) : void {
        self::$classes["database"] = $database;
    }

    /**
     * @return Database
     */
    public static function getDatabase(): ?Database {
        return self::$classes["database"] ?? null;
    }

    /**
     * @return Users
     */
    public static function getUsersFactory() : UsersFactory {
        if (!self::$classes["users"]) self::$classes["users"] = new UsersFactory();
        
        return self::$classes["users"];
    }

    public static function setClass(string $name, $object) : void {
        self::$classes[$name] = $object;
    }

    public static function getClass(string $name) {
        return self::$classes[$name];
    }
}
