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

use advanced\Bootstrap;
use PDO;
use advanced\exceptions\DatabaseException;
use advanced\config\Config;
use advanced\data\Database;

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
        if (!extension_loaded("pdo")) {
            throw new DatabaseException(0, "exception.database.pdo_required");

            return;
        }

        self::$instance = $this;

        if ($db instanceof Database) $this->con = $db->getPDO();

        $this->host = $db instanceof Database ? $db->getHost() : $host;
        $this->port = $db instanceof Database ? $db->getPort() : $port;
        $this->username = $db instanceof Database ? $db->getUsername() : $username;
        $this->password = $db instanceof Database ? $db->getPassword() : $password;
        $this->database = $db instanceof Database ? $db->getDatabase() : $database;

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

    /**
     * Import tables (create tables) from a list of tables.
     *
     * @param array $import
     * @return void
     * @throws DatabaseException
     */
    public function import(array $import) : void {
        foreach ($import as $table => $columns) {
            $query = $this->select()->table($table)->executeBool();

            if (!$query && !$this->create()->table($table)->columns($columns)->execute()) throw new DatabaseException(1, "exception.database.create_table", $table, $this->getLastError());
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
            $query = $this->select()->table($table)->executeBool();

            if (!$query) throw new DatabaseException(2, "exception.database.modify_column", $table, $this->getLastError());
    
            $colms = [];

            foreach ($this->showColumns()->table($table)->execute()->fetchAll() as $column) $colms[] = $colms["Field"];

            foreach ($columns as $column => $type) {
                $execute = in_array($column, $colms) ? $this->addColumns()->table($table)->column($column, $type) : $this->modifyColumns()->table($table)->column($column, $type);

                if (!$execute) throw new DatabaseException(2, "exception.database.modify_column", $table, $this->getLastError());
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
}
