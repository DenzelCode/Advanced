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

namespace advanced\sql;

use advanced\config\Config;
use advanced\sql\query\AddColumns;
use advanced\sql\query\Create;
use advanced\sql\query\Delete;
use advanced\sql\query\Drop;
use advanced\sql\query\DropColumns;
use advanced\sql\query\Insert;
use advanced\sql\query\ModifyColumns;
use advanced\sql\query\Query;
use advanced\sql\query\Select;
use advanced\sql\query\ShowColumns;
use advanced\sql\query\Truncate;
use advanced\sql\query\Update;
use advanced\sql\table\ITable;
use PDOStatement;

/**
 * ISQL interface
 */
interface ISQL{

    public function isConnected(): bool;

    public function table(string $table): ITable;

    public function select(?string $table = null) : Select;

    public function insert(?string $table = null) : Insert;

    public function update(?string $table = null) : Update;

    public function delete(?string $table = null) : Delete;

    public function create(?string $table = null) : Create;

    public function truncate(?string $table = null) : Truncate;

    public function drop(?string $table = null) : Drop;

    public function showColumns(?string $table = null) : ShowColumns;

    public function addColumns(?string $table = null) : AddColumns;

    public function modifyColumns(?string $table = null) : ModifyColumns;

    public function dropColumns(?string $table = null) : DropColumns;

    public function prepare(Query $query) : PDOStatement;

    public function setLastStatement(PDOStatement $statement) : void;

    public function getLastStatement() : ?PDOStatement;

    public function getLastError() : string;

    public function import(array $import) : void;

    public function modify(array $tables) : void;

    public function setup(Config $config) : void;
}
