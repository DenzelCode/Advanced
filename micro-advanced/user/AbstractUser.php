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

use advanced\user\Guest;
use advanced\Bootstrap;
use advanced\user\auth\Auth;
use advanced\user\provider\IProvider;

/**
 * User abstract class
 */
abstract class AbstractUser implements IUser {

    /**
     * @var IProvider
     */
    protected static $provider;

    /**
     * @var array
     */
    protected $authData = [];

    /**
     * @return int
     */
    public function getId() : int {
        return (int) $this->data['id'];
    }

    /**
     * @return string
     */
    public function getName() : string {
        return (string) $this->data['username'];
    }

    /**
     * @return string
     */
    public function getFirstName() : string {
        return (string) $this->data['firstname'];
    }

    /**
     * @return string
     */
    public function getLastName() : string {
        return (string) $this->data['lastname'];
    }

    /**
     * @return string
     */
    public function getFullName() : string {
        return $this->getFirstName() . $this->getLastName();
    }

    /**
     * @return string
     */
    public function getGender() : string {
        return (string) $this->data['gender'];
    }

    /**
     * @return string
     */
    public function getMail() : string {
        return (string) $this->data['mail'];
    }

    /**
     * @return string
     */
    public function getPassword() : string {
        return (string) $this->data['password'];
    }

    /**
     * @return string
     */
    public function getRegisterIp() : string {
        return (string) $this->data['ip_reg'];
    }

    /**
     * @return string
     */
    public function getLastIp() : string {
        return (string) $this->data['ip_last'];
    }

    /**
     * @return int
     */
    public function getAccountCreated() : int {
        return (int) $this->data['account_created'];
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function get(string $data) {
        return $this->data[$data];
    }

    /**
     * @return bool
     */
    abstract public function delete() : bool;

    /**
     * @param boolean $cookie
     * @param array $data
     * @return boolean
     */
    abstract public function authenticate(bool $cookie = false, array $data = []) : bool;

    /**
     * @return boolean
     */
    abstract public function create() : bool;

    /**
     * @return bool
     */
    abstract public function exists() : bool;

    /**
     * @param array $data
     * @return void
     */
    abstract public function set(array $data) : void;

    /**
     * @return array
     */
    abstract public function getAll() : array;

    /**
     * @param string $mail
     * @return boolean
     */
    public static function isValidMail(string $mail) : bool {
        return filter_var($mail, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @return array
     */
    public function getData() : array {
        return $this->data;
    }

    /**
     * @param array $data
     * @return void
     */
    public function setDataArray(array $data) {
        foreach ($data as $key => $value) $this->data[$key] = $value;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setData(string $name, $value) {
        $this->data[$name] = $value;
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function getAuthData(string $data) {
        return $this->authData[$data];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setAuthData(string $name, $value) {
        $this->authData[$name] = $value;
    }

    /**
     * @return array
     */
    public function getAuthDataArray() : array {
        return $this->authData;
    }

    /**
     * @param array $authData
     * @return void
     */
    public function setAuthDataArray(array $authData = []) {
        foreach ($authData as $key => $value) $this->authData[$key] = $value;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public static function isValidName(string $name) : bool {
        $config = Bootstrap::getConfig();

        if (!$config->has("sign_up")) $config->set("sign_up.min_characters", 4)->set("sign_up.max_characters", 32)->save();

        $userCheck = preg_match('/^(?=.*[a-zA-Z]{1,})(?=.*[\d]{0,})[a-zA-Z0-9=?!@:.-]{' . $config->get("sign_up.min_characters", 4) . ',' . $config->get("sign_up.max_characters", 32) . '}$/', $name);

        $invalidNames = ['guest', strtolower((new Guest())->getName())];

        return $userCheck && !in_array(strtolower($name), $invalidNames) && !self::isValidMail($name);
    }

    /**
     * @param string $name
     * @return boolean
     */
    public static function isValidDisplayName(string $name) : bool {
        return !preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $name);
    }

    /**
     * @param integer $length
     * @return string
     */
    public static function generateToken(int $length = 40) : string {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz+';
        $token = "";

        for ($i = 0; $i < $length; $i++) $token .= $characters[mt_rand(0, strlen($characters) - 1)];

        return $token;
    }

    /**
     * Verify if the password match with user password.
     *
     * @param string $password
     * @return boolean
     */
    public function verify(string $password) : bool {
        return Auth::verify($password, $this->getPassword());
    }
}

