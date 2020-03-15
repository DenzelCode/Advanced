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

namespace advanced\data\sql\query\join;

use advanced\data\sql\query\IQuery;
use advanced\data\sql\query\Select;
use PDOStatement;

/**
 * Join class
 */
class Join implements IJoin {

    /**
     * @var Select
     */
    protected $query;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $on;

    /**
     * @var string
     */
    protected $as;

    /**
     * @var array
     */
    protected $using = [];

    /**
     * @param Select $query
     * @param string $table
     */
    public function __construct(Select $query, string $table) {
        $this->query = $query;

        $this->table = $table;
    }

    /**
     * @param string $on
     * @return Join
     */
    public function on(string $on) : Join {
        $this->on = $on;

        return $this;
    }

    /**
     * @param string $as
     * @return Join
     */
    public function as(string $as) : Join {
        $this->as = $as;

        return $this;
    }

    /**
     * @param array $columns
     * @return Join
     */
    public function using(array $columns) : Join {
        $this->using = $columns;

        return $this;
    }


    /**
     * @param string $table
     * @return IJoin
     */
    public function join(string $table) : IJoin {
        return $this->query->join($table);
    }

    /**
     * Set the WHERE SQL parameter.
     *
     * @param string $where Set where example: "name = ?" or "name = ? AND last = ?".
     * @param mixed $execute Set values example "Denzel" or ["Denzel", "Code"].
     * @return IQuery
     */
    public function where(string $where, $execute = []) : IQuery {
        return $this->query->where($where, $execute);
    }

    /**
     * Execute query and return PDOStatement.
     * 
     * @return PDOStatement
     */
    public function execute() : PDOStatement {
        return $this->query->execute();
    }

    /**
     * @return string
     */
    public function getPreffix(): string {
        return "JOIN";
    }

    /**
     * Convert Object to Query string. 
     *
     * @return string
     */
    public function toQuery(): string {
        $query = "{$this->getPreffix()} {$this->table}";

        $query .= !empty($this->as) ? " AS ($this->as)" : "";

        $query .= !empty($this->on) ? " ON ($this->on)" : "";

        $query .= !empty($this->using) ? " USING (" . join(", ", $this->using) . ")" : "";

        return $query;
    }
}
