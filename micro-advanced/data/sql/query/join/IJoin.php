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

namespace advanced\data\sql\query\join;

use advanced\data\sql\query\IQuery;
use PDOStatement;

/**
 * IJoin class
 */
interface IJoin{

    public function on(string $data) : IJoin;

    public function as(string $data) : IJoin;

    public function using(array $columns) : IJoin;

    public function join() : IJoin;

    public function where(string $where, array $execute = []) : IQuery;

    public function execute() : PDOStatement;

    public function getPreffix() : string;

    public function convertToQuery() : string;
}