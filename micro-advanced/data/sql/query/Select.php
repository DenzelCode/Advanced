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
use advanced\data\sql\query\join\RightJoin;
use PDOStatement;

/**
 * Select class
 */
class Select extends Query implements Prepared{

    private $columns = [];

    private $distinct = null;

    private $joins = [];

    public function setColumns(array $columns = ["*"]) : void {
        $this->columns = $columns;
    }

    public function distinct(string $column = null) : Select {
        $this->distinct = $column;

        return $this;
    }

    public function join(string $table) : IJoin {
        return ($this->joins[] = new Join($this, $table));
    }

    public function leftJoin(string $table, string $on) : IJoin {
        return ($this->joins[] = new Join($this, $table));
    }

    public function innerJoin(string $table, string $on) : IJoin {
        return ($this->joins[] = new InnerJoin($this, $table));
    }

    public function rightJoin(string $table, string $on) : IJoin {
        return ($this->joins[] = new RightJoin($this, $table));
    }

    public function fullJoin(string $table, string $on) : IJoin {
        return ($this->joins[] = new FullJoin($this, $table));
    }

    public function execute(): PDOStatement {
        parent::execute();

        return $this->prepare;
    }

    public function convertToQuery() : string {
        $query = "SELECT " . (!empty($this->distinct) ? "DISTINCT {$this->distinct}" : "");

        foreach ($this->columns as $i => $column) $query .= $i != (count($this->columns) - 1) ? "{$column}, " : $query .= "{$column} ";

        if (!empty($table)) $query .= "FROM " . $this->table;

        foreach ($this->joins as $join) $query .= " " . $join->convertToQuery();

        $query .= !empty($this->where) ? " WHERE {$this->where}" : "";

        return $query;
    }
}
