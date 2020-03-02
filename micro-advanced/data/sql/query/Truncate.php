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

/**
 * Truncate class
 */
class Truncate extends Query{

    /**
     * Set the table that you want to modify.
     *
     * @param string $table
     * @return Truncate
     */
    public function setTable(string $table) : IQuery {
        return parent::setTable($table);
    }

    /**
     * Set the table that you want to modify
     *
     * @param string $table
     * @return Truncate
     */
    public function table(string $table) : IQuery {
        return parent::setTable($table);
    }

    /**
    * Generate the query string of the object
    *
    * @return string
    */
    public function convertToQuery() : string {
        $query = "TRUNCATE TABLE {$this->table}";
        
        return $query;
    }
}
