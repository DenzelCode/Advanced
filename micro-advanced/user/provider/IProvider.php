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

namespace advanced\user\provider;

use advanced\user\IUser;

/**
 * IProvider interface
 */
interface IProvider{

    public function setup(): void;

    public function getAll(IUser $user) : array;

    public function set(IUser $user, array $data) : bool;

    public function create(array $data) : bool;

    public function delete(IUser $user) : bool;

    public function getUserBy(string $field, $value) : array;

    public function getUsersBy(string $field, $value, int $limit = 0, ?string $orderBy = null) : array;

    public function getUsersByMultiple(string $fields, array $values, int $limit = 0, ?string $orderBy = null) : array;

    public function getUsersNotEqual(string $field, $value, int $limit = 0, ?string $orderBy = null) : array;
}

