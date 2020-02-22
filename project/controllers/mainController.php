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

use advanced\body\template\TemplateProvider;
use advanced\controllers\Controller;
use advanced\http\router\Request;

/**
* mainController class
*/
class mainController extends Controller {

    public function index(string $method = Request::ALL) : string {
        return parent::index($method);
    }

    public function testing(string $method = Request::GET, string $argument = "default value") : string {
        return "Testing URL: {$argument}";
    }

    public function error404(string $method = Request::ALL) : string {
        return "Error 404";
    }
}