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
use PDOStatement;

/**
 * Database class
 * 
 * This class is the old version of the MySQL Database connection in Advanced
 * Please update to our new and better practices (SQL Abstraction)
 * 
 */
class Database{

    /**
     * @var PDO
     */
    private $con;

    /**
     * @var Database
     */
    private static $instance;

    /**
     * @var string
     */
    private $table = null;

    /**
     * @var string
     */
    private $host, $port, $username, $password;

    /**
     * @var PDOStatement
     */
    private $lastStatement;

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
     * @param MySQL $mysql
     */
    public function __construct(string $host = "127.0.0.1", int $port = 3306, string $username = "root", string $password = "", string $database = "", MySQL $mysql = null) {
        self::$instance = $this;

        $this->host = $mysql instanceof MySQL ? $mysql->getHost() : $host;
        $this->port = $mysql instanceof MySQL ? $mysql->getPort() : $port;
        $this->username = $mysql instanceof MySQL ? $mysql->getUsername() : $username;
        $this->password = $mysql instanceof MySQL ? $mysql->getPassword() : $password;
        $this->database = $mysql instanceof MySQL ? $mysql->getDatabase() : $database;

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
     * @return Database
     */
    public function getInstance() : Database {
        return self::$instance;
    }

    /**
     * Create a Database object from a MySQL connection object.
     *
     * @param MySQL $mysql
     * @return Database
     */
    public static function fromMySQL(MySQL $mysql) : Database {
        return new Database("127.0.0.1", 3306, "root", "", "", $mysql);
    }

    /**
     * @return string
     */
    public function getHost() : string {
        return $this->host;
    }

    /**
     * @return string
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
     * @return PDO
     */
    public function getPDO() : PDO {
        return $this->con;
    }

    /**
     * Set the table that you want to action with.
     *
     * @param string $table
     * @return Database
     */
    public function setTable(string $table) : Database {
        $this->table = $table;

        return $this;
    }

    /**
     * Get the table that you want to action with.
     *
     * @return string|null
     */
    public function getTable() : ?string {
        return $this->table;
    }

    /**
     * @return PDOStatement
     */
    public function getLastStatement() : PDOStatement {
        return $this->lastStatement;
    }

    /**
     * @return string
     */
    public function getLastError() : string {
        return $this->getLastStatement()->errorInfo()[2];
    }
 
    /**
     * Select rows from a table.
     *
     * @param array $data
     * @param string $options
     * @param array $execute
     * @return PDOStatement
     */
    public function select(array $data = ["*"], string $options = null, array $execute = []) : PDOStatement {
        if (!$this->getTable()) return false;

        if (empty($data)) return false;

        $query = "SELECT ";

        if (strtolower($data[0]) === "distinct") {
            unset($data[0]);

            $query .= "DISTINCT ";
        }

        $i = 0;

        foreach ($data as $key) {
            if ($i != (count($data) - 1)) $query .= "{$key}, "; else $query .= "{$key} ";

            $i++;
        }

        $query .= "FROM " . $this->getTable();

        if ($options) $query .= " " . $options;

        $prepare = $this->getPDO()->prepare($query);

        $this->lastStatement = $prepare;

        $execute = $prepare->execute($execute);

        return $prepare;
    }

    /**
     * Insert row into a table.
     *
     * @param array $data
     * @return boolean
     */
    public function insert(array $data) : bool {
        if (!$this->getTable()) return false;

        if (empty($data)) return false;

        $query = "INSERT INTO " . $this->getTable() . " (";

        $i = 0;

        foreach ($data as $key => $value) {
            if ($i != (count($data) - 1)) $query .= "{$key}, "; else $query .= "{$key}";

            $i++;
        }

        $query .= ") VALUES (";

        $i = 0;

        $execute = [];

        foreach ($data as $key => $value) {
            if ($i != (count($data) - 1)) $query .= "?, "; else $query .= "?";

            $execute[] = $value;

            $i++;
        }

        $query .= ")";

        $add = $this->getPDO()->prepare($query);

        $this->lastStatement = $add;

        $add = $add->execute($execute);

        return $add;
    }

    /**
     * Update a row from a table.
     *
     * @param array $data
     * @param string $options
     * @param array $execute
     * @return boolean
     */
    public function update(array $data, string $options = null, array $execute = []) : bool {
        if (!$this->getTable()) return false;

        if (empty($data)) return false;

        $query = "UPDATE " . $this->getTable() . " ";

        $i = 0;

        $exc = [];

        foreach ($data as $key => $value) {
            if (count($data) == 1) $query .= "SET {$key} = ? "; else if ($i == 0) $query .= "SET {$key} = ?, "; else if ($i != (count($data) - 1)) $query .= "{$key} = ?, "; else $query .= "{$key} = ?";

            $exc[] = $value;

            $i++;
        }

        $exc = array_merge($exc, $execute);

        if ($options) $query .= " " . $options;

        $update = $this->getPDO()->prepare($query);

        $this->lastStatement = $update;

        $update = $update->execute($exc);

        return $update;
    }

    /**
     * Delete rows from a table.
     *
     * @param string $options
     * @param array $execute
     * @return boolean
     */
    public function delete(string $options = null, array $execute = []) : bool {
        if (!$this->getTable()) return false;

        $query = "DELETE FROM " . $this->getTable();

        if ($options) $query .= " " . $options;

        $delete = $this->getPDO()->prepare($query);

        $this->lastStatement = $delete;

        $delete = $delete->execute($execute);

        return $delete;
    }

    /**
     * Create a table.
     *
     * @param array $data
     * @return void
     */
    public function create(array $data) : bool {
        if (!$this->getTable()) return false;

        if (empty($data)) return false;

        $query = "CREATE TABLE IF NOT EXISTS " . $this->getTable() . " ( ";

        $i = 0;

        $execute = [];

        foreach ($data as $key => $value) {
            if ($i != (count($data) - 1)) $query .= "{$key} {$value}, "; else $query .= "{$key} {$value} ";

            $execute[] = $value;

            $i++;
        }

        $query .= ")";

        $create = $this->getPDO()->prepare($query);

        $this->lastStatement = $create;

        $create = $create->execute($execute);

        return $create;
    }

    /**
     * Truncate a table.
     *
     * @param string $options
     * @param array $execute
     * @return void
     */
    public function truncate(string $options = null, array $execute = []) : bool {
        if (!$this->getTable()) return false;

        $query = "TRUNCATE TABLE " . $this->getTable();

        if ($options) $query .= " " . $options;

        $truncate = $this->getPDO()->prepare($query);

        $this->lastStatement = $truncate;

        $truncate = $truncate->execute($execute);

        return $truncate;
    }

    /**
     * Drop a table.
     *
     * @param string $options
     * @param array $execute
     * @return void
     */
    public function drop(string $options = null, array $execute = []) : bool {
        if (!$this->getTable()) return false;

        $query = "DROP TABLE " . $this->getTable();

        if ($options) $query .= " " . $options;

        $drop = $this->getPDO()->prepare($query);

        $this->lastStatement = $drop;

        $drop = $drop->execute($execute);

        return $drop;
    }

    /**
     * Drop columns of a table
     *
     * @param array $data
     * @param string $options
     * @param array $execute
     * @return boolean
     */
    public function addColumns(array $data, string $options = null, array $execute = []) : bool {
        if (!$this->getTable()) return false;

        $query = "ALTER TABLE " . $this->getTable();

        $i = 0;

        $exc = [];

        foreach ($data as $key => $value) {
            $queryColumn = $this->getPDO()->prepare("SHOW COLUMNS FROM {$this->getTable()} LIKE ?");

            $queryColumn->execute([$key]);

            if (count($queryColumn->fetchAll())) unset($data[$key]);
        }

        foreach ($data as $key => $value) {
            if (count($data) == 1) $query .= " ADD COLUMN {$key} {$value};"; else if ($i == 0) $query .= " ADD COLUMN {$key} {$value}, "; else if ($i != (count($data) - 1)) $query .= "ADD COLUMN {$key} {$value}, "; else $query .= "ADD COLUMN {$key} {$value};";

            $i++;
        }

        if ($options) $query .= " " . $options;

        $exc = array_merge($exc, $execute);

        $add = $this->getPDO()->prepare($query);

        $this->lastStatement = $add;

        $add = $add->execute($exc);

        return $add;
    }

    /**
     * Drop columns of a table
     *
     * @param array $data
     * @param string $options
     * @param array $execute
     * @return boolean
     */
    public function dropColumns(array $data, string $options = null, array $execute = []) : bool {
        if (!$this->getTable()) return false;

        $query = "ALTER TABLE " . $this->getTable();

        $i = 0;

        foreach ($data as $key => $value) {
            $queryColumn = $this->getPDO()->prepare("SHOW COLUMNS FROM {$this->getTable()} LIKE ?");

            $queryColumn->execute([$key]);

            if (!count($queryColumn->fetchAll())) unset($data[$key]);
        }

        foreach ($data as $key) {
            if (count($data) == 1) $query .= " DROP COLUMN {$key};"; else if ($i == 0) $query .= " DROP COLUMN {$key}, "; else if ($i != (count($data) - 1)) $query .= "DROP COLUMN {$key}, "; else $query .= "DROP COLUMN {$key};";

            $i++;
        }

        if ($options) $query .= " " . $options;

        $drop = $this->getPDO()->prepare($query);

        $this->lastStatement = $drop;

        $drop = $drop->execute($execute);

        return $drop;
    }

    /**
     * Import tables (create tables) from a list of tables.
     *
     * @param array $import
     * @return void
     */
    public function import(array $import) : void {
        foreach ($import as $key => $value) {
            $query = $this->setTable($key)->select()->execute();

            if (!$query && !$this->setTable($key)->create($value)) throw new DatabaseException(1, "exception.database.create_table", $key, $this->getLastError());
        }
    }

    /**
     * Update the columns of a list of tables.
     *
     * @param array $update
     * @return void
     */
    public function updateImport(array $update) : void {
        foreach ($update as $key => $value) {
            $query = $this->setTable($key)->select()->execute();

            if ($query && !$this->setTable($key)->addColumns($value)) throw new DatabaseException(2, "exception.database.add_column", $key, $this->getLastError());
        }
    }

    /**
     * Setup the tables to import and update from a Config.
     *
     * @param Config $config
     * @return void
     */
    public function setup(Config $config) : void {
        $import = $config->get("import", []);

        $this->import($import);

        $update = $config->get("update", []);

        $this->updateImport($update);
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
}
