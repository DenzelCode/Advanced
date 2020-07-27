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

namespace advanced\sql;

use advanced\sql\ISQL;
use advanced\sql\query\AddColumns;
use advanced\sql\query\Create;
use advanced\sql\query\Delete;
use advanced\sql\query\Drop;
use advanced\sql\query\DropColumns;
use advanced\sql\query\Insert;
use advanced\sql\query\ModifyColumns;
use advanced\sql\query\Query;
use advanced\sql\query\Select;
use advanced\sql\query\ShowColumns;
use advanced\sql\query\Truncate;
use advanced\sql\query\Update;
use advanced\sql\table\ITable;
use advanced\sql\table\Table;
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
     * @var boolean
     */
    protected $connected = false;

    /**
     * @var PDOStatement
     */
    protected $lastStatement;

    /**
     * @return void
     */
    abstract public function run() : void;

    /**
     * Set the table to which you want to make a query.
     *
     * @param string $table
     * @return ITable
     */
    public function table(string $table): ITable {
        return new Table($this, $table);
    }

    /**
     * Get if the SQL instance is connected.
     *
     * @return boolean
     */
    public function isConnected(): bool {
       return $this->connected; 
    }

    /**
     * Generate a select query.
     * Recommendation: Use $sql->table("table")->select(); instead.
     *
     * @param string|null $table
     * @return Select
     */
    public function select(?string $table = null) : Select {
        return (new Select($this->table($table)));
    }

    /**
     * Generate an insert query.
     * Recommendation: Use $sql->table("table")->insert(); instead.
     *
     * @param string|null $table
     * @return Insert
     */
    public function insert(?string $table = null) : Insert {
        return (new Insert($this->table($table)));
    }

    /**
     * Generate an update query.
     * Recommendation: Use $sql->table("table")->update(); instead.
     *
     * @param string|null $table
     * @return Update
     */
    public function update(?string $table = null) : Update {
        return (new Update($this->table($table)));
    }

    /**
     * Generate a delete query.
     * Recommendation: Use $sql->table("table")->delete(); instead.
     *
     * @param string|null $table
     * @return Delete
     */
    public function delete(?string $table = null) : Delete {
        return (new Delete($this->table($table)));
    }

    /**
     * Generate a create query.
     * Recommendation: Use $sql->table("table")->create(); instead.
     *
     * @param string|null $table
     * @return Create
     */
    public function create(?string $table = null) : Create {
        return (new Create($this->table($table)));
    }
    
    /**
     * Generate a drop query.
     * Recommendation: Use $sql->table("table")->drop(); instead.
     *
     * @param string|null $table
     * @return Drop
     */
    public function drop(?string $table = null) : Drop {
        return (new Drop($this->table($table)));
    }

    /**
     * Generate a query to show table columns.
     * Recommendation: Use $sql->table("table")->showColumns(); instead.
     *
     * @param string|null $table
     * @return ShowColumns
     */
    public function showColumns(?string $table = null) : ShowColumns {
        return (new ShowColumns($this->table($table)));
    }

    /**
     * Generate a query to add columns into a table. 
     * Recommendation: Use $sql->table("table")->addColumns(); instead.
     *
     * @param string|null $table
     * @return AddColumns
     */
    public function addColumns(?string $table = null) : AddColumns {
        return (new AddColumns($this->table($table)));
    }

    /**
     * Generate a query to modify columns from a table.
     * Recommendation: Use $sql->table("table")->modifyColumns(); instead.
     *
     * @param string|null $table
     * @return ModifyColumns
     */
    public function modifyColumns(?string $table = null) : ModifyColumns {
        return (new ModifyColumns($this->table($table)));
    }

    /**
     * Generate a query to drop columns from a table.
     * Recommendation: Use $sql->table("table")->dropColumns(); instead.
     *
     * @param string|null $table
     * @return DropColumns
     */
    public function dropColumns(?string $table = null) : DropColumns {
        return (new DropColumns($this->table($table)));
    }

    /**
     * Generate a query to truncate a table.
     * Recommendation: Use $sql->table("table")->truncate(); instead.
     *
     * @param string|null $table
     * @return Truncate
     */
    public function truncate(?string $table = null) : Truncate {
        return (new Truncate($this->table($table)));
    }

    /**
     * Prepare the query.
     * 
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
