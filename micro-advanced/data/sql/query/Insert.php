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
 * Insert class
 */
class Insert extends Query{

    private $fields = [];

    private $values = [];

    public function setField(string $field, $value) : Insert {
        $this->fields[] = $field;

        $this->values[] = $value;

        return $this;
    }
    
    public function setFieldsByArray(array $data) : Insert {
        foreach ($data as $key => $value) $this->setField($key, $value);

        return $this;
    }

    public function convertToQuery() : string {
        $query = "INSERT INTO " . $this->table . " (";

        foreach ($this->fields as $i => $field) $query .= $i != (count($this->fields) - 1) ? "{$field}, " : $field;

        $query .= ") VALUES (";

        for ($i = 0; $i < (count($this->values) - 1); $i++) $query .= $i != (count($this->values) - 1) ? "?, " : "?";

        $query .= ")";

        return $query;
    }
}
