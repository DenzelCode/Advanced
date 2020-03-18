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

namespace advanced\user;

use advanced\Bootstrap;
use advanced\exceptions\UserException;
use advanced\user\User;
use advanced\config\Config;
use advanced\data\Database;
use advanced\user\provider\IProvider;
use advanced\user\provider\MySQLProvider;

/**
 * UsersFactory class
 */
class UsersFactory {

    /**
     * @var UsersFactory|null
     */
    private static $instance;

    /**
     * @var string
     */
    private static $userObject = "\\advanced\\user\\User";
    
    /**
     * @var string
     */
    private static $guestObject = "\\advanced\\user\\Guest";

    /**
     * @var IProvider
     */
    private static $provider = null;

    /**
     * Initialize factory.
     * 
     * @throws UserException
     */
    public function __construct() {
        if (!Bootstrap::getSQL()) throw new UserException(0, "exception.database.needed");

        self::$instance = $this;
        
        self::$provider = new MySQLProvider(Bootstrap::getSQL());

        $this->setupTable();
    }

    /**
     * @return UsersFactory
     */
    public static function getInstance() : ?UsersFactory {
        return self::$instance;
    }

    /**
     * Get data provider.
     *
     * @return IProvider
     */
    public static function getProvider() : IProvider {
        return self::$provider;
    }

    /**
     * Set data provider.
     *
     * @param IProvider $provider
     * @return void
     */
    public static function setProvider(IProvider $provider) : void {
        self::$provider = $provider;
    }

    /**
     * Get the User object namespace.
     * 
     * @return string
     */
    public static function getUserObject() : string {
        return self::$userObject;
    }

    /**
     * Modify the User object namespace.
     *
     * @param string $object
     * @return void
     */
    public static function setUserObject(string $object) : void {
        self::$userObject = $object;
    }

    /**
     * Get the Guest object namespace.
     *
     * @return string
     */
    public static function getGuestObject() : string {
        return self::$guestObject;
    }

    /**
     * Modify the Guest object namespace.
     *
     * @return string
     */
    public static function setGuestObject(string $object) : void {
        self::$guestObject = $object;
    }

    /**
     * Create user object.
     *
     * @param array $data
     * @param array $authData
     * @return User
     */
    public function createUser(array $data, array $authData = []) : User {
        $user = new self::$userObject($data, $authData);

        return $user;
    }

    /**
     * Get user by username.
     *
     * @param string $name
     * @param array $authData
     * @return User|null
     */
    public function getUser(string $name, array $authData = []) : ?User {
        $return = null;

        if (($data = self::$provider->getUserBy("username", $name))) return $this->createUser($data, $authData);

        return $return;
    }

    /**
     * Get user by ID.
     *
     * @param integer $id
     * @param array $authData
     * @return User|null
     */
    public function getUserById(int $id, array $authData = []) : ?User {
        $return = null;

        if (($data = self::$provider->getUserBy("id", $id))) return $this->createUser($data, $authData);

        return $return;
    }

    /**
     * Get user by mail.
     *
     * @param string $mail
     * @param array $authData
     * @return User|null
     */
    public function getUserByMail(string $mail, array $authData = []) : ?User {
        $return = null;

        if (($data = self::$provider->getUserBy("mail", $mail))) return $this->createUser($data, $authData);

        return $return;
    }

    /**
     * Get last users registered.
     *
     * @param integer $limit
     * @return User[]
     */
    public function getLastUsers(int $limit = 1) : array {
        return $this->getTopUsers("id", $limit);
    }

    /**
     * Get random users
     *
     * @param integer $limit
     * @return User[]
     */
    public function getRandomUsers(int $limit = 1) : array {
        $users = [];

        $data = self::$provider->getUsersNotEqual("username", "", $limit, "RAND()");

        foreach ($data as $user) $users[$user["id"]] = $this->createUser($user);

        return $users;
    }

    /**
     * Get users by IP
     *
     * @param string $ip
     * @param integer $limit
     * @return User[]
     */
    public function getUsersByIp(string $ip, int $limit = 1) : array {
        $users = [];

        // Users
        $data = self::$provider->getUsersByMultiple("ip_last LIKE ? OR ip_reg LIKE ?", [$ip, $ip], $limit);

        foreach ($data as $user) $users[$user["id"]] = $this->createUser($user);

        return $users;
    }

    /**
     * Get users ordered by a column from from highest to lowest.
     *
     * @param string $column
     * @param integer $limit
     * @return array
     */
    public function getTopUsers(string $column, int $limit = 1) : array {
        $users = [];

        // Users
        $data = self::$provider->getUsersNotEqual("username", "", $limit, "{$column} DESC");

        foreach ($data as $user) $users[$user["id"]] = $this->createUser($user);

        return $users;
    }

    /**
     * Setup the users tables.
     *
     * @return void
     */
    public static function setupTable() : void {
        $config = new Config(Database::getConfigPath());

        $config->setIfNotExists("import.users", [
            "id" => "int(11) PRIMARY KEY AUTO_INCREMENT",
            "username" => "varchar(255)",
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

        $config->setIfNotExists("import.ranks", [
            "id" => "int(11) PRIMARY KEY AUTO_INCREMENT",
            "name" => "text",
            "description" => "text",
            "timestamp" => "double(50, 0) DEFAULT 0"
        ]);

        $config->saveIfModified();

        Bootstrap::getSQL()->setup($config);
    }
}

