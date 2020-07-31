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

namespace advanced\data;

use advanced\Bootstrap;
use PDO;
use advanced\exceptions\DatabaseException;
use advanced\config\Config;
use advanced\sql\MySQL;
use PDOException;
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
    private $host, $port, $username, $password, $database;

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

        if (!extension_loaded("pdo")) {
            throw new DatabaseException(0, "exception.database.pdo_required");

            return;
        }

        if ($mysql != null) $this->con = $mysql;
        
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        (new Config(self::$configPath, [ "import" => [], "update" => [] ]));
        
        $this->run();
    }

    /**
     * @return void
     * @throws DatabaseException
     */
    public function run() : void {
        if ($this->con) return;

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
                    $temp = new PDO("mysql:host={$this->host};port={$this->port};charset=utf8mb4", $this->username, $this->password);

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
        return new Database($mysql->getHost(), $mysql->getPort(), $mysql->getUsername(), $mysql->getPassword(), $mysql->getDatabase(), $mysql);
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
    public function select(array $data = ["*"], string $options = null, array $execute = []) : ?PDOStatement {
        if (!$this->getTable()) return null;

        if (empty($data)) return null;

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
     * @param array $fields
     * @return boolean
     */
    public function insert(array $fields) : bool {
        if (!$this->getTable()) return false;

        if (empty($fields)) return false;

        $query = "INSERT INTO " . $this->getTable() . " (";

        $i = 0;

        foreach ($fields as $key => $value) {
            if ($i != (count($fields) - 1)) $query .= "{$key}, "; else $query .= "{$key}";

            $i++;
        }

        $query .= ") VALUES (";

        $i = 0;

        $execute = [];

        foreach ($fields as $key => $value) {
            if ($i != (count($fields) - 1)) $query .= "?, "; else $query .= "?";

            $execute[] = $value;

            $i++;
        }

        $query .= ")";

        $insert = $this->getPDO()->prepare($query);

        $this->lastStatement = $insert;

        $insert = $insert->execute($execute);

        return $insert;
    }

    /**
     * Update a row from a table.
     *
     * @param array $data
     * @param string $options
     * @param array $fields
     * @return boolean
     */
    public function update(array $fields, string $options = null, array $execute = []) : bool {
        if (!$this->getTable()) return false;

        if (empty($fields)) return false;

        $query = "UPDATE " . $this->getTable() . " ";

        $i = 0;

        $exc = [];

        foreach ($fields as $key => $value) {
            if (count($fields) == 1) $query .= "SET {$key} = ? "; else if ($i == 0) $query .= "SET {$key} = ?, "; else if ($i != (count($fields) - 1)) $query .= "{$key} = ?, "; else $query .= "{$key} = ?";

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
     * @param array $columns
     * @return void
     */
    public function create(array $columns) : bool {
        if (!$this->getTable()) return false;

        if (empty($data)) return false;

        $query = "CREATE TABLE IF NOT EXISTS {$this->table} ( ";

        $i = 0;

        $execute = [];

        foreach ($columns as $key => $value) {
            if ($i != (count($columns) - 1)) $query .= "{$key} {$value}, "; else $query .= "{$key} {$value} ";

            $execute[] = $value;

            $i++;
        }

        $query .= ")";
    
        echo($query);

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

        $query = "TRUNCATE TABLE {$this->table}";

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

        $query = "DROP TABLE {$this->table}";

        if ($options) $query .= " " . $options;

        $drop = $this->getPDO()->prepare($query);

        $this->lastStatement = $drop;

        $drop = $drop->execute($execute);

        return $drop;
    }

    /**
     * Add columns to a table
     *
     * @param array $columns
     * @param string $options
     * @param array $execute
     * @return boolean
     */
    public function addColumns(array $columns, string $options = null, array $execute = []) : bool {
        if (!$this->getTable()) return false;

        $query = "ALTER TABLE {$this->table}";

        $i = 0;

        $exc = [];

        foreach ($columns as $key => $value) {
            if (count($columns) == 1) $query .= " ADD COLUMN {$key} {$value};"; else if ($i == 0) $query .= " ADD COLUMN {$key} {$value}, "; else if ($i != (count($columns) - 1)) $query .= "ADD COLUMN {$key} {$value}, "; else $query .= "ADD COLUMN {$key} {$value};";

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
     * Modify columns of a table
     *
     * @param array $fields
     * @param string $options
     * @param array $execute
     * @return boolean
     */
    public function modifyColumns(array $fields, string $options = null, array $execute = []) : bool {
        if (!$this->getTable()) return false;

        $query = "ALTER TABLE " . $this->getTable();

        $i = 0;

        $exc = [];

        foreach ($fields as $key => $value) {
            if (count($fields) == 1) $query .= " MODIFY COLUMN {$key} {$value};"; else if ($i == 0) $query .= " MODIFY COLUMN {$key} {$value}, "; else if ($i != (count($fields) - 1)) $query .= "MODIFY COLUMN {$key} {$value}, "; else $query .= "MODIFY COLUMN {$key} {$value};";

            $i++;
        }

        if ($options) $query .= " " . $options;

        $exc = array_merge($exc, $execute);

        $modify = $this->getPDO()->prepare($query);

        $this->lastStatement = $modify;

        $modify = $modify->execute($exc);

        return $modify;
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
     * Show columns from a table.
     *
     * @param array $data
     * @param string $options
     * @param array $execute
     * @return PDOStatement|null
     */
    public function showColumns(string $options = null, array $execute = []) : ?PDOStatement {
        if (!$this->getTable()) return null;

        $query = "SHOW COLUMNS FROM " . $this->getTable();

        if ($options) $query .= " " . $options;

        $show = $this->getPDO()->prepare($query);

        $this->lastStatement = $show;

        $show->execute($execute);

        return $show;
    }

    /**
     * Import tables (create tables) from a list of tables.
     *
     * @param array $import
     * @return void
     * @throws DatabaseException
     */
    public function import(array $import) : void {
        foreach ($import as $key => $value) {
            $query = $this->setTable($key)->select()->execute();

            if (!$query && !$this->setTable($key)->create($value)) throw new DatabaseException(1, "exception.database.create_table", $key, $this->getLastError());
        }
    }

    /**
     * Modify/add columns into a list of tables.
     *
     * @param array $tables
     * @return void
     * @throws DatabaseException
     */
    public function modify(array $tables) : void {
        foreach ($tables as $table => $columns) {
            $query = $this->setTable($table)->select()->execute();

            if ($query) throw new DatabaseException(2, "exception.database.add_column", $table, $this->getLastError());

            $colms = [];

            foreach ($this->setTable($table)->showColumns()->fetchAll() as $column) $colms[] = $colms["Field"];

            foreach ($columns as $column => $type) {
                $execute = in_array($column, $colms) ? $this->setTable($table)->addColumns([ $column => $type ]) : $this->setTable($table)->modifyColumns([ $column => $type ]);

                if (!$execute) throw new DatabaseException(3, "exception.database.modify_column", $table, $this->getLastError());
            }
        }
    }

    /**
     * Setup the tables to import and modify from a Config.
     *
     * @param Config $config
     * @return void
     * @throws DatabaseException
     */
    public function setup(Config $config) : void {
        Bootstrap::getConfig()->setIfNotExists("database.setup", true)->saveIfModified();

        if (Bootstrap::getConfig()->get("database.setup", true)) {
            $this->import($config->get("import", []));

            $this->modify($config->get("modify", []));
        }
    }

    /**
     * Get config path.
     * 
     * @return string
     */
    public static function getConfigPath() : string {
        return self::$configPath;
    }

    /**
     * Set config path.
     * 
     * @param string $configPath
     * @return void
     */
    public static function setConfigPath(string $configPath) : void {
        self::$configPath = $configPath;
    }
}
