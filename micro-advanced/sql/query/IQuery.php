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

use advanced\sql\table\ITable;

/**
 * IQuery class
 */
interface IQuery{

    public function getTable() : ?ITable;

    /**
     * Add WHERE parameter to the query.
     *
     * @param string $where
     * @param array $execute
     * @return IQuery
     */
    public function where(string $where, $execute = []) : IQuery;

    public function toQuery() : string;

    /**
     * Execute the query.
     *
     * @return boolean
     */
    public function execute();

    /**
     * Get error string if there is a problem with the Query.
     *
     * @return string|null
     */
    public function getError() : ?string;
}
