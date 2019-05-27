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
* serverController class
*/
class serverController extends Controller {

    public function view(string $method = 'get', ...$view) : ?string {
        if (empty($view)) return null;

        $view = implode('/', $view);

        return TemplateProvider::get($view, true, false);
    }
}


