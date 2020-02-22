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

namespace advanced\account;

use advanced\account\base\User;
use advanced\Bootstrap;
use advanced\http\router\Request;

/**
 * Guest class
 */
class Guest extends User {

    public function __construct() {
        $config = Bootstrap::getConfig();

        if (!$config->has("sign_up.user")) $config->set("sign_up.user", [])->save();

        $signup = $config->get("sign_up");

        $data = [
            "id" => 0,
            "username" => Bootstrap::getMainLanguage()->get("general.guest"),
            "rank" => 1,
            "gender" => (Auth::get("gender") ? Auth::get("gender") : "M"),
            "ip_last" => Request::getIp(),
            "ip_reg" => Request::getIp(),
            "display_name" => Bootstrap::getMainLanguage()->get("general.guest")
        ];

        foreach ((!empty($signup["user"]) ? $signup["user"] : []) as $key => $value) $data[$key] = $value;

        $this->set($data);
    }

    public function set(array $data) : void {
        $keys = ["username", "id", "password"];

        foreach ($data as $key => $value) {
            if (!in_array($key, array_keys($keys))) Auth::set([ $key => $value ]);

            $this->data[$key] = $value;
        }
    }

    /**
     * @return bool
     */
    protected function create() : bool {
        return true;
    }

    public function delete() : bool {
        return false;
    }

    public function exists() : bool {
        return false;
    }

    public function getAll() : array {
        return $this->data;
    }

    public function authenticate(bool $cookie = false, array $data = []) : bool {
        return false;
    }
}

