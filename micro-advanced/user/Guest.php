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
use advanced\http\router\Request;

/**
 * Guest class
 */
class Guest implements IUser {

    /**
     * @var array
     */
    protected $data;

    public function __construct() {
        $config = Bootstrap::getConfig();

        if (!$config->has("sign_up.user")) $config->set("sign_up.user", [])->save();

        $signup = $config->get("sign_up");

        $data = [
            "id" => 0,
            "username" => Bootstrap::getMainLanguage()->get("general.guest"),
            "firstname" => Bootstrap::getMainLanguage()->get("general.guest"),
            "password" => "",
            "mail" => "guest@example.com",
            "lastname" => "",
            "rank" => 1,
            "gender" => "M",
            "ip_last" => Request::getInstance()->getIp(),
            "ip_reg" => Request::getInstance()->getIp(),
            "display_name" => Bootstrap::getMainLanguage()->get("general.guest")
        ];

        foreach ((!empty($signup["user"]) ? $signup["user"] : []) as $key => $value) $data[$key] = $value;

        $this->setByArray($data);
    }

    /**
     * Set guest data value.
     *
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function set(string $key, $value): bool {
        return $this->setByArray([ $key => $value ]);
    }

    /**
     * Set guest data by array.
     *
     * @param array $values
     * @return boolean
     */
    public function setByArray(array $values) : bool {
        foreach ($values as $key => $value) $this->data[$key] = $value;

        return true;
    }

    /**
     * Get user data.
     *
     * @param string $key
     * @return void
     */
    public function get(string $key) {
        return $this->data[$key];
    }

    /**
     * Get guest id.
     *
     * @return integer
     */
    public function getId() : int {
        return $this->data["id"];
    }

    /**
     * Get guest name.
     *
     * @return string
     */
    public function getName() : string {
        return $this->data["username"];
    }

    /**
     * Get guest first name.
     *
     * @return string
     */
    public function getFirstName() : string {
        return $this->data["firstname"];
    }

    /**
     * Get last name.
     *
     * @return string
     */
    public function getLastName() : string {
        return $this->data["lastname"];
    }

    /**
     * Get full name.
     *
     * @return string
     */
    public function getFullName() : string {
        return $this->getFirstName() . " " . $this->getLastName();
    }

    /**
     * Get gender.
     *
     * @return string
     */
    public function getGender() : string {
        return $this->data["gender"];
    }

    /**
     * Get mail.
     *
     * @return string
     */
    public function getMail() : string {
        return $this->data["mail"];
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword() : string {
        return $this->data["password"];
    }

    /**
     * Get IP of registration
     *
     * @return string
     */
    public function getRegisterIp() : string {
        return $this->data["ip_reg"];
    }

    /**
     * Get last IP.
     *
     * @return string
     */
    public function getLastIp() : string {
        return $this->data["ip_last"];
    }

    /**
     * Get all data as array.
     *
     * @return array
     */
    public function getAll(): array {
        return (is_array($this->data) ? $this->data : []);
    }
}

