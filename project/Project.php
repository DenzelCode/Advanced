<?php 
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
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
        self::setConfig(Bootstrap::getConfig());

        TemplateProvider::setParameters(self::getConfig()->get('web'));

        try {
            $database = new Database(self::$config->get('database.host'), self::$config->get('database.port'), self::$config->get('database.username'), self::$config->get('database.password'), self::$config->get('database.database'));

            self::setDatabase($database);
        } catch (DatabaseException $e) {
            die($e->getMessage());
        }

        self::initRouter();
    }
}
