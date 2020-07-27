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

namespace project\controllers;

use advanced\Bootstrap;
use advanced\controllers\Controller;
use advanced\exceptions\UserException;
use advanced\http\Post;
use advanced\http\router\Request;
use advanced\user\auth\Auth;

/**
* authController class
*/
class authController extends Controller {

    /**
     * Initialize controller.
     */
    public function index(string $method = Request::ALL) : string {
        $this->setResponseCode(403);

        return "Error 403";
    }

    /**
     * Create URL /auth/login and let POST method requests.
     *
     * @param string $method
     * @return string
     */
    public function login(string $method = Request::POST) : string {
        // Login
        $response = [];

        $response["type"] = "error";

        $pop = $this->request->post([
            "username" => null,
            "password" => null,
            "remember" => null
        ]);

        $user = Bootstrap::getUserFactory()->getUser((string) $pop["username"]);

        // Get user by mail
        if (!$user) $user = Bootstrap::getUserFactory()->getUserByMail((string) $pop["username"]);

        $language = Bootstrap::getLanguage();

        // Check if there is an account already logged in in the browser
        if (Auth::isAuthenticated()) {
            $response["message"] = $language->get("form.login.logged");
        } else if (empty($pop["username"]) || empty($pop["password"])) {
            $response["message"] = $language->get("form.general.empty");
        } else if ($user == null) {
            // Check if the user does not exists
            $response["message"] = $language->get("form.login.not_exists");
        } else {
            $pop["username"] = $user->getName();

            // authenticate account
            $auth = $user->authenticate($pop["password"], (bool) $pop["remember"]);

            if ($auth) {
                // Update user
                $user->setByArray([
                    "ip_last" => $this->request->getIp(),
                    "last_used" => time()
                ]);

                $response["message"] = $language->get("form.login.success");

                $response["type"] = "success";
            } else $response["message"] = $language->get("form.login.invalid_password");
        }

        return $this->response->json($response);
    }

    /**
     * Create URL /auth/register and let POST method requests.
     *
     * @param string $method
     * @return string
     */
    public function register(string $method = Request::ALL) : string {
        // Register
        $response = [];

        $response["type"] = "error";

        $pop = $this->request->post([
            "username" => null,
            "password" => null,
            "mail" => null,
            "gender" => null
        ]);

        // Get user
        $user = Bootstrap::getUserFactory()->getUser((string) $pop["username"]);

        $mailUser = Bootstrap::getUserFactory()->getUserByMail((string) $pop["mail"]);

        $accountsLimit = Bootstrap::getConfig()->get("sign_up.accounts_limit");

        $ip = $this->request->getIp();

        if ($accountsLimit) $usersByIp = Bootstrap::getUserFactory()->getUsersByIp($ip);

        if (empty($usersByIp)) $usersByIp = [];

        $language = Bootstrap::getLanguage();

        // Check if there is an account already logged in in the browser
        if (Auth::isAuthenticated()) {
            $response["message"] = $language->get("form.register.logged");
        } else if (empty($pop["username"]) || empty($pop["password"]) || empty($pop["mail"]) || !$pop["gender"]) {
            $response["message"] = $language->get("form.general.empty");
        } else if ($user !== null) {
            $response["message"] = $language->get("form.register.exists");

            $response["type"] = "username";
        } else if ($mailUser !== null) {
            // Check if an user with the mail provided already exists
            $response["message"] = $language->get("form.register.mail_exists");

            $response["type"] = "mail";
        } else if (strlen($pop["password"]) < 6 || strlen($pop["password"]) > 60) {
            $response["message"] = $language->get("form.register.password_chars");

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

                $data = array_merge($data, Bootstrap::getConfig()->get("sign_up.user", []));

                // Create user with data $data (keys are the columns from the database)

                $user = Bootstrap::getUserFactory()->createUser($data, $pop["password"]);

                // Authenticate the account and log in

                $auth = $user->authenticate();

                if ($auth) {
                    $response["type"] = "success";

                    $response["message"] = $language->get("form.register.success");
                } else $response["message"] = "Auth error";
            } catch (UserException $e) {
                $response["message"] = $e->getMessage();
            }
        }

        return $this->response->json($response);
    }

    /**
     * Error 404
     *
     * @param string $method
     * @return string
     */
    public function error404(string $method = Request::ALL) : string {
        return "Error 404";
    }
}
