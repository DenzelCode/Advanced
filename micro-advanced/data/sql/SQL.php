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

use advanced\data\sql\ISQL;
use advanced\data\sql\query\AddColumns;
use advanced\data\sql\query\Create;
use advanced\data\sql\query\Delete;
use advanced\data\sql\query\Drop;
use advanced\data\sql\query\DropColumns;
use advanced\data\sql\query\Insert;
use advanced\data\sql\query\ModifyColumns;
use advanced\data\sql\query\Query;
use advanced\data\sql\query\Select;
use advanced\data\sql\query\ShowColumns;
use advanced\data\sql\query\Truncate;
use advanced\data\sql\query\Update;
use PDOStatement;

/**
 * SQL abstract class
 */
abstract class SQL implements ISQL{

    /**
     * @var PDO
     */
    protected $con;

    /**
     * @var PDOStatement
     */
    protected $lastStatement;

    /**
     * @return void
     */
    abstract public function run() : void;

    /**
     * Generate a select query.
     *
     * @param string|null $table
     * @return Select
     */
    public function select(?string $table = null) : Select {
        return (new Select($this, $table));
    }

    /**
     * Generate an insert query.
     *
     * @param string|null $table
     * @return Insert
     */
    public function insert(?string $table = null) : Insert {
        return (new Insert($this, $table));
    }

    /**
     * Generate an update query.
     *
     * @param string|null $table
     * @return Update
     */
    public function update(?string $table = null) : Update {
        return (new Update($this, $table));
    }

    /**
     * Generate a delete query.
     *
     * @param string|null $table
     * @return Delete
     */
    public function delete(?string $table = null) : Delete {
        return (new Delete($this, $table));
    }

    /**
     * Generate a create query.
     *
     * @param string|null $table
     * @return Create
     */
    public function create(?string $table = null) : Create {
        return (new Create($this, $table));
    }
    
    /**
     * Generate a drop query.
     *
     * @param string|null $table
     * @return Drop
     */
    public function drop(?string $table = null) : Drop {
        return (new Drop($this, $table));
    }

    /**
     * Generate a query to show table columns.
     *
     * @param string|null $table
     * @return ShowColumns
     */
    public function showColumns(?string $table = null) : ShowColumns {
        return (new ShowColumns($this, $table));
    }

    /**
     * Generate a query to add columns into a table. 
     *
     * @param string|null $table
     * @return AddColumns
     */
    public function addColumns(?string $table = null) : AddColumns {
        return (new AddColumns($this, $table));
    }

    /**
     * Generate a query to modify columns from a table.
     *
     * @param string|null $table
     * @return ModifyColumns
     */
    public function modifyColumns(?string $table = null) : ModifyColumns {
        return (new ModifyColumns($this, $table));
    }

    /**
     * Generate a query to drop columns from a table.
     *
     * @param string|null $table
     * @return DropColumns
     */
    public function dropColumns(?string $table = null) : DropColumns {
        return (new DropColumns($this, $table));
    }

    /**
     * Generate a query to truncate a table.
     *
     * @param string|null $table
     * @return Truncate
     */
    public function truncate(?string $table = null) : Truncate {
        return (new Truncate($this, $table));
    }

    /**
     * Prepare the query.
     * @param Query $query
     * @return PDOStatement
     */
    public function prepare(Query $query) : PDOStatement {
        return $this->con->prepare((string) $query);
    }

    /**
     * Set last statement. 
     *
     * @param PDOStatement $statement
     * @return void
     */
    public function setLastStatement(PDOStatement $statement) : void {
        $this->lastStatement = $statement;
    }

    /**
     * Get last executed prepared statement.
     *
     * @return PDOStatement|null
     */
    public function getLastStatement() : ?PDOStatement {
        return $this->lastStatement;
    }

    /**
     * Get last query error.
     *
     * @return string
     */
    public function getLastError() : string {
        return $this->getLastStatement()->errorInfo()[2];
    }
}
