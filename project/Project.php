<?php 
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soul)
 */

namespace project;

use advanced\Bootstrap;
use advanced\project\Project as BaseProject;
use advanced\data\{Database, Config};
use advanced\exceptions\DatabaseException;

/**
* Project class
*/
class Project extends BaseProject {

    private static $database;
    private static $config;

    public function init() : void {
        self::$config = Bootstrap::getConfig();

        try {
            // self::$database = new Database(self::$config->get('database.host'), self::$config->get('database.port'), self::$config->get('database.username'), self::$config->get('database.password'), self::$config->get('database.database'));

            // Bootstrap::setDatabase(self::$database);
        } catch (DatabaseException $ex) {
            die($ex->getMessage());
        }
    }

    public static function getDatabase() : ?Database {
        return self::$database;
    }

    public static function getConfig() : ?Config {
        return self::$config;
    }
}
