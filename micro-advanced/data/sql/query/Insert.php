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

    /**
     * Set the table that you want to modify.
     *
     * @param string $table
     * @return Insert
     */
    public function setTable(string $table) : Insert {
        return parent::setTable($table);
    }

    /**
     * Set the table that you want to modify.
     *
     * @param string $table
     * @return Insert
     */
    public function table(string $table) : Insert {
        return parent::setTable($table);
    }

    /**
     * Set the column name and the value that you want to asign to the row.
     *
     * @param string $field
     * @param mixed $value
     * @return Insert
     */
    public function setField(string $field, $value) : Insert {
        $this->fields[] = $field;

        $this->values[] = $value;

        return $this;
    }

    /**
     * Set the column name and the value that you want to asign to the row.
     *
     * @param string $field
     * @param mixed $value
     * @return Insert
     */
    public function field(string $field, $value) : Insert {
        $this->setField($field, $value);

        return $this;
    }
    
    /**
     * Set the column name and the value that you want to asign to the row bu array.
     *
     * @param array $fields
     * @return Insert
     */
    public function setFieldsByArray(array $fields) : Insert {
        foreach ($fields as $key => $value) $this->setField($key, $value);

        return $this;
    }

    /**
     * Set the column name and the value that you want to asign to the row bu array.
     *
     * @param array $fields
     * @return Insert
     */
    public function fields(array $fields) : Insert {
        $this->setFieldsByArray($fields);

        return $this;
    }

    /**
     * Set the column name and the value that you want to asign to the row bu array.
     *
     * @param array $fields
     * @return Insert
     */
    public function setFields(array $fields) : Insert {
        $this->setFieldsByArray($fields);

        return $this;
    }

    /**
    * Generate the query string of the object
    *
    * @return string
    */
    public function convertToQuery() : string {
        $query = "INSERT INTO {$this->table} (";

        foreach ($this->fields as $i => $field) $query .= $i != (count($this->fields) - 1) ? "{$field}, " : $field;

        $query .= ") VALUES (";

        for ($i = 0; $i < (count($this->values) - 1); $i++) $query .= $i != (count($this->values) - 1) ? "?, " : "?";

        $query .= ")";

        return $query;
    }
}
