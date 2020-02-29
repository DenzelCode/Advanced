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
 * Create class
 */
class Create extends Query{

    private $columns = [];

    private $types = [];

    public function setColumn(string $column, $value) : Create {
        $this->columns[] = $column;

        $this->types[] = $value;

        return $this;
    }
    
    public function setColumnsByArray(array $data) : Create {
        foreach ($data as $key => $value) $this->setColumn($key, $value);

        return $this;
    }

    public function convertToQuery() : string {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table . " ( ";

        for ($i = 0; $i < (count($this->columns - 1)); $i++) $query .= $i != (count($this->fields) - 1) ? "{$this->columns[$i]} {$this->types[$i]}, " :  "{$this->columns[$i]} {$this->types[$i]} ";

        return $query;
    }
}
