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

    // URL: /login returning the login template
    public function login(string $method = "get|post") : string {
        // If it's not authenticated, send into /logged
        if (Auth::isAuthenticated()) Response::redirect("/logged");

        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.login"));

        return TemplateProvider::get("main/login");
    }

    // URL: /register returning the register template
    public function register(string $method = "get|post") : string {
        // If it's not authenticated, send into /logged
        if (Auth::isAuthenticated()) Response::redirect("/logged");

        // Set parameter title on the template we can access to it in the template as {@title} or {#= $title #}
        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.register"));

        return TemplateProvider::get("main/register");
    }

    // URL: /login returning the login template
    public function logged(string $method = "*") : string {
        // If it's not authenticated, send into /login
        if (!Auth::isAuthenticated()) Response::redirect("/login");

        // Set parameter title on the template we can access to it in the template as {@title} or {#= $title #}
        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.logged", null, Auth::getUser()->getName()));

        return TemplateProvider::get("main/logged");
    }

    // URL: /logout
    public function logout(string $method = "*") : void {
        // If it's not authenticated, send into /login
        if (!Auth::isAuthenticated()) Response::redirect("/login");

        // Destroy the session
        Auth::destroy();

        Response::redirect("/login");
    }

    // URL: parameters_examples
    public function parameters_examples(string $method = "get") : string {
        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.parameters_examples"));

        return TemplateProvider::get("main/parameters_examples");
    }

    // URL: /new_template returning the newtemplate template
    public function new_template(string $method = "get|post") : string {
        // If the template doesn't exists the framework creates the file automatically
        return TemplateProvider::get("main/new_template");
    }

    /**
     * Some examples of URL methods with the framework.
     */
    // URL: /testing Method: GET
    public function testing(string $method = "get") : string {
        return "Testing method: {$method}";
    }
    
    // URL: /testingPost Method: POST
    public function testingPost(string $method = "post") : string {
        return "Testing method: {$method}";
    }

    // URL: /testingGetPost Method: GET or POST
    public function testingGetPost(string $method = "get|post") : string {
        return "Testing method: {$method}";
    }

    // URL: /testingGeneral Method: General
    public function testingGeneral(string $method = "*") : string {
        return "Testing method: {$method}";
    }

    // URL: /testingAny Method: General
    public function testingAny(string $method = "any") : string {
        return "Testing method: {$method}";
    }

    // URL: /testingAll Method: General
    public function testingAll(string $method = "all") : string {
        return "Testing method: {$method}";
    }

    // Url: /error404, this is going to be run by default when an URL doesn't exists or if you access /error404 url.
    public function error404(string $method = "*") : string {
        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.404"));

        return TemplateProvider::get("main/error404");
    }
}
