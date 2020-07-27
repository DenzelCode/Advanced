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
class Update extends Query{

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var array
     */
    private $values = [];

    /**
     * Set the LIMIT attribute to the SQL query.
     *
     * @param int $limit
     * @return Update
     */
    public function setLimit(int $limit) : IQuery {
        return parent::setLimit($limit);
    }

    /**
     * Set the LIMIT attribute to the SQL query.
     *
     * @param int $limit
     * @return Update
     */
    public function limit(int $limit) : IQuery {
        return parent::limit($limit);
    }

    /**
     * Set the WHERE SQL parameter.
     *
     * @param string $where Set where example: "name = ?" or "name = ? AND last = ?".
     * @param mixed $execute Set values example "Denzel" or ["Denzel", "Code"].
     * @return Update
     */
    public function where(string $where, $execute = []) : IQuery {
        return parent::where($where, $execute);
    }

    /**
     * Set the column name and the value that you want to asign to the row.
     *
     * @param string $field
     * @param mixed $value
     * @return Update
     */
    public function setField(string $field, $value) : Update {
        $this->fields[] = $field;

        $this->values[] = $value;

        return $this;
    }

    /**
     * Set the column name and the value that you want to asign to the row.
     *
     * @param string $field
     * @param mixed $value
     * @return Update
     */
    public function field(string $field, $value) : Update {
        return $this->setField($field, $value);
    }
    
    /**
     * Set the column name and the value that you want to asign to the row bu array.
     *
     * @param array $fields
     * @return Update
     */
    public function setFieldsByArray(array $fields) : Update {
        foreach ($fields as $key => $value) $this->setField($key, $value);

        return $this;
    }

    /**
     * Set the column name and the value that you want to asign to the row bu array.
     *
     * @param array $fields
     * @return Update
     */
    public function fields(array $fields) : Update {
        return $this->setFieldsByArray($fields);
    }

    /**
     * Set the column name and the value that you want to asign to the row bu array.
     *
     * @param array $fields
     * @return Update
     */
    public function setFields(array $fields) : Update {
        return $this->setFieldsByArray($fields);
    }

    /**
     * Execute the query.
     *
     * @return boolean
     */
    public function execute(): bool {
        $this->execute = array_merge($this->values, $this->execute);

        return parent::execute();
    }

    /**
    * Generate the query string of the object.
    *
    * @return string
    */
    public function toQuery() : string {
        $query = "UPDATE {$this->table} ";

        foreach ($this->fields as $i => $field) $query .= (count($this->fields) == 1) ? "SET {$field} = ? " : ($i == 0 ? "SET {$field} = ?, " : ($i != (count($this->fields) - 1) ? "{$field} = ?, " : "{$field} = ?"));

        $query .= !empty($this->where) ? " WHERE {$this->where}" : "";

        $query .= $this->limit > 0 ? " LIMIT {$this->limit}" : "";

        return $query;
    }
}
