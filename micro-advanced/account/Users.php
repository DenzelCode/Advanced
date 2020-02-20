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

namespace advanced\account;

use advanced\Bootstrap;
use advanced\exceptions\UserException;
use advanced\account\base\User;
use advanced\config\Config;
use advanced\data\Database;

/**
 * Users class
 */
class Users {

    private $users = [];

    private static $instance;

    private static $userObject = "\\advanced\\account\\User";
    
    private static $guestObject = "\\advanced\\account\\Guest";

    public function __construct() {
        self::$instance = $this;

        if (!Bootstrap::getDatabase()) throw new UserException(0, "exception.database.needed");

        $this->setupTable();
    }

    /**
     * @return Users
     */
    public static function getInstance() : Users {
        return self::$instance;
    }

    public static function getUserObject() : string {
        return self::$userObject;
    }

    public static function setUserObject(string $object) : void {
        self::$userObject = $object;
    }

    public static function getGuestObject() : string {
        return self::$guestObject;
    }

    public static function setGuestObject(string $object) : void {
        self::$guestObject = $object;
    }

    /**
     * @return User
     */
    public function createUser(array $data, array $authData = []) : User {
        $user = new self::$userObject($data, $authData);

        return $user;
    }

    /**
     * @return User[]|null
     */
    public function getUsers(int $limit = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable("users")->select(["*"], ($limit > 0) ? "LIMIT {$limit}" : "");

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user["id"]] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User|null
     */
    public function getUser(string $name, array $authData = []) : ? User {
        $return = null;

        $this->users = [];

        // User
        $query = Bootstrap::getDatabase()->setTable("users")->select(["*"], "WHERE username = ?", [$name]);

        if (($data = $query->fetch())) {
            $this->users[$data["id"]] = $this->createUser($data, $authData);

            $return = $this->users[$data["id"]];
        }

        return $return;
    }

    /**
     * @return User|null
     */
    public function getUserById(int $id, array $authData = []) : ? User {
        $return = null;

        $this->users = [];

        // User
        $query = Bootstrap::getDatabase()->setTable("users")->select(["*"], "WHERE id = ?", [$id]);

        if (($data = $query->fetch())) {
            $this->users[$data["id"]] = $this->createUser($data, $authData);

            $return = $this->users[$data["id"]];
        }

        return $return;
    }

    /**
     * @return User|null
     */
    public function getUserByMail(string $mail, array $authData = []) : ? User {
        $return = null;

        $this->users = [];

        // User
        $query = Bootstrap::getDatabase()->setTable("users")->select(["*"], "WHERE mail = ?", [$mail]);

        if (($data = $query->fetch())) {
            $this->users[$data["id"]] = $this->createUser($data, $authData);

            $return = $this->users[$data["id"]];
        }

        return $return;
    }

    /**
     * @return User[]|null
     */
    public function getLastUsers(int $limit = 1) : ? array {
        return $this->getTopUsers("id", $limit);
    }

    /**
     * @return User[]|null
     */
    public function getRankUsers(int $rank = null, int $limit = 1, bool $occult = true) : ? array {
        $users = [];

        // Users
        if ($rank == null) {
            $rank = Bootstrap::getConfig()->get("hk")["min_rank"];

            $query = Bootstrap::getDatabase()->setTable("users")->select(["*"], "WHERE rank >= ? ORDER BY rank DESC" . ($limit > 0 ? " LIMIT {$limit}" : ""), [$rank]);
        } else {
            $query = Bootstrap::getDatabase()->setTable("users")->select(["*"], "WHERE rank = ?" . ($limit > 0 ? " LIMIT {$limit}" : ""), [$rank]);
        }


        $data = $query->fetchAll();

        foreach ($data as $user) if ($occult && $user["staff_occult"]) continue; else $users[$user["id"]] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User[]|null
     */
    public function getRandomUsers(int $limit = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable("users")->select(["*"], "ORDER BY RAND()" . ($limit > 0 ? " LIMIT {$limit}" : ""));

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user["id"]] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User[]|null
     */
    public function getUsersByIp(string $ip, int $limit = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable("users")->select(["*"], "WHERE ip_last LIKE ? OR ip_reg LIKE ? OR ip_current LIKE ? OR ip_register LIKE ?" . ($limit > 0 ? " LIMIT {$limit}" : ""), ["%{$ip}%", "%{$ip}%", "%{$ip}%", "%{$ip}%"]);

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user["id"]] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User[]|null
     */
    public function getUsersByName(string $name, int $limit = 1, int $from = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable("users")->select(["*"], "WHERE username LIKE ? AND id >= ?" . ($limit > 0 ? " LIMIT {$limit}" : ""), ["%{$name}%", $from]);

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user["id"]] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User[]|null
     */
    public function getUsersByNameAndDisplay(string $name, int $limit = 1, int $from = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable("users")->select(["*"], "WHERE username LIKE ? OR display_name LIKE ? AND id >= ?"  . ($limit > 0 ? " LIMIT {$limit}" : ""), ["%{$name}%", $from, "%{$name}%", $from]);

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user["id"]] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User[]|null
     */
    public function getUsersByDisplayName(string $name, int $limit = 1, int $from = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable("users")->select(["*"], "WHERE display_name LIKE ? AND id >= ?" . ($limit > 0 ? " LIMIT {$limit}" : ""), ["%{$name}%", $from]);

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user["id"]] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User[]|null
     */
    public function getTopUsers(string $column, int $limit = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable("users")->select(["*"], "ORDER BY {$column} DESC" . ($limit > 0 ? " LIMIT {$limit}" : ""));

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user["id"]] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    public static function setupTable() : void {
        Bootstrap::getConfig()->setIfNotExists("database.setup", true)->saveIfModified();

        if (Bootstrap::getConfig()->get("database.setup", true)) {
            $config = new Config(Database::getConfigPath());

            $config->setIfNotExists("import.users", [
                "id" => "int(11) PRIMARY KEY AUTO_INCREMENT",
                "username" => "varchar(125)",
                "firstname" => "varchar(255)",
                "lastname" => "varchar(255)",
                "password" => "varchar(255)",
                "mail" => "varchar(255)",
                "rank" => "int(11)",
                "country" => "varchar(4)",
                "gender" => "enum('M', 'F') DEFAULT 'M'",
                "account_created" => "double(50, 0) DEFAULT 0",
                "last_used" => "double(50, 0) DEFAULT 0",
                "last_online" => "double(50, 0) DEFAULT 0",
                "last_password" => "double(50, 0) DEFAULT 0",
                "online" => "enum('0, '1') DEFAULT '0'",
                "ip_reg" => "varchar(45) NOT NULL",
                "ip_last" => "varchar(45) NOT NULL",
                "language" => "varchar(255) DEFAULT 'en'",
                "connection_id" => "text",
                "birth_date" => "varchar(55)",
                "facebook_id" => "text",
                "facebook_token" => "text",
                "facebook_account" => "boolean DEFAULT false"
            ]);

            $config->setIfNotExists("import.ranks", [
                "id" => "int(11) PRIMARY KEY AUTO_INCREMENT",
                "name" => "text",
                "description" => "text",
                "timestamp" => "double(50, 0) DEFAULT 0"
            ]);

            $config->saveIfModified();

            Bootstrap::getDatabase()->setup($config);
        }
    }
}

