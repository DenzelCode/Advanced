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
        return parent::index($method);
    }

    public function error404(string $method = "*") : string {
        return "Error 404";
    }
}
