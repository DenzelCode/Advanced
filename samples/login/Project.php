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

namespace project;

use advanced\project\Project as BaseProject;
use advanced\exceptions\DatabaseException;
use advanced\template\TemplateProvider;
use advanced\Bootstrap;
use advanced\sql\MySQL;

/**
* Project class
*/
class Project extends BaseProject {

    /**
     * @return void
     */
    public function init() : void {
        // Set all elements on the web section of the config into all templates parameters
        // Example: {@name}, {@cdn}, etc.
        TemplateProvider::setParameters(self::$config->get("web"));
        
        $config = self::$config;
        
        $config->setIfNotExists("database", [
            "host" => "127.0.0.1",
            "port" => 3306,
            "username" => "root",
            "password" => "",
            "database" => "testing_project"
        ])->saveIfModified();
        
        $dbConfig = $config->get("database");

        try {
            // Initialize MySQL
            $sql = new MySQL($dbConfig["host"], $dbConfig["port"], $dbConfig["username"], $dbConfig["password"], $dbConfig["database"]);

            self::setSQL($sql);
        } catch (DatabaseException $e) {
            die($e->getMessage());
        }

        // Init router
        self::initRouter();
    }

    /**
     * Get project name.
     *
     * @return string
     */
    public function getName(): string {
        return "Auth";
    }
}
