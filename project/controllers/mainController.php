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