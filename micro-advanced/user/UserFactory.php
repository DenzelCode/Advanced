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

use advanced\Bootstrap;
use advanced\user\User;
use advanced\user\provider\IProvider;
use advanced\user\provider\MySQLProvider;

/**
 * UserFactory class
 */
class UserFactory {

    /**
     * @var UserFactory|null
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
     * @throws DatabaseException
     */
    public function __construct() {
        self::$instance = $this;

        self::$provider = new MySQLProvider(Bootstrap::getSQL());
    }

    public static function setup(): void {
        self::$provider->setup();
    }

    /**
     * @return UserFactory
     */
    public static function getInstance() : ?UserFactory {
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
     * @param string|null $password If you want to sign in using $user->authenticate(), put the non-hashed password here.
     * @return User
     */
    public function createUser(array $data, ?string $password = null) : User {
        $user = new self::$userObject($data, $password);

        return $user;
    }

    /**
     * Get user by username.
     *
     * @param string $name
     * @param string|null $password If you want to sign in using $user->authenticate(), put the non-hashed password here.
     * @return User
     */
    public function getUser(string $name, ?string $password = null) : ?User {
        $return = null;

        if (($data = self::$provider->getUserBy("username", $name))) return $this->createUser($data, $password);

        return $return;
    }

    /**
     * Get user by ID.
     *
     * @param integer $id
     * @param string|null $password If you want to sign in using $user->authenticate(), put the non-hashed password here.
     * @return User
     */
    public function getUserById(int $id, ?string $password = null) : ?User {
        $return = null;

        if (($data = self::$provider->getUserBy("id", $id))) return $this->createUser($data, $password);

        return $return;
    }

    /**
     * Get user by mail.
     *
     * @param string $mail
     * @param string|null $password If you want to sign in using $user->authenticate(), put the non-hashed password here.
     * @return User
     */
    public function getUserByMail(string $mail, ?string $password = null) : ?User {
        $return = null;

        if (($data = self::$provider->getUserBy("mail", $mail))) return $this->createUser($data, $password);

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
}

