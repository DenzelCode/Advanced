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

use PDOStatement;

/**
 * Update class
 */
class Delete extends Query{

    /**
     * Set the table that you want to modify.
     *
     * @param string $table
     * @return Delete
     */
    public function setTable(string $table) : IQuery {
        return parent::setTable($table);
    }

    /**
     * Set the table that you want to modify.
     *
     * @param string $table
     * @return Delete
     */
    public function table(string $table) : IQuery {
        return parent::setTable($table);
    }

    /**
     * Set the LIMIT attribute to the SQL query.
     *
     * @param int $limit
     * @return Delete
     */
    public function setLimit(int $limit) : IQuery {
        return parent::setLimit($limit);
    }

    /**
     * Set the LIMIT attribute to the SQL query.
     *
     * @param int $limit
     * @return Delete
     */
    public function limit(int $limit) : IQuery {
        return parent::limit($limit);
    }

    /**
     * Add WHERE parameter to the query.
     *
     * @param string $where
     * @param array $execute
     * @return Delete
     */
    public function where(string $where, array $execute = []) : IQuery {
        return parent::where($where, $execute);
    }

    /**
     * Convert object to query.
     *
     * @return string
     */
    public function convertToQuery() : string {
        $query = "DELETE FROM {$this->table}";

        $query .= !empty($this->where) ? " WHERE {$this->where}" : "";

        $query .= $this->limit > 0 ? " LIMIT {$this->limit}" : "";
        
        return $query;
    }
}
