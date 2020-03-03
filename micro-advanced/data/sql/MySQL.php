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

namespace advanced\data\sql;

use PDO;
use advanced\exceptions\DatabaseException;
use advanced\config\Config;
use advanced\data\Database;
use advanced\data\sql\ISQL;
use advanced\data\sql\query\AddColumns;
use advanced\data\sql\query\Create;
use advanced\data\sql\query\Delete;
use advanced\data\sql\query\Drop;
use advanced\data\sql\query\DropColumns;
use advanced\data\sql\query\Insert;
use advanced\data\sql\query\Query;
use advanced\data\sql\query\Select;
use advanced\data\sql\query\ShowColumns;
use advanced\data\sql\query\Truncate;
use advanced\data\sql\query\Update;
use PDOStatement;

/**
 * MySQL class
 */
class MySQL extends SQL{

    /**
     * @var string
     */
    private $host, $port, $username, $password;

    /**
     * @var MySQL
     */
    private static $instance;

    /**
     * @var string
     */
    private static $configPath = PROJECT . "resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "database";

    /**
     * Initialize MySQL Connection.
     *
     * @param string $host
     * @param integer $port
     * @param string $username
     * @param string $password
     * @param string $database
     * @param Database $db
     */
    public function __construct(string $host = "127.0.0.1", int $port = 3306, string $username = "root", string $password = "", string $database = "", Database $db = null) {
        self::$instance = $this;

        $this->host = $db instanceof Database ? $db->getHost() : $host;
        $this->port = $db instanceof Database ? $db->getPort() : $port;
        $this->username = $db instanceof Database ? $db->getUsername() : $username;
        $this->password = $db instanceof Database ? $db->getPassword() : $password;
        $this->database = $db instanceof Database ? $db->getDatabase() : $database;

        if ($db instanceof Database) $this->con = $db->getPDO();

        if (!extension_loaded("pdo")) {
            throw new DatabaseException(0, "exception.database.pdo_required");

            return;
        }

        (new Config(self::$configPath, [ "import" => [], "update" => [] ]));
        
        $this->run();
    }

    /**
     * @return void
     */
    public function run() : void {
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

                    $temp = null;
                } catch (\PDOException $ex) {
                    throw new DatabaseException($ex->getCode(), "exception.database.connecting", $e->getMessage());
                }

                $this->run();

                return;
            }

            throw new DatabaseException($e->getCode(), "exception.database.connecting", $e->getMessage());
        }
    }

    /**
     * @return MySQL
     */
    public function getInstance() : MySQL {
        return self::$instance;
    }

    /**
     * Create a MySQL object from a Database connection object.
     * 
     * @param Database $database
     * @return MySQL
     */
    public static function fromDatabase(Database $database) : MySQL {
        return new MySQL("127.0.0.1", 3306, "root", "", "", $database);
    }

    /**
     * @return string
     */
    public function getHost() : string {
        return $this->host;
    }

    /**
     * @return integer
     */
    public function getPort() : int {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUsername() : string {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword() : string {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getDatabase() : string {
        return $this->database;
    }

    /**
     * @return string
     */
    public static function getConfigPath() : string {
        return self::$configPath;
    }

    /**
     * @param string $configPath
     * @return void
     */
    public static function setConfigPath(string $configPath) : void {
        self::$configPath = $configPath;
    }

    public function import() : void {}
}
