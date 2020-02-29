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
class Update extends Query{

    private $fields = [];

    private $values = [];

    public function setField(string $field, $value) : Update {
        $this->fields[] = $field;

        $this->values[] = $value;

        return $this;
    }
    
    public function setFieldsByArray(array $data) : Update {
        foreach ($data as $key => $value) $this->setField($key, $value);

        return $this;
    }

    public function execute(): bool {
        $this->execute = array_merge($this->values, $this->execute);

        return parent::execute();
    }

    public function convertToQuery() : string {
        $query = "UPDATE " . $this->table . " ";

        foreach ($this->fields as $i => $field) $query .= $i != (count($this->fields) == 1) ? "SET {$field} = ? " : ($i == 0 ? "SET {$field} = ?, " : ($i != (count($this->fields) != 1) ? "{$field} = ?, " : "{$field} = ?"));

        $query .= !empty($this->where) ? " WHERE {$this->where}" : "";
        
        return $query;
    }
}
