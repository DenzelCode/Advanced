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

namespace advanced\user\provider;

use advanced\Bootstrap;
use advanced\user\IUser;

/**
 * MySQLProvider class
 */
class MySQLProvider implements IProvider{

    /**
     * @param IUser $user
     * @return array
     */
    public function getAll(IUser $user) : array {
        return Bootstrap::getSQL()->select()->table("users")->where("id = ?", [$user->getId()])->execute()->fetch();
    }

    /**
     * @param IUser $user
     * @param array $data
     * @return boolean
     */
    public function set(IUser $user, array $data) : bool {
        return Bootstrap::getSQL()->update()->table("users")->fields($data)->where("id = ?", [$user->getId()])->execute();
    }

    /**
     * @param array $data
     * @return boolean
     */
    public function create(array $data) : bool {
        return Bootstrap::getSQL()->insert()->table("users")->fields($data)->execute();
    }
    
    /**
     * @param IUser $user
     * @return boolean
     */
    public function delete(IUser $user) : bool {
        return Bootstrap::getSQL()->delete()->table("users")->where("id = ?", [$user->getId()])->execute();
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return array
     */
    public function getUserBy(string $field, $value) : array {
        return Bootstrap::getSQL()->select()->table("users")->where("{$field} = ?", [$value])->limit(1)->execute()->fetch();
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param integer $limit
     * @param string|null $orderBy
     * @return array
     */
    public function getUsersBy(string $field, $value, int $limit = 0, ?string $orderBy = null) : array {
        return Bootstrap::getSQL()->select()->table("users")->where("{$field} = ?", [$value])->orderBy($orderBy)->limit($limit)->execute()->fetch();
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param integer $limit
     * @param string|null $orderBy
     * @return array
     */
    public function getUsersByMultiple(string $fields, array $values, int $limit = 0, ?string $orderBy = null) : array {
        return Bootstrap::getSQL()->select()->table("users")->where($fields, $values)->orderBy($orderBy)->limit($limit)->execute()->fetch();
    }
}

