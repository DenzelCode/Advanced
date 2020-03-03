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
     * @return Select
     */
    public function select() : Select {
        return (new Select($this));
    }

    /**
     * @return Insert
     */
    public function insert() : Insert {
        return (new Insert($this));
    }

    /**
     * @return Update
     */
    public function update() : Update {
        return (new Update($this));
    }

    /**
     * @return Delete
     */
    public function delete() : Delete {
        return (new Delete($this));
    }

    /**
     * @return Create
     */
    public function create() : Create {
        return (new Create($this));
    }
    
    /**
     * @return Drop
     */
    public function drop() : Drop {
        return (new Drop($this));
    }

    /**
     * @return ShowColumns
     */
    public function showColumns() : ShowColumns {
        return (new ShowColumns($this));
    }

    /**
     * @return AddColumns
     */
    public function addColumns() : AddColumns {
        return (new AddColumns($this));
    }

     /**
     * @return ModifyColumns
     */
    public function modifyColumns() : ModifyColumns {
        return (new ModifyColumns($this));
    }

    /**
     * @return DropColumns
     */
    public function dropColumns() : DropColumns {
        return (new DropColumns($this));
    }

    /**
     * @return Truncate
     */
    public function truncate() : Truncate {
        return (new Truncate($this));
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
     * @return PDOStatement|null
     */
    public function getLastStatement() : ?PDOStatement {
        return $this->lastStatement;
    }

    /**
     * @return string
     */
    public function getLastError() : string {
        return $this->getLastStatement()->errorInfo()[2];
    }
}
