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
 * DropColumns class
 */
class DropColumns extends Query{

    private $columns = [];

    /**
     * Set a column that you want to drop by string.
     *
     * @param string $column
     * @return DropColumns
     */
    public function setColumn(string $column) : DropColumns {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * Set a column that you want to drop by string.
     *
     * @param string $column
     * @return DropColumns
     */
    public function column(string $column) : DropColumns {
        $this->setColumn($column);
        
        return $this;
    }
    
    /**
     * Set the columns list that you want to drop by array.
     *
     * @param array $columns
     * @return DropColumns
     */
    public function setColumnsByArray(array $columns) : DropColumns {
        foreach ($columns as $key => $value) $this->setColumn($key, $value);

        return $this;
    }

    /**
     * Set the columns list that you want to drop by array.
     *
     * @param array $columns
     * @return DropColumns
     */
    public function columns(array $columns) : DropColumns {
        $this->setColumnsByArray($columns);

        return $this;
    }

    /**
     * Set the columns list that you want to drop by array.
     *
     * @param array $datcolumnsa
     * @return DropColumns
     */
    public function setColumns(array $columns) : DropColumns {
        $this->setColumnsByArray($columns);

        return $this;
    }

    /**
     * Execute the query
     *
     * @return boolean
     */
    public function execute() {
        $this->execute = $this->columns;

        return parent::execute();
    }

   /**
    * Generate the query string of the object
    *
    * @return string
    */
    public function toQuery() : string {
        $query = "ALTER TABLE {$this->table}";

        for ($i = 0; $i < count($this->columns); $i++) $query .= $i != (count($this->columns) == 1) ? " DROP COLUMN {$this->columns[$i]};" : ($i == 0 ? " DROP COLUMN {$this->columns[$i]}, " : ($i != (count($this->columns) != 1) ? "DROP COLUMN {$this->columns[$i]}, " : "DROP COLUMN {$this->columns[$i]};"));
        
        return $query;
    }
}
