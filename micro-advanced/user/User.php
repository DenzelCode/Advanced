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
use advanced\user\Auth;
use advanced\mailer\Mailer;
use advanced\mailer\Receipient;

/**
 * User class
 */
class User extends AbstractUser {

    /**
     * Create a user instance.
     *
     * @param array $data
     * @param array $authData
     * @throws UserException
     */
    public function __construct(array $data, array $authData = []) {
        $this->data = $data;

        $this->authData = $authData;

        UsersFactory::setupTable();

        if (!$this->exists()) {
            $config = Bootstrap::getMainConfig();

            $min = $config->get("sign_up.min_characters", 4);

            $max = $config->get("sign_up.max_characters", 32);

            if (strlen($this->getName()) < $min || strlen($this->getName()) > $max) {
                throw new UserException(0, "exception.user.characters", $min, $max);
            } if (!self::isValidName($this->getName())) {
                throw new UserException(1, "exception.user.invalid_name");
            } else if (!empty($this->getMail()) && !self::isValidMail($this->getMail())) {
                throw new UserException(2, "exception.user.invalid_email");
            } else if (!$this->create()) throw new UserException(3, "exception.database.error", Bootstrap::getDatabase()->getLastStatement()->errorInfo()[2]);
        }

        $name = strtolower($this->getName());

        $query = Bootstrap::getDatabase()->setTable("users")->select(["*"], (!empty($this->getName()) && !empty($this->getId()) == 0 ? "WHERE id = ? AND username = ?" : (!empty($this->getName()) ? "WHERE username = ?" : "WHERE id = ?")), (!empty($this->getName()) && !empty($this->getId()) == 0 ? [$this->getId(), $name] : (!empty($this->getName()) ? [$name] : [$this->getId()])));

        if (!empty(($fetch = $query->fetch()))) $this->data = $fetch;
    }

    /**
     * Send mail into the user.
     *
     * @param string $server
     * @param string $subject
     * @param string $body
     * @return void
     */
    public function sendMail(string $server, string $subject, string $body) : void {
        Mailer::sendMail($server, $subject, $body, new Receipient($this->getName(), $this->getMail()));
    }

    /**
     * Authenticate account.
     *
     * @param boolean $cookie
     * @param array $data
     * @return boolean
     */
    public function authenticate(bool $cookie = false, array $data = []) : bool {
        foreach ($data as $key => $value) $this->authData[$key] = $value;

        if ($this->exists()) {
            if (empty($this->authData)) return false;

            $this->authData["cookie"] = $cookie;

            $auth = Auth::attempt($this->authData, $this);

            return $auth;
        }

        return false;
    }
    
    /**
     * Set a data into the object and mm
     *
     * @param array $data
     * @return void
     */
    public function set(array $data) : void {
        Bootstrap::getDatabase()->setTable("users")->update($data, "WHERE id = ?", [$this->getId()]);

        foreach ($data as $key => $value) $this->data[$key] = $value;
    }

    /**
     * @return array
     */
    public function getAll() : ?array {
        return $this->data ?? null;
    }

    /**
     * Update the data from the table.
     *
     * @return void
     */
    public function updateData() : void {
        $query = Bootstrap::getDatabase()->setTable("users")->select(["*"], "WHERE id = ? AND username = ?", [$this->getId(), $this->getName()]);
        
        $this->data = $query->fetch();
    }

    /**
     * Create.
     *
     * @return boolean
     */
    public function create() : bool {
        return Bootstrap::getDatabase()->setTable("users")->insert($this->data);
    }

    /**
     * Delete user.
     *
     * @return boolean
     */
    public function delete() : bool {
        if (!$this->exists()) return false;

        return Bootstrap::getDatabase()->setTable("users")->delete("WHERE id = ?", [$this->getId()]);
    }

    /**
     * Check if user exists
     *
     * @return boolean
     */
    public function exists() : bool {
        $name = strtolower($this->getName());

        $query = Bootstrap::getDatabase()->setTable("users")->select(["username"], "WHERE username = ?", [$name]);

        $exist = $query->fetchAll();

        return (bool) count($exist);
    }
}

