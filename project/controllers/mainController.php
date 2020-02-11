<?php 
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace project\controllers;

use advanced\controllers\Controller;

/**
* mainController class
*/
class mainController extends Controller {

    public function index(string $method = "*") : string {
        return parent::index($method);
    }

    public function testing(string $method = "get", string $argument = "default value") : string {
        return "Testing URL: {$argument}";
    }

    public function error404(string $method = "*") : string {
        return "Error 404";
    }
}