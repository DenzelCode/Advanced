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
 * ShowColumns class
 */
class ShowColumns extends Query{

    /**
     * @var string|null
     */
    private $like = null;

    /**
     * Execute the Query and return an PDOStatement Object so you can fetch results.
     *
     * @return PDOStatement
     */
    public function execute() {
        parent::execute();

        return $this->prepare;
    }

    /**
     * Fetch all the results.
     *
     * @return array
     */
    public function fetchAll() : array {
        return $this->execute()->fetchAll();
    }

    /**
     * Fetch the first result false if is a bad query.
     *
     * @return array
     */
    public function fetch() : array {
        return ($this->execute()->fetch() ?? []);
    }

    /**
     * @param string $like
     * @return ShowColumns
     */
    public function like(string $like) : ShowColumns {
        $this->like = $like;

        $this->execute[] = $like;

        return $this;
    }

    /**
    * Generate the query string of the object.
    *
    * @return string
    */
    public function toQuery() : string {
        $query = "SHOW COLUMNS FROM {$this->table}";

        $query .= !empty($this->like) ? "LIKE ?" : "";

        return $query;
    }
}
