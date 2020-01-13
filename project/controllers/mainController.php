<?php 
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace project\controllers;

use advanced\body\template\TemplateProvider;
use advanced\Bootstrap;
use advanced\controllers\Controller;
use advanced\http\Response;
use advanced\session\Auth;

/**
* mainController class
*/
class mainController extends Controller {

    // URL: /index and main page
    public function index(string $method = "*") : string {
        // Set parameter title on the template we can access to it in the template as {@title} or {#= $title #}
        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.index"));
    
        // Show the main template of the framework and the template of the index that we created
        return parent::index($method) . TemplateProvider::get("main/index");
    }

    public function error404(string $method = "*") : string {
        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.404"));

        return TemplateProvider::get("main/error404");
    }
}
