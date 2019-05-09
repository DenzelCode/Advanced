<?php 
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace project\controllers;

use advanced\Bootstrap;
use advanced\controllers\Controller;
use advanced\body\template\TemplateProvider;
use advanced\project\Project;

/**
* mainController class
*/
class mainController extends Controller {

    public function index(string $method = "*") : string {
        return TemplateProvider::get('main/index');
    }

    public function testing(string $method = "*") : string {
        return TemplateProvider::get('main/testing');
    }

    public function home(string $method = "get|post") : string {
        return TemplateProvider::get('main/home');
    }

    public function error404(string $method = "get|post") : string {
        return "Error 404";
    }

    public function profile(string $method = "get|post", string $username) {
        return $_GET['data'] . ": " . $username;
    }
}


