<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */
namespace advanced\account;

use advanced\account\base\User as BaseUser;
use advanced\Bootstrap;
use advanced\data\Database;
use advanced\exceptions\UserException;
use advanced\account\Auth;

/**
 * User class
 */
class User extends BaseUser {

    public function __construct(array $data, array $authData = []) {
        $this->data = $data;

        $this->authData = $authData;

        if (!$this->exists()) {
            $config = Bootstrap::getConfig();

            $userChars = strlen($this->getName());

            $min = $config->get('sign_up.min_characters');

            $max = $config->get('sign_up.max_characters');

            if ($userChars < $min || $userChars > $max) {
                throw new UserException(0, 'user.characters', $min, $max);
            } if (!self::isValidName($this->getName())) {
                throw new UserException(1, 'user.invalid_name');
            } else if (!empty($this->getMail()) && !self::isValidMail($this->getMail())) {
                throw new UserException(1, 'user.invalid_email');
            } else {
                $create = $this->create();

                if (!$create) throw new UserException(1, 'exceptions.database.error', Bootstrap::getDatabase()->getLastStatement()->errorInfo()[2]);
            }
        }

        $name = strtolower($this->getName());

        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], "WHERE id = ? AND username = ?", [$this->getId(), $name]);

        switch (true) {
            case $this->getName() != "" && $this->getId() == 0:
                $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], "WHERE username = ?", [$name]);
                break;
            case $this->getName() == "" && $this->getId() != 0:
                $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], "WHERE id = ?", [$this->getId()]);
                break;
        }

        $data = array_merge($this->getDataArray(), $query->fetch());

        if (!empty($data)) $this->setDataArray($data);
    }

    /**
    * @return bool
    */
    public function authenticate(bool $cookie = false, array $data = []) : bool {
        foreach ($data as $key => $value) $this->setAuthData($key, $value);

        if ($this->exists()) {
            if (empty($this->getAuthDataArray())) return false;

            $this->setAuthData('cookie', $cookie);

            $auth = Auth::attempt($this->getAuthDataArray(), $this);

            return $auth;
        }

        return false;
    }
    
    public function set(array $data) : void {
        Bootstrap::getDatabase()->setTable('users')->update($data, "WHERE id = ?", [$this->getId()]);

        foreach ($data as $key => $value) $this->setData($key, $value);
    }
    /**
     * @return array
     */
    public function getAll() : array {
        $query = Bootstrap::getDatabase()->setTable('users')->select(['*'], "WHERE id = ? AND username = ?", [$this->getId(), $this->getName()]);

        $this->setDataArray($query->fetch());

        if (!empty($this->getDataArray())) return $this->getDataArray(); else return false;
    }

    /**
     * @return bool
     */
    protected function create() : bool {
        $insert = Bootstrap::getDatabase()->setTable('users')->insert($this->getDataArray());

        return $insert;
    }

    public function delete() : bool {
        if (!$this->exists()) return false;

        return Bootstrap::getDatabase()->setTable('users')->delete("WHERE id = ?", [$this->getId()]);
    }

    public function exists() : bool {
        $name = strtolower($this->getName());

        $query = Bootstrap::getDatabase()->setTable('users')->select(['username'], "WHERE username = ?", [$name]);

        $exist = $query->fetchAll();

        return (count($exist) ? true : false);
    } 
}

