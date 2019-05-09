<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace advanced\data;

use PDO;
use advanced\exceptions\DatabaseException;

/**
 * Database class
 */
class Database{

    private $con;

    private static $instance;

    private $table = false;

    private $host, $port, $username, $password;

    public function __construct(string $host = '127.0.0.1', int $port = 3306, string $username = 'root', string $password = '', string $database = '') {
        self::$instance = $this;

        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        if (!extension_loaded('pdo')) {
            throw new DatabaseException(0, 'exceptions.database.pdo_required');

            return;
        } 
        
        $this->run();
    }

    public function getInstance() : Database {
        return self::$instance;
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
            throw new DatabaseException(0, 'exceptions.database.connecting', $e->getMessage());
        }
    }

    public function getPDO() : PDO {
        return $this->con;
    }

    public function setTable($table) : Database {
        $this->table = $table;

        return $this;
    }

    public function getTable() {
        return $this->table;
    }

    public function select(array $data = ['*'], string $options = null, array $execute = []) {
        if (!$this->getTable()) return false;

        if (empty($data)) return false;

        $query = 'SELECT ';

        if (strtolower($data[0]) === 'distinct') {
            unset($data[0]);

            $query .= 'DISTINCT ';
        }

        $i = 0;

        foreach ($data as $key) {
            if ($i != (count($data) - 1)) $query .= "{$key}, "; else $query .= "{$key} ";

            $i++;
        }

        $query .= 'FROM ' . $this->getTable();

        if ($options) $query .= ' ' . $options;

        $prepare = $this->getPDO()->prepare($query);

        $execute = $prepare->execute($execute);

        return $prepare;
    }

    public function insert(array $data) {
        if (!$this->getTable()) return false;

        if (empty($data)) return false;

        $query = 'INSERT INTO ' . $this->getTable() . ' (';

        $i = 0;

        foreach ($data as $key => $value) {
            if ($i != (count($data) - 1)) $query .= "{$key}, "; else $query .= "{$key}";

            $i++;
        }

        $query .= ') VALUES (';

        $i = 0;

        $execute = [];

        foreach ($data as $key => $value) {
            if ($i != (count($data) - 1)) $query .= "?, "; else $query .= "?";

            $execute[] = $value;

            $i++;
        }

        $query .= ')';

        $add = $this->getPDO()->prepare($query);

        $add = $add->execute($execute);

        return $add;
    }

    public function update(array $data, string $options = null, array $execute = []) {
        if (!$this->getTable()) return false;

        if (empty($data)) return false;

        $query = 'UPDATE ' . $this->getTable() . ' ';

        $i = 0;

        $exc = [];

        foreach ($data as $key => $value) {
            if (count($data) == 1) $query .= "SET {$key} = ? "; else if ($i == 0) $query .= "SET {$key} = ?, "; else if ($i != (count($data) - 1)) $query .= "{$key} = ?, "; else $query .= "{$key} = ?";

            $exc[] = $value;

            $i++;
        }

        $exc = array_merge($exc, $execute);

        if ($options) $query .= ' ' . $options;

        $update = $this->getPDO()->prepare($query);

        $update = $update->execute($exc);

        return $update;
    }

    public function delete(string $options = null, array $execute = []) {
        if (!$this->getTable()) return false;

        $query = 'DELETE FROM ' . $this->getTable();

        if ($options) $query .= ' ' . $options;

        $delete = $this->getPDO()->prepare($query);

        $delete = $delete->execute($execute);

        return $delete;
    }

    public function create(array $data) {
        if (!$this->getTable()) return false;

        if (empty($data)) return false;

        $query = 'CREATE TABLE IF NOT EXISTS ' . $this->getTable() . ' ( ';

        $i = 0;

        $execute = [];

        foreach ($data as $key => $value) {
            if ($i != (count($data) - 1)) $query .= "{$key} {$value}, "; else $query .= "{$key} {$value} ";

            $execute[] = $value;

            $i++;
        }

        $query .= ')';

        $create = $this->getPDO()->prepare($query);

        $create = $create->execute($execute);

        return $create;
    }

    public function truncate(string $options = null, array $execute = []) {
        if (!$this->getTable()) return false;

        $query = 'TRUNCATE TABLE ' . $this->getTable();

        if ($options) $query .= ' ' . $options;

        $truncate = $this->getPDO()->prepare($query);

        $truncate = $truncate->execute($execute);

        return $truncate;
    }

    public function drop(string $options = null, array $execute = []) {
        if (!$this->getTable()) return false;

        $query = 'DROP TABLE ' . $this->getTable();

        if ($options) $query .= ' ' . $options;

        $drop = $this->getPDO()->prepare($query);

        $drop = $drop->execute($execute);

        return $drop;
    }

    public function addColumn(array $data, string $options = null, array $execute = []) {
        if (!$this->getTable()) return false;

        $query = 'ALTER TABLE ' . $this->getTable();

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

        if ($options) $query .= ' ' . $options;

        $exc = array_merge($exc, $execute);

        $add = $this->getPDO()->prepare($query);

        $add = $add->execute($exc);

        return $add;
    }

    public function dropColumn(array $data, string $options = null, array $execute = []) {
        if (!$this->getTable()) return false;

        $query = 'ALTER TABLE ' . $this->getTable();

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

        if ($options) $query .= ' ' . $options;

        $drop = $this->getPDO()->prepare($query);

        $drop = $drop->execute($execute);

        return $drop;
    }
}
