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
use advanced\data\Database;
use advanced\project\Project;
use tests\TestCase;

class ConfigTest extends TestCase {
    
    public function testSetGetConfiguration() : void {
        $config = new Config(Database::getConfigPath(), [
            "import" => [],

            "update" => []
        ]);

        $table = [
            'id' => 'int(11) PRIMARY KEY AUTO_INCREMENT',
            "result" => "varchar(255)", 
            'timestamp' => "double(50, 0) DEFAULT 0"
        ];

        $config->set("import.tests", $table);

        $config->save();

        $this->assertEquals($table, $config->get("import.tests"));
    }

    public function testDeleteGetConfiguration() : void {
        $config = new Config(Project::getConfigPath());

        $config->set("test.delete", true);

        $config->save();

        $config->delete("test.delete");

        $this->assertEmpty($config->get("test.delete"));
    }

    public function testHasConfiguration() : void {
        $config = new Config(Project::getConfigPath());

        $config->set("test.has", true);

        $config->save();

        $this->assertTrue($config->get("test.has"));
    }
}

