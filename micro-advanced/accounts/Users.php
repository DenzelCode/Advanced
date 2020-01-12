<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace advanced\accounts;

use advanced\Bootstrap;
use advanced\exceptions\UserException;
use advanced\accounts\base\User;

/**
 * Users class
 */
class Users {

    private $users = [];

    private static $instance;

    private static $userObject = '\\advanced\\accounts\\User';
    
    private static $guestObject = '\\advanced\\accounts\\Guest';

    public function __construct() {
        self::$instance = $this;

        if (!Bootstrap::getDatabase()) throw new UserException(0, 'exceptions.database.needed');
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
        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], ($limit > 0) ? "LIMIT {$limit}" : "");

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user['id']] = $this->createUser($user);

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
        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], "WHERE username = ?", [$name]);

        $data = $query->fetch();

        if ($data) {
            $this->users[$data['id']] = $this->createUser($data, $authData);

            $return = $this->users[$data['id']];
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
        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], "WHERE id = ?", [$id]);

        $data = $query->fetch();

        if ($data) {
            $this->users[$data['id']] = $this->createUser($data);

            $return = $this->users[$data['id']];
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
        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], "WHERE mail = ?", [$mail]);

        $data = $query->fetch();

        if ($data) {
            $this->users[$data['id']] = $this->createUser($data);

            $return = $this->users[$data['id']];
        }

        return $return;
    }

    /**
     * @return User|null
     */
    public function getUserByConnectionId(string $id, array $authData = []) : ? User {
        $return = null;

        $this->users = [];

        // User
        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], "WHERE connection_id = ?", [$id]);

        $data = $query->fetch();

        if ($data) {
            $this->users[$data['id']] = $this->createUser($data);

            $return = $this->users[$data['id']];
        }

        return $return;
    }

    /**
     * @return User[]|null
     */
    public function getOnlineUsers(int $limit = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], ($limit > 0) ? "WHERE online != '0' LIMIT {$limit}" : "WHERE online != '0'");

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user['id']] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User[]|null
     */
    public function getOfflineUsers(int $limit = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], ($limit > 0) ? "WHERE online = '0' LIMIT {$limit}" : "WHERE online = '0'");

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user['id']] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User[]|null
     */
    public function getLastUsers(int $limit = 1) : ? array {
        return $this->getTopUsers('id', $limit);
    }

    /**
     * @return User[]|null
     */
    public function getRankUsers(int $rank = null, int $limit = 1, bool $occult = true) : ? array {
        $users = [];

        // Users
        if ($rank == null) {
            $rank = Bootstrap::getConfig()->get('hk')['min_rank'];

            $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], ($limit > 0) ? "WHERE rank >= ? ORDER BY rank DESC LIMIT {$limit}" : "WHERE rank >= ? ORDER BY rank DESC", [$rank]);
        } else {
            $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], ($limit > 0) ? "WHERE rank = ? LIMIT {$limit}" : "WHERE rank = ?", [$rank]);
        }


        $data = $query->fetchAll();

        foreach ($data as $user) if ($occult && $user['staff_occult']) continue; else $users[$user['id']] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User[]|null
     */
    public function getRandomUsers(int $limit = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], ($limit > 0) ? "ORDER BY RAND() LIMIT {$limit}" : "ORDER BY RAND()");

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user['id']] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User[]|null
     */
    public function getUsersByIp(string $ip, int $limit = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], ($limit > 0) ? "WHERE ip_last LIKE ? OR ip_reg LIKE ? OR ip_current LIKE ? OR ip_register LIKE ? LIMIT {$limit}" : "WHERE ip_last LIKE ? OR ip_reg LIKE ? OR ip_current LIKE ? OR ip_register LIKE ?", ["%{$ip}%", "%{$ip}%", "%{$ip}%", "%{$ip}%"]);

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user['id']] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User[]|null
     */
    public function getUsersByName(string $name, int $limit = 1, int $from = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], ($limit > 0) ? "WHERE username LIKE ? AND id >= ? LIMIT {$limit}" : "WHERE username LIKE ? AND id >= ?", ["%{$name}%", $from]);

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user['id']] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User[]|null
     */
    public function getUsersByNameAndDisplay(string $name, int $limit = 1, int $from = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], ($limit > 0) ? "WHERE username LIKE ? AND id >= ? OR display_name LIKE ? AND id >= ? LIMIT {$limit}" : "WHERE username LIKE ? OR display_name LIKE ? AND id >= ?", ["%{$name}%", $from, "%{$name}%", $from]);

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user['id']] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User[]|null
     */
    public function getUsersByDisplayName(string $name, int $limit = 1, int $from = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], ($limit > 0) ? "WHERE display_name LIKE ? AND id >= ? LIMIT {$limit}" : "WHERE display_name LIKE ? AND id >= ?", ["%{$name}%", $from]);

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user['id']] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }

    /**
     * @return User[]|null
     */
    public function getTopUsers(string $column, int $limit = 1) : ? array {
        $users = [];

        // Users
        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], ($limit > 0) ? "ORDER BY {$column} DESC LIMIT {$limit}" : "ORDER BY {$column} DESC");

        $data = $query->fetchAll();

        foreach ($data as $user) $users[$user['id']] = $this->createUser($user);

        if (empty($users)) $users = null;

        return $users;
    }
}

