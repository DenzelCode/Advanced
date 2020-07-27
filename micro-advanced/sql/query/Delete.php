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

use PDOStatement;

/**
 * Update class
 */
class Delete extends Query{

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
     * Set the WHERE SQL parameter.
     *
     * @param string $where Set where example: "name = ?" or "name = ? AND last = ?".
     * @param mixed $execute Set values example "Denzel" or ["Denzel", "Code"].
     * @return Delete
     */
    public function where(string $where, $execute = []) : IQuery {
        return parent::where($where, $execute);
    }

    /**
     * Convert object to query.
     *
     * @return string
     */
    public function toQuery() : string {
        $query = "DELETE FROM {$this->table}";

        $query .= !empty($this->where) ? " WHERE {$this->where}" : "";

        $query .= $this->limit > 0 ? " LIMIT {$this->limit}" : "";
        
        return $query;
    }
}
