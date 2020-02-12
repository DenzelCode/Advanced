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

namespace project;

use advanced\Bootstrap;
use advanced\project\Project as BaseProject;
use advanced\data\{Database, Config};
use advanced\exceptions\DatabaseException;
use advanced\body\template\TemplateProvider;

/**
* Project class
*/
class Project extends BaseProject {

    public function init() : void {
        // Set all elements on the web section of the config into all templates parameters
        // Example: {@name}, {@cdn}, etc.
        TemplateProvider::setParameters(self::getConfig()->get('web'));

        try {
            // Initialize database
            $database = new Database(self::$config->get('database.host'), self::$config->get('database.port'), self::$config->get('database.username'), self::$config->get('database.password'), self::$config->get('database.database'));

            self::setDatabase($database);
        } catch (DatabaseException $e) {
            die($e->getMessage());
        }

        // Init router
        self::initRouter();
    }
}
