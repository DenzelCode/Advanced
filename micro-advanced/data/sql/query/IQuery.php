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

/**
 * IQuery class
 */
interface IQuery{

    public function getTable() : ?string;

    /**
     * Add WHERE parameter to the query.
     *
     * @param string $where
     * @param array $execute
     * @return IQuery
     */
    public function where(string $where, array $execute = []) : IQuery;

    public function convertToQuery() : string;

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
