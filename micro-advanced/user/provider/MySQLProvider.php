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

use advanced\Bootstrap;
use advanced\config\Config;
use advanced\data\Database;
use advanced\sql\ISQL;
use advanced\exceptions\DatabaseException;
use advanced\user\IUser;

/**
 * MySQLProvider class
 */
class MySQLProvider implements IProvider{

    /**
     * @var ISQL
     */
    protected $sql;

    /**
     * @var string
     */
    protected $table = "users";

    /**
     * Initialize provider
     *
     * @param ISQL $sql
     * @throws DatabaseException
     */
    public function __construct(ISQL $sql) {
        if (!Bootstrap::getSQL()) throw new DatabaseException(0, "exception.database.needed");

        $this->sql = $sql;

        $this->setup();
    }

    /**
     * Setup the users tables.
     *
     * @return void
     */
    public function setup(): void{
        $config = new Config(Database::getConfigPath());

        $config->setIfNotExists("import.{$this->table}", [
            "id" => "int(11) PRIMARY KEY AUTO_INCREMENT",
            "username" => "varchar(255)",
            "firstname" => "varchar(255)",
            "lastname" => "varchar(255)",
            "password" => "varchar(255)",
            "mail" => "varchar(255)",
            "country" => "varchar(4)",
            "gender" => "enum('M', 'F') DEFAULT 'M'",
            "account_created" => "double(50, 0) DEFAULT 0",
            "last_used" => "double(50, 0) DEFAULT 0",
            "last_online" => "double(50, 0) DEFAULT 0",
            "last_password" => "double(50, 0) DEFAULT 0",
            "online" => "enum('0', '1') DEFAULT '0'",
            "ip_reg" => "varchar(45) NOT NULL",
            "ip_last" => "varchar(45) NOT NULL",
            "language" => "varchar(255) DEFAULT 'en'",
            "connection_id" => "text",
            "birth_date" => "varchar(55)",
            "facebook_id" => "text",
            "facebook_token" => "text",
            "facebook_account" => "boolean DEFAULT false"
        ]);

        $config->saveIfModified();

        Bootstrap::getSQL()->setup($config);
    }

    /**
     * @param IUser $user
     * @return array
     */
    public function getAll(IUser $user) : array {
        $fetch = $this->sql->table($this->table)->select()->where((!empty($user->getName()) && !empty($user->getId()) ? "id = ? AND username = ?" : (!empty($user->getName()) ? "username = ?" : "id = ?")), (!empty($user->getName()) && !empty($user->getId()) ? [$user->getId(), $user->getName()] : (!empty($user->getName()) ? [$user->getName()] : [$user->getId()])))->fetch();

        return !$fetch ? [] : $fetch;
    }

    /**
     * @param IUser $user
     * @param array $data
     * @return boolean
     */
    public function set(IUser $user, array $data) : bool {
        return $this->sql->table($this->table)->update()->fields($data)->where("id = ?", [$user->getId()])->execute();
    }

    /**
     * @param array $data
     * @return boolean
     */
    public function create(array $data) : bool {
        $insert = $this->sql->table($this->table)->insert()->fields($data);

        return $insert->execute();
    }
    
    /**
     * @param IUser $user
     * @return boolean
     */
    public function delete(IUser $user) : bool {
        return $this->sql->table($this->table)->delete()->where("id = ?", $user->getId())->execute();
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return array
     */
    public function getUserBy(string $field, $value) : array {
        $fetch = $this->sql->table($this->table)->select()->where("{$field} = ?", $value)->limit(1)->fetch();

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
        $fetchAll = $this->sql->table($this->table)->select()->where("{$field} = ?", $value)->orderBy($orderBy)->limit($limit)->fetchAll();

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
        $fetchAll = $this->sql->table($this->table)->select()->where("{$field} = ?", $value)->orderBy($orderBy)->limit($limit)->fetchAll();

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
        $fetchAll = $this->sql->table($this->table)->select()->where($fields, $values)->orderBy($orderBy)->limit($limit)->fetch();

        return !$fetchAll ? [] : $fetchAll;
    }

    /**
     * Get users table name.
     *
     * @return string
     */
    public function getTable(): string {
        return $this->table;
    }

    /**
     * Set users table name.
     *
     * @param string $table
     * @return void
     */
    public function setTable(string $table): void {
        $this->table = $table;
    }
}

