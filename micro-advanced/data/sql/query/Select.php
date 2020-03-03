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

namespace advanced\data\sql\query;

use advanced\data\sql\query\join\FullJoin;
use advanced\data\sql\query\join\IJoin;
use advanced\data\sql\query\join\InnerJoin;
use advanced\data\sql\query\join\Join;
use advanced\data\sql\query\join\LeftJoin;
use advanced\data\sql\query\join\RightJoin;
use PDOStatement;

/**
 * Select class
 */
class Select extends Query{

    /**
     * @var array
     */
    private $columns = ["*"];

    /**
     * @var string|null
     */
    private $distinct = null;

    /**
     * @var array
     */
    private $joins = [];

    /**
     * Set the table that you want to modify.
     *
     * @param string $table
     * @return Select
     */
    public function setTable(string $table) : IQuery {
        return parent::setTable($table);
    }

    /**
     * Set the table that you want to modify.
     *
     * @param string $table
     * @return Select
     */
    public function table(string $table) : IQuery {
        return parent::setTable($table);
    }

    /**
     * Add WHERE parameter to the query.
     *
     * @param string $where
     * @param array $execute
     * @return Select
     */
    public function where(string $where, array $execute = []) : IQuery {
        return parent::where($where, $execute);
    }

    /**
     * Set the list of columns that tou want to select by array
     *
     * @param array $columns
     * @return Select
     */
    public function setColumns(array $columns = ["*"]) : Select {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Set the list of columns that tou want to select by array
     *
     * @param array $columns
     * @return Select
     */
    public function columns(array $columns = ["*"]) : Select {
        return $this->setColumns($columns);
    }

    /**
     * Select distinct value
     *
     * @param string $column
     * @return Select
     */
    public function distinct(string $column = null) : Select {
        $this->distinct = $column;

        return $this;
    }

    /**
     * Join table.
     *
     * @param string $table
     * @return Join
     */
    public function join(string $table) : IJoin {
        return ($this->joins[] = new Join($this, $table));
    }

    /**
     * Left join table.
     *
     * @param string $table
     * @return LeftJoin
     */
    public function leftJoin(string $table) : IJoin {
        return ($this->joins[] = new LeftJoin($this, $table));
    }

    /**
     * Inner join table.
     *
     * @param string $table
     * @return InnerJoin
     */
    public function innerJoin(string $table) : IJoin {
        return ($this->joins[] = new InnerJoin($this, $table));
    }

    /**
     * Right join table.
     *
     * @param string $table
     * @return RightJoin
     */
    public function rightJoin(string $table) : IJoin {
        return ($this->joins[] = new RightJoin($this, $table));
    }

    /**
     * Full join table.
     *
     * @param string $table
     * @return FullJoin
     */
    public function fullJoin(string $table) : IJoin {
        return ($this->joins[] = new FullJoin($this, $table));
    }

    /**
     * Execute the Query and return an PDOStatement Object so you can fetch results.
     *
     * @return PDOStatement
     */
    public function execute() {
        parent::execute();

        return $this->prepare;
    }

    public function getError() : ?string {
        return $this->prepare = null || empty($this->prepare->errorInfo()[2]) ? null : $this->prepare->errorInfo()[2];
    }

    /**
    * Generate the query string of the object
    *
    * @return string
    */
    public function convertToQuery() : string {
        $query = "SELECT " . (!empty($this->distinct) ? "DISTINCT {$this->distinct} " : "");

        $query .= join(", ", $this->columns);

        $query .= !empty($this->table) ? " FROM " . $this->table : "";

        foreach ($this->joins as $join) $query .= " " . $join->convertToQuery();

        $query .= !empty($this->where) ? " WHERE {$this->where}" : "";

        return $query;
    }
}
