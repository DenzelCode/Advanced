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

/**
 * ModifyColumns class
 */
class ModifyColumns extends Query{

    /**
     * @var array
     */
    private $columns = [];

    /**
     * @var array
     */
    private $values = [];

    /**
     * Set a column that you want to modify.
     *
     * @param string $column
     * @param string $value
     * @return ModifyColumns
     */
    public function setColumn(string $column, string $value) : ModifyColumns {
        $this->columns[] = $column;

        $this->values[] = $value;

        return $this;
    }

    /**
     * Set a column that you want to modify.
     *
     * @param string $column
     * @param string $value
     * @return ModifyColumns
     */
    public function column(string $column, string $value) : ModifyColumns {
        $this->setColumn($column, $value);
        
        return $this;
    }
    
    /**
     * Set the columns that you want to modify by array.
     *
     * @param string $column
     * @param string $value
     * @return ModifyColumns
     */
    public function setColumnsByArray(array $data) : ModifyColumns {
        foreach ($data as $key => $value) $this->setColumn($key, $value);

        return $this;
    }

    /**
     * Set the columns that you want to modify by array.
     *
     * @param string $column
     * @param string $value
     * @return ModifyColumns
     */
    public function columns(array $columns) : ModifyColumns {
        $this->setColumnsByArray($columns);

        return $this;
    }

    /**
     * Set the columns that you want to modify by array.
     *
     * @param string $column
     * @param string $value
     * @return ModifyColumns
     */
    public function setColumns(array $data) : ModifyColumns {
        $this->setColumnsByArray($data);

        return $this;
    }

    /**
     * Execute the query.
     *
     * @return boolean
     */
    public function execute() {
        $this->execute = array_merge($this->values, $this->execute);

        return parent::execute();
    }

    /**
     * Convert object to query.
     *
     * @return string
     */
    public function toQuery() : string {
        $query = "ALTER TABLE {$this->table}";

        for ($i = 0; $i < count($this->columns); $i++) $query .= $i != (count($this->columns) == 1) ? " MODIFY COLUMN {$this->columns[$i]} {$this->values[$i]};" : ($i == 0 ? " MODIFY COLUMN {$this->columns[$i]} {$this->values[$i]}, " : ($i != (count($this->columns) != 1) ? "MODIFY COLUMN {$this->columns[$i]} {$this->values[$i]}, " : "MODIFY COLUMN {$this->columns[$i]} {$this->values[$i]};"));
        
        return $query;
    }
}
