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

namespace advanced\sql\query;

use advanced\sql\query\join\FullJoin;
use advanced\sql\query\join\IJoin;
use advanced\sql\query\join\InnerJoin;
use advanced\sql\query\join\Join;
use advanced\sql\query\join\LeftJoin;
use advanced\sql\query\join\RightJoin;
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
     * @var Join[]
     */
    private $joins = [];

    /**
     * @var ?string
     */
    private $order = null;

    /**
     * Set the ORDER BY attribute to the SQL query.
     *
     * @param string|null $table
     * @return Select
     */
    public function orderBy(?string $by) : Select {
        $this->order = $by;

        return $this;
    }

    /**
     * Set the LIMIT attribute to the SQL query.
     *
     * @param int $limit
     * @return Select
     */
    public function setLimit(int $limit) : IQuery {
        return parent::setLimit($limit);
    }

    /**
     * Set the LIMIT attribute to the SQL query.
     *
     * @param int $limit
     * @return Select
     */
    public function limit(int $limit) : IQuery {
        return parent::limit($limit);
    }

    /**
     * Set the WHERE SQL parameter.
     *
     * @param string $where Set where example: "name = ?" or "name = ? AND last = ?".
     * @param mixed $execute Set values example "Denzel" or ["Denzel", "Code"].
     * @return Select
     */
    public function where(string $where, $execute = []) : IQuery {
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
     * Fetch all the results.
     *
     * @return array
     */
    public function fetchAll() : array {
        return $this->execute()->fetchAll();
    }

    /**
     * Fetch the first result false if is a bad query.
     *
     * @return array
     */
    public function fetch() : array {
        $data = $this->execute()->fetch();

        return $data == false ? [] : $data;
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

    /**
     * Execute the query.
     *
     * @return bool
     */
    public function executeBool() {
        return parent::execute();
    }

    /**
    * Generate the query string of the object
    *
    * @return string
    */
    public function toQuery() : string {
        $query = "SELECT " . (!empty($this->distinct) ? "DISTINCT {$this->distinct} " : "");

        $query .= join(", ", $this->columns);

        $query .= !empty($this->table) ? " FROM {$this->table}" : "";

        foreach ($this->joins as $join) $query .= " " . $join->toQuery();

        $query .= !empty($this->where) ? " WHERE {$this->where}" : "";

        $query .= !empty($this->order) ? " ORDER BY {$this->order}" : "";

        $query .= $this->limit > 0 ? " LIMIT {$this->limit}" : "";

        return $query;
    }
}
