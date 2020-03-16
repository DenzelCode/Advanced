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
use advanced\user\auth\Auth;
use advanced\mailer\Mail;
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
            } else if (!$this->create()) throw new UserException(3, "exception.database.error", Bootstrap::getSQL()->getLastError());
        }

        $name = strtolower($this->getName());

        $fetch = UsersFactory::getProvider()->getAll($this);

        if ($fetch) $this->data = $fetch;
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
        Mail::sendMail($server, $subject, $body, null, new Receipient($this->getName(), $this->getMail()));
    }
    
    /**
     * Update the data from the table.
     *
     * @return void
     */
    public function updateData() : void {
        $this->data = UsersFactory::getProvider()->getAll($this);
    }

    /**
     * Delete user.
     * 
     * @return bool
     */
    public function delete() : bool {
        if (!$this->exists()) return false;

        return UsersFactory::getProvider()->delete($this);
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
     * Create user.
     * 
     * @return boolean
     */
    public function create() : bool {
        return UsersFactory::getProvider()->create($this->data);
    }

    /**
     * @return boolean
     */
    public function exists() : bool {
        $data = UsersFactory::getProvider()->getAll($this);

        return !empty($data);
    }

    /**
     * @param array $data
     * @return void
     */
    public function set(array $data) : void {
        UsersFactory::getProvider()->set($this, $data);

        foreach ($data as $key => $value) $this->data[$key] = $value;
    }

    /**
     * @return array
     */
    public function getAll() : array {
        return !empty($this->data) ? $this->data : [];
    }
}

