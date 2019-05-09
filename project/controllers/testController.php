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

/**
* testController class
*/
class testController extends Controller {

    public function index(string $method = "*") : string {
        return "Method type: {$method}";
    }
}
