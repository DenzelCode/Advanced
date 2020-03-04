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
            "rank" => 1,
            "gender" => "M",
            "ip_last" => Request::getIp(),
            "ip_reg" => Request::getIp(),
            "display_name" => Bootstrap::getMainLanguage()->get("general.guest")
        ];

        foreach ((!empty($signup["user"]) ? $signup["user"] : []) as $key => $value) $data[$key] = $value;

        $this->set($data);
    }

    public function set(array $values) {
        foreach ($values as $key => $value) $this->data[$key] = $value;
    }

    public function get(string $key) {
        return $this->data[$key];
    }

    public function getId() : int {
        return 0;
    }

    public function getName() : string {
        return "Guest";
    }

    public function getFirstName() : string {
        return "Guest";
    }

    public function getLastName() : string {
        return "";
    }

    public function getFullName() : string {
        return "Guest";
    }

    public function getGender() : string {
        return "M";
    }

    public function getMail() : string {
        return "guest@example.com";
    }

    public function getPassword() : string {
        return "";
    }

    public function getRegisterIp() : string {
        return Request::getIp();
    }

    public function getLastIp() : string {
        return Request::getIp();
    }

}

