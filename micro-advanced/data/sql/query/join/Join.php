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

use advanced\data\sql\query\Query;
use advanced\data\sql\query\Select;
use PDOStatement;

/**
 * Join class
 */
class Join implements IJoin {

    protected $query;

    protected $table;

    protected $on;

    protected $as;

    protected $using = [];

    public function __construct(Select $query, string $table) {
        $this->query = $query;
    }

    public function on(string $on) : Join {
        $this->on = $on;

        return $this;
    }

    public function as(string $as) : Join {
        $this->as = $as;

        return $this;
    }

    public function using(array $columns) : Join {
        $this->using = $columns;

        return $this;
    }

    public function join(string $table) : IJoin {
        return $this->query->join($table);
    }

    public function where(string $where, array $execute = []) : Query {
        return $this->query->where($where, $execute);
    }

    public function execute() : PDOStatement {
        return $this->query->execute();
    }

    public function getPreffix(): string {
        return "JOIN";
    }

    public function convertToQuery(): string {
        $query = "{$this->getPreffix()} {$this->table}";

        $query .= !empty($this->on) ? " AS ($this->on)" : "";

        $query .= !empty($this->on) ? " ON ($this->on)" : "";

        $query .= !empty($this->on) ? " USING (" . join(", ", $this->using) . ")" : "";

        return $query;
    }
}
