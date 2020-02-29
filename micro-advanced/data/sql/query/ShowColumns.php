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
 * ShowColumns class
 */
class ShowColumns extends Query implements Prepared{

    private $like = null;

    public function execute() : PDOStatement {
        parent::execute();

        return $this->prepare;
    }

    public function like(string $like) : ShowColumns {
        $this->like = $like;

        $this->execute[] = $like;

        return $this;
    }

    public function convertToQuery() : string {
        $query = "SHOW COLUMNS FROM {$this->table}";

        $query .= !empty($this->like) ? "LIKE ?" : "";

        return $query;
    }
}
