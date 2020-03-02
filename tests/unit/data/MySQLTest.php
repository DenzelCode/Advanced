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

namespace tests\unit\data;

use advanced\config\Config;
use advanced\data\MySQL;
use advanced\project\Project;
use Exception;
use tests\TestCase;

class MySQLTest extends TestCase {

    public function __construct() {
        parent::__construct();

        $config = new Config(Project::getConfigPath());

        $config->setIfNotExists("database", [
            "host" => "127.0.0.1",
            "port" => 3306,
            "username" => "root",
            "password" => "",
            "database" => "unittesting"
        ])->saveIfModified();

        Project::setSQL(new MySQL(
            $config->get("database.host"), 
            $config->get("database.port"), 
            $config->get("database.username"), 
            $config->get("database.password"), 
            $config->get("database.database")
        ));
    }
    
    public function testCreateTable() {
        $sql = Project::getSQL();

        $query = $sql->create()->setTable("test")->columns([
            "id" => "int(11) PRIMARY KEY AUTO_INCREMENT",
            "name" => "varchar(255)"
        ]);

        $this->assertTrue($query->execute());
    }
}

