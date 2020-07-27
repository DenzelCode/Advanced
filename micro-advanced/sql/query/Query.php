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

use advanced\sql\ISQL;
use advanced\sql\table\ITable;
use PDOStatement;

/**
 * Query class
 */
abstract class Query implements IQuery{

    /**
     * @var ISQL
     */
    protected $sql = null;

    /**
     * @var ITable
     */
    protected $table = null;

    /**
     * @var array
     */
    protected $execute = [];

    /**
     * @var PDOStatement
     */
    protected $prepare = null;

    /**
     * @var string
     */
    protected $where = null;

    /**
     * @var integer
     */
    protected $limit = 0;

    /**
     * @param ISQL $sql
     */
    public function __construct(ITable $table = null) {
        $this->sql = $table->getSQL();

        $this->table = $table;
    }

    /**
     * Set the LIMIT attribute to the SQL query.
     *
     * @param int $limit
     * @return IQuery
     */
    public function setLimit(int $limit) : IQuery {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Set the LIMIT attribute to the SQL query.
     *
     * @param int $limit
     * @return IQuery
     */
    public function limit(int $limit) : IQuery {
        return $this->setLimit($limit);
    }

    /**
     * Get the table that you want to modify.
     *
     * @return ITable
     */
    public function getTable() : ?ITable {
        return $this->table;
    }

    /**
     * Set the WHERE SQL parameter.
     *
     * @param string $where Set where example: "name = ?" or "name = ? AND last = ?".
     * @param mixed $execute Set values example "Denzel" or ["Denzel", "Code"].
     * @return IQuery
     */
    public function where(string $where, $execute = []) : IQuery {
        $this->where = $where;

        if (is_array($execute)) $this->execute = $execute; else $this->execute[] = $execute;

        return $this;
    }

    /**
     * Execute the query.
     *
     * @return boolean
     */
    public function execute() {
        $prepare = $this->sql->prepare($this);

        $this->sql->setLastStatement($prepare);

        $this->prepare = $prepare;

        return $prepare->execute($this->execute);
    }

    /**
     * @return PDOStatement|null
     */
    public function getPrepare() : ?PDOStatement {
        return $this->prepare;
    }

    /**
     * Get error string if there is a problem with the Query.
     *
     * @return string|null
     */
    public function getError() : ?string {
        return $this->prepare = null || empty($this->prepare->errorInfo()[2]) ? null : $this->prepare->errorInfo()[2];
    }

    /**
    * Generate the query string of the object.
    *
    * @return string
    */
    public abstract function toQuery() : string;

    /**
     * @return string
     */
    public function __toString() {
        return $this->toQuery();
    }
}
