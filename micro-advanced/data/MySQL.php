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

namespace advanced\data;

use PDO;
use advanced\exceptions\DatabaseException;
use advanced\config\Config;
use advanced\data\sql\ISQL;
use advanced\data\sql\query\Create;
use advanced\data\sql\query\Delete;
use advanced\data\sql\query\Drop;
use advanced\data\sql\query\Insert;
use advanced\data\sql\query\Query;
use advanced\data\sql\query\Select;
use advanced\data\sql\query\Truncate;
use advanced\data\sql\query\Update;
use PDOStatement;

/**
 * MySQL class
 */
class MySQL implements ISQL{

    private $host, $port, $username, $password;

    private $con;

    private $lastStatement;

    private static $configPath = PROJECT . "resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "database";

    public function __construct(string $host = "127.0.0.1", int $port = 3306, string $username = "root", string $password = "", string $database = "") {
        self::$instance = $this;

        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        if (!extension_loaded("pdo")) {
            throw new DatabaseException(0, "exception.database.pdo_required");

            return;
        }

        (new Config(self::$configPath, [
            "import" => [],

            "update" => []
        ]));
        
        $this->run();
    }

    public function getInstance() : MySQL {
        return self::$instance;
    }

    public static function getConfigPath() : string {
        return self::$configPath;
    }

    public static function setConfigPath(string $configPath) : void {
        self::$configPath = $configPath;
    }

    public function run() {
        try {
            $options = [
                // PDO::ATTR_EMULATE_PREPARES => false,
                // PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];

            $this->con = new PDO("mysql:host={$this->host};port={$this->port};dbname={$this->database};charset=utf8mb4", $this->username, $this->password);

            foreach ($options as $key => $value) $this->con->setAttribute($key, $value);
        } catch (\PDOException $e) {
            if ($e->getCode() == 1049) {
                try {
                    $temp = new PDO("mysql:host=" . $this->host, $this->username, $this->password);

                    $temp->exec("CREATE DATABASE {$this->database}");
                } catch (\PDOException $ex) {
                    throw new DatabaseException($ex->getCode(), "exception.database.connecting", $e->getMessage());
                }

                $this->run();

                return;
            }

            throw new DatabaseException($e->getCode(), "exception.database.connecting", $e->getMessage());
        }
    }

    public function select() : Select {
        return (new Select($this));
    }

    public function insert() : Insert {
        return (new Insert($this));
    }

    public function update() : Update {
        return (new Update($this));
    }

    public function delete() : Delete {
        return (new Delete($this));
    }

    public function create() : Create {
        return (new Create($this));
    }
    
    public function drop() : Drop {
        return (new Drop($this));
    }

    public function truncate() : Truncate {
        return (new Truncate($this));
    }

    public function prepare(Query $query) : PDOStatement {
        return $this->con->prepare((string) $query);
    }
}
