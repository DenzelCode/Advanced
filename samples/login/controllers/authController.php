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

namespace project\controllers;

use advanced\Bootstrap;
use advanced\controllers\Controller;
use advanced\exceptions\UserException;
use advanced\http\Post;
use advanced\http\Response;
use advanced\http\router\Request;
use advanced\account\Auth;

/**
* authController class
*/
class authController extends Controller {

    // URL: /auth/login Method: POST
    public function login(string $method = "post") : string {
        // Login
        $response = [];

        $pop = Post::get([
            "username" => null,
            "password" => null,
            "remember" => null
        ]);

        $user = Bootstrap::getUsers()->getUser((string) $pop["username"], $pop);

        // Get user by mail
        if (!$user) $user = Bootstrap::getUsers()->getUserByMail((string) $pop["username"], $pop);

        $response["type"] = "error";

        // Check if there is an account already logged in in the browser
        if (Auth::isAuthenticated()) {
            $response["message"] = Bootstrap::getLanguage()->get("form.login.logged");
        } else if (empty($pop["username"]) || empty($pop["password"])) {
            $response["message"] = Bootstrap::getLanguage()->get("form.general.empty");
        } else if ($user == null) {
            // Check if the user does not exists
            $response["message"] = Bootstrap::getLanguage()->get("form.login.not_exists");
        } else {
            $pop["username"] = $user->getName();

            // authenticate account
            $auth = $user->authenticate((bool) $pop["remember"], $pop);

            if ($auth) {
                // Update user
                $ip = Request::getIp();

                $user->set([
                    "ip_last" => $ip,
                    "last_used" => time()
                ]);

                $response["message"] = Bootstrap::getLanguage()->get("form.login.success");

                $response["type"] = "success";
            } else $response["message"] = Bootstrap::getLanguage()->get("form.login.invalid_password");
        }

        return Response::setJSON(true)->write($response);
    }

    // URL: /auth/register Method: POST
    public function register(string $method = "post") : string {
        // Register
        $response = [];

        $pop = Post::get([
            "username" => null,
            "password" => null,
            "mail" => null,
            "gender" => null
        ]);

        // Get user
        $user = Bootstrap::getUsers()->getUser((string) $pop["username"]);

        $mailUser = Bootstrap::getUsers()->getUserByMail((string) $pop["mail"]);

        $accountsLimit = Bootstrap::getConfig()->get("sign_up.accounts_limit");

        $ip = Request::getIp();

        if ($accountsLimit) $usersByIp = Bootstrap::getUsers()->getUsersByIp($ip);

        if (empty($usersByIp)) $usersByIp = [];

        $response["type"] = "error";

        // Check if there is an account already logged in in the browser
        if (Auth::isAuthenticated()) {
            $response["message"] = Bootstrap::getLanguage()->get("form.register.logged");
        } else if (empty($pop["username"]) || empty($pop["password"]) || empty($pop["mail"]) || !$pop["gender"]) {
            $response["message"] = Bootstrap::getLanguage()->get("form.general.empty");
        } else if ($user !== null) {
            $response["message"] = Bootstrap::getLanguage()->get("form.register.exists");

            $response["type"] = "username";
        } else if ($mailUser !== null) {
            // Check if an user with the mail provided already exists
            $response["message"] = Bootstrap::getLanguage()->get("form.register.mail_exists");

            $response["type"] = "mail";
        } else if (strlen($pop["password"]) < 6 || strlen($pop["password"]) > 60) {
            $response["message"] = Bootstrap::getLanguage()->get("form.register.password_chars");

            $response["type"] = "password";
        } else {
            if ($pop["gender"] != "M" && $pop["gender"] != "F") $pop["gender"] = "M";

            try {
                $pop["cookie"] = false;

                $data = [
                    "username" => $pop["username"],
                    "password" => Auth::hash($pop["password"]),
                    "mail" => $pop["mail"],
                    "account_created" => time(),
                    "ip_reg" => $ip,
                    "ip_last" => $ip,
                    "gender" => $pop["gender"],
                    "last_used" => time()
                ];

                $data = array_merge($data, Bootstrap::getConfig()->get("sign_up")["user"]);

                // Create user with data $data (keys are the columns from the database)

                $user = Bootstrap::getUsers()->createUser($data, $pop);

                // Authenticate the account and log in

                $auth = $user->authenticate();

                if ($auth) {
                    $response["type"] = "success";

                    $response["message"] = Bootstrap::getLanguage()->get("form.register.success");
                } else $response["message"] = "Auth error";
            } catch (UserException $e) {
                $response["message"] = $e->getMessage();
            }
        }

        return Response::setJSON(true)->write($response);
    }
}
