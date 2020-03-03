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

    /**
     * @var ISQL
     */
    protected $sql = null;

    /**
     * @var string
     */
    protected $table = null;

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
     * @param ISQL $sql
     */
    public function __construct(ISQL $sql) {
        $this->sql = $sql;
    }

    /**
     * Set the table that you want to modify.
     *
     * @param string $table
     * @return IQuery
     */
    public function setTable(string $table) : IQuery {
        $this->table = $table;

        return $this;
    }

    /**
     * Set the table that you want to modify.
     *
     * @param string $table
     * @return IQuery
     */
    public function table(string $table) : IQuery {
        return $this->setTable($table);
    }

    /**
     * Get the table that you want to modify.
     *
     * @return string|null
     */
    public function getTable() : ?string {
        return $this->table;
    }

    /**
     * Set the WHERE SQL parameter.
     *
     * @param string $where
     * @param array $execute
     * @return IQuery
     */
    public function where(string $where, array $execute = []) : IQuery {
        $this->where = $where;

        $this->execute = $execute;

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
    * Generate the query string of the object.
    *
    * @return string
    */
    public abstract function convertToQuery() : string;

    /**
     * @return string
     */
    public function __toString() {
        return $this->convertToQuery();
    }
}
