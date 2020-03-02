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
    public function setTable(string $table) : Delete {
        return parent::setTable($table);
    }

    /**
     * Set the table that you want to modify.
     *
     * @param string $table
     * @return Delete
     */
    public function table(string $table) : Delete {
        return parent::setTable($table);
    }

    /**
     * Convert object to query.
     *
     * @return string
     */
    public function convertToQuery() : string {
        $query = "DELETE FROM {$this->table}";

        $query .= !empty($this->where) ? " WHERE {$this->where}" : "";
        
        return $query;
    }
}
