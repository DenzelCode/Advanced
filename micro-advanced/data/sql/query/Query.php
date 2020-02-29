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

use advanced\data\sql\ISQL;
use PDOStatement;

/**
 * Query class
 */
abstract class Query implements IQuery{

    protected $sql;

    protected $table;

    protected $execute;

    protected $prepare;

    public function __construct(ISQL $sql) {
        $this->sql = $sql;
    }

    public function setTable(string $table) : void {
        $this->table = $table;
    }

    public function getTable() : ?string {
        return $this->table;
    }

    public function where(string $where, array $execute = []) : Query {
        $this->where = $where;

        $this->execute = $execute;

        return $this;
    }

    public function execute() : bool {
        $prepare = $this->sql->prepare($this);

        $this->prepare = $prepare;

        return $prepare->execute($this->execute);
    }

    public function getPrepare() : PDOStatement {
        return $this->prepare;
    }

    protected abstract function convertToQuery() : string;

    public function __toString() {
        return $this->convertToQuery();
    }
}
