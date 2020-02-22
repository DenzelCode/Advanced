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

namespace tests\unit\account;

use advanced\account\Auth;
use advanced\account\User;
use advanced\Bootstrap;
use advanced\data\Database;
use advanced\http\router\Request;
use advanced\project\Project;
use Exception;
use tests\TestCase;

class UserTest extends TestCase {

    public function __construct() {
        parent::__construct();

        Project::setDatabase(new Database("127.0.0.1", 3306, "root", "", "testing"));
    }

    public function testCreateAccount() : void {
        $user = Bootstrap::getUsers()->createUser([
            "username" => "dsdsdsds",
            "password" => Auth::hash("testing"),
            "mail" => "testing@example.com",
            "account_created" => time(),
            "ip_reg" => Request::getIp(),
            "ip_last" => Request::getIp(),
            "gender" => "M",
            "last_used" => time()
        ]);

        $this->assertTrue(true);
    }
}