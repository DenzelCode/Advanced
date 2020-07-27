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

namespace advanced\sql\table;

use advanced\sql\ISQL;
use advanced\sql\query\AddColumns;
use advanced\sql\query\Create;
use advanced\sql\query\Delete;
use advanced\sql\query\Drop;
use advanced\sql\query\DropColumns;
use advanced\sql\query\Insert;
use advanced\sql\query\ModifyColumns;
use advanced\sql\query\Select;
use advanced\sql\query\ShowColumns;
use advanced\sql\query\Truncate;
use advanced\sql\query\Update;

class Table implements ITable {

    /**
     * @var string
     */
    private $name;

    /**
     * @var ISQL
     */
    private $sql;

    public function __construct(ISQL $sql, string $name) {
        $this->sql = $sql;

        $this->name = $name;
    }

    /**
     * Get table name.
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
    
    /**
     * Get SQL instance.
     *
     * @return ISQL
     */
    public function getSQL(): ISQL {
        return $this->sql;
    }

    /**
     * Generate a select query.
     *
     * @param string|null $table
     * @return Select
     */
    public function select() : Select {
        return (new Select($this));
    }

    /**
     * Generate an insert query.
     *
     * @param string|null $table
     * @return Insert
     */
    public function insert() : Insert {
        return (new Insert($this));
    }

    /**
     * Generate an update query.
     *
     * @param string|null $table
     * @return Update
     */
    public function update() : Update {
        return (new Update($this));
    }

    /**
     * Generate a delete query.
     *
     * @param string|null $table
     * @return Delete
     */
    public function delete() : Delete {
        return (new Delete($this));
    }

    /**
     * Generate a create query.
     *
     * @param string|null $table
     * @return Create
     */
    public function create() : Create {
        return (new Create($this));
    }
    
    /**
     * Generate a drop query.
     *
     * @param string|null $table
     * @return Drop
     */
    public function drop() : Drop {
        return (new Drop($this));
    }

    /**
     * Generate a query to show table columns.
     *
     * @param string|null $table
     * @return ShowColumns
     */
    public function showColumns() : ShowColumns {
        return (new ShowColumns($this));
    }

    /**
     * Generate a query to add columns into a table. 
     *
     * @param string|null $table
     * @return AddColumns
     */
    public function addColumns() : AddColumns {
        return (new AddColumns($this));
    }

    /**
     * Generate a query to modify columns from a table.
     *
     * @param string|null $table
     * @return ModifyColumns
     */
    public function modifyColumns() : ModifyColumns {
        return (new ModifyColumns($this));
    }

    /**
     * Generate a query to drop columns from a table.
     *
     * @param string|null $table
     * @return DropColumns
     */
    public function dropColumns() : DropColumns {
        return (new DropColumns($this));
    }

    /**
     * Generate a query to truncate a table.
     *
     * @param string|null $table
     * @return Truncate
     */
    public function truncate() : Truncate {
        return (new Truncate($this));
    }

    /**
     * Check if the table exists.
     *
     * @return boolean
     */
    public function exists(): bool {
        return $this->select()->executeBool();
    }

    public function __toString() {
        return $this->name;
    }
}