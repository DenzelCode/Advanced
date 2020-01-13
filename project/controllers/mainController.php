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

    public function index(string $method = "*") : string {
        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.index"));
    
        return parent::index($method) . TemplateProvider::get("main/index");
    }

    public function error404(string $method = "*") : string {
        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.404"));

        return TemplateProvider::get("main/error404");
    }
}
