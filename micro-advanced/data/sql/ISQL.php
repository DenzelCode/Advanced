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

namespace advanced\data\sql;

use advanced\data\sql\query\Query;
use PDOStatement;

/**
 * ISQL interface
 */
interface ISQL{

    public function select();

    public function insert();

    public function update();

    public function delete();

    public function create();

    public function truncate();

    public function drop();

    public function prepare(Query $query) : PDOStatement;
}
