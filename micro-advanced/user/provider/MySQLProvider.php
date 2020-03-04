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
use advanced\data\sql\ISQL;
use advanced\user\IUser;
use project\Project;

/**
 * MySQLProvider class
 */
class MySQLProvider implements IProvider{

    /**
     * @var ISQL
     */
    protected $sql;

    public function __construct(ISQL $sql) {
        $this->sql = $sql;
    }

    /**
     * @param IUser $user
     * @return array
     */
    public function getAll(IUser $user) : array {
        $fetch = $this->sql->select()->table("users")->where((!empty($user->getName()) && !empty($user->getId()) ? "id = ? AND username = ?" : (!empty($user->getName()) ? "username = ?" : "id = ?")), (!empty($user->getName()) && !empty($user->getId()) ? [$user->getId(), $user->getName()] : (!empty($user->getName()) ? [$user->getName()] : [$user->getId()])))->execute()->fetch();

        return !$fetch ? [] : $fetch;
    }

    /**
     * @param IUser $user
     * @param array $data
     * @return boolean
     */
    public function set(IUser $user, array $data) : bool {
        return $this->sql->update()->table("users")->fields($data)->where("id = ?", [$user->getId()])->execute();
    }

    /**
     * @param array $data
     * @return boolean
     */
    public function create(array $data) : bool {
        $insert = $this->sql->insert()->table("users")->fields($data);

        return $insert->execute();
    }
    
    /**
     * @param IUser $user
     * @return boolean
     */
    public function delete(IUser $user) : bool {
        return $this->sql->delete()->table("users")->where("id = ?", [$user->getId()])->execute();
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return array
     */
    public function getUserBy(string $field, $value) : array {
        $fetch = $this->sql->select()->table("users")->where("{$field} = ?", [$value])->limit(1)->execute()->fetch();

        return !$fetch ? [] : $fetch;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param integer $limit
     * @param string|null $orderBy
     * @return array
     */
    public function getUsersBy(string $field, $value, int $limit = 0, ?string $orderBy = null) : array {
        $fetchAll = $this->sql->select()->table("users")->where("{$field} = ?", [$value])->orderBy($orderBy)->limit($limit)->execute()->fetchAll();

        return !$fetchAll ? [] : $fetchAll;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param integer $limit
     * @param string|null $orderBy
     * @return array
     */
    public function getUsersNotEqual(string $field, $value, int $limit = 0, ?string $orderBy = null) : array {
        $fetchAll = $this->sql->select()->table("users")->where("{$field} = ?", [$value])->orderBy($orderBy)->limit($limit)->execute()->fetchAll();

        return !$fetchAll ? [] : $fetchAll;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param integer $limit
     * @param string|null $orderBy
     * @return array
     */
    public function getUsersByMultiple(string $fields, array $values, int $limit = 0, ?string $orderBy = null) : array {
        $fetchAll = $this->sql->select()->table("users")->where($fields, $values)->orderBy($orderBy)->limit($limit)->execute()->fetch();

        return !$fetchAll ? [] : $fetchAll;
    }
}

