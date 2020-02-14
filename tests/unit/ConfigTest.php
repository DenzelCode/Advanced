<?php

namespace tests\unit;

use advanced\data\Config;
use advanced\data\Database;
use advanced\project\Project;
use environment;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase {

    public function __construct() {
        parent::__construct();

        environment::init(__DIR__);
    }
    
    public function testSetConfiguration() {
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

    public function testDeleteConfiguration() {
        $config = new Config(Project::getConfigPath());

        $config->set("test.delete", true);

        $config->save();

        $config->delete("test.delete");

        $this->assertEmpty($config->get("test.delete"));
    }
}

