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

namespace advanced\data\sql\table;

use advanced\data\sql\ISQL;
use advanced\data\sql\query\AddColumns;
use advanced\data\sql\query\Create;
use advanced\data\sql\query\Delete;
use advanced\data\sql\query\Drop;
use advanced\data\sql\query\DropColumns;
use advanced\data\sql\query\Insert;
use advanced\data\sql\query\ModifyColumns;
use advanced\data\sql\query\Select;
use advanced\data\sql\query\ShowColumns;
use advanced\data\sql\query\Truncate;
use advanced\data\sql\query\Update;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * ISQL interface
 */
interface ITable
{
    public function getName(): string;

    public function getSQL(): ISQL;

    public function exists(): bool;

    public function select(): Select;

    public function insert(): Insert;

    public function update(): Update;

    public function delete(): Delete;

    public function create(): Create;

    public function truncate(): Truncate;

    public function drop(): Drop;

    public function showColumns(): ShowColumns;

    public function addColumns(): AddColumns;

    public function modifyColumns(): ModifyColumns;

    public function dropColumns(): DropColumns;
}
