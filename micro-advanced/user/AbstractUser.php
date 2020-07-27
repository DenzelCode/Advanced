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

use advanced\user\Guest;
use advanced\Bootstrap;
use advanced\user\provider\IProvider;

/**
 * User abstract class
 */
abstract class AbstractUser implements IUser
{

    /**
     * @var IProvider
     */
    protected static $provider;

    /**
     * @var string|null
     */
    protected $password = null;

    /**
     * @var string
     */
    protected static $authProvider = "\\advanced\\user\\auth\\Auth";

    /**
     * Get user id.
     * 
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->data['id'];
    }

    /**
     * Get user name.
     * 
     * @return string
     */
    public function getName(): string
    {
        return (string) $this->data['username'];
    }

    /**
     * Get user first name.
     * 
     * @return string
     */
    public function getFirstName(): string
    {
        return (string) $this->data['firstname'];
    }

    /**
     * Get user last name.
     * 
     * @return string
     */
    public function getLastName(): string
    {
        return (string) $this->data['lastname'];
    }

    /**
     * Get user full name.
     * 
     * @return string
     */
    public function getFullName(): string
    {
        return $this->getFirstName() . " " . $this->getLastName();
    }

    /**
     * Get user gender.
     * 
     * @return string
     */
    public function getGender(): string
    {
        return (string) $this->data['gender'];
    }

    /**
     * Get user email.
     * 
     * @return string
     */
    public function getMail(): string
    {
        return (string) $this->data['mail'];
    }

    /**
     * Get user hashed password.
     * 
     * @return string
     */
    public function getPassword(): string
    {
        return (string) $this->data['password'];
    }

    /**
     * Get register IP.
     * 
     * @return string
     */
    public function getRegisterIp(): string
    {
        return (string) $this->data['ip_reg'];
    }

    /**
     * Get last IP.
     * 
     * @return string
     */
    public function getLastIp(): string
    {
        return (string) $this->data['ip_last'];
    }

    /**
     * Get timestamp of the account creation date.
     * 
     * @return int
     */
    public function getAccountCreated(): int
    {
        return (int) $this->data['account_created'];
    }

    /**
     * Get data.
     * 
     * @param string $data
     * @return mixed
     */
    public function get(string $data)
    {
        return $this->data[$data];
    }

    /**
     * Delete account.
     * 
     * @return bool
     */
    abstract public function delete(): bool;

    /**
     * Authenticate account.
     *
     * @param string|null $password
     * @param boolean $cookie
     * @return boolean
     */
    public function authenticate(?string $password = null, bool $cookie = false) : bool {
        if ($password) $this->password = $password;

        if ($this->exists()) {
            if (empty($this->password)) return false;

            return call_user_func_array([self::$authProvider, "attempt"], [$this->password, $this, $cookie]);
        }

        return false;
    }

    /**
     * Create account.
     * 
     * @return boolean
     */
    abstract public function create(): bool;

    /**
     * Chekck if account exists.
     * 
     * @return bool
     */
    abstract public function exists(): bool;

    /**
     * Set a user object value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): bool
    {
        return $this->setByArray([$key => $value]);
    }

    /**
     * Set user object values by array.
     * 
     * @param array $data
     * @return void
     */
    abstract public function setByArray(array $data): bool;

    /**
     * Get all data as array.
     * 
     * @return array
     */
    abstract public function getAll(): array;

    /**
     * Check if an email is valid.
     * 
     * @param string $mail
     * @return boolean
     */
    public static function isValidMail(string $mail): bool
    {
        return filter_var($mail, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Check if a username is valid.
     * 
     * @param string $name
     * @return boolean
     */
    public static function isValidName(string $name): bool
    {
        $config = Bootstrap::getConfig();

        if (!$config->has("sign_up")) $config->set("sign_up.min_characters", 4)->set("sign_up.max_characters", 32)->save();

        $userCheck = preg_match('/^(?=.*[a-zA-Z]{1,})(?=.*[\d]{0,})[a-zA-Z0-9=?!@:.-]{' . $config->get("sign_up.min_characters", 4) . ',' . $config->get("sign_up.max_characters", 32) . '}$/', $name);

        $invalidNames = ['guest', strtolower((new Guest())->getName())];

        return $userCheck && !in_array(strtolower($name), $invalidNames) && !self::isValidMail($name);
    }

    /**
     * Check if a name is valid.
     * 
     * @param string $name
     * @return boolean
     */
    public static function isValidDisplayName(string $name): bool
    {
        return !preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $name);
    }

    /**
     * Generate a random token.
     * 
     * @param integer $length
     * @return string
     */
    public static function generateToken(int $length = 40): string
    {
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
    public function verify(string $password): bool {
        return call_user_func_array([self::$authProvider, "verify"], [$password, $this->getPassword()]);
    }

    /**
     * Set the auth provider class.
     *
     * @param string $class
     * @return void
     */
    public static function setAuthProvider(string $class): void {
        self::$authProvider = $class;
    }

    /**
     * Get auth provider class.
     *
     * @return string
     */
    public static function getAuthProvider(): string {
        return self::$authProvider;
    }
}
