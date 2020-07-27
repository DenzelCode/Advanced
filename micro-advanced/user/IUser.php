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

namespace advanced\user;

/**
 * IUser interface
 */
interface IUser{

    public function get(string $data);

    public function getAll() : array;

    public function set(string $key, $value) : bool;

    public function setByArray(array $values);

    public function getId() : int;

    public function getName() : string;

    public function getFirstName() : string;

    public function getLastName() : string;

    public function getFullName() : string;

    public function getGender() : string;

    public function getMail() : string;

    public function getPassword() : string;

    public function getRegisterIp() : string;

    public function getLastIp() : string;
}

