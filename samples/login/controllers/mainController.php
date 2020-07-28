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
 * @copyright Copyright (c) 2019 - 2020 Advanced microFramework
 * @author Advanced microFramework Team (Denzel Code, Soull Darknezz)
 * @link https://github.com/DenzelCode/Advanced
 * 
 */

namespace project\controllers;

use advanced\template\TemplateProvider;
use advanced\Bootstrap;
use advanced\controllers\Controller;
use advanced\http\Response;
use advanced\user\auth\Auth;
use advanced\http\router\Request;

/**
* mainController class
*/
class mainController extends Controller {

    /**
     * URL: /index and main page
     *
     * @param string $method
     * @return string
     */
    public function index(string $method = Request::ALL) : string {
        // Set parameter title on the template we can access to it in the template as {@title} or {#= $title #}
        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.index"));
    
        // Show the main template of the framework and the template of the index that we created
        return parent::index($method) . TemplateProvider::get("main/index");
    }

    /**
     * URL: /login returning the login template
     *
     * @param string $method
     * @return string
     */
    public function login(string $method = "get|post") : string {
        // If it's not authenticated, send into /logged
        if (Auth::isAuthenticated()) Response::redirect("/logged");

        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.login"));

        return TemplateProvider::get("main/login");
    }

    /**
     * URL: /register returning the register template
     *
     * @param string $method
     * @return string
     */
    public function register(string $method = "get|post") : string {
        // If it's not authenticated, send into /logged
        if (Auth::isAuthenticated()) Response::redirect("/logged");

        // Set parameter title on the template we can access to it in the template as {@title} or {#= $title #}
        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.register"));

        return TemplateProvider::get("main/register");
    }

    /**
     * URL: /logged returning the login logged
     *
     * @param string $method
     * @return string
     */
    public function logged(string $method = Request::ALL) : string {
        // If it's not authenticated, send into /login
        if (!Auth::isAuthenticated()) Response::redirect("/login");

        // Set parameter title on the template we can access to it in the template as {@title} or {#= $title #}
        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.logged", null, Auth::getUser()->getName()));

        return TemplateProvider::get("main/logged");
    }

    /**
     * URL: /logout
     *
     * @param string $method
     * @return void
     */
    public function logout(string $method = Request::ALL) : void {
        // If it's not authenticated, send into /login
        if (!Auth::isAuthenticated()) Response::redirect("/login");

        // Destroy the session
        Auth::destroy();

        Response::redirect("/login");
    }

    /**
     * URL: /parameters_examples
     *
     * @param string $method
     * @return string
     */
    public function parameters_examples(string $method = Request::GET) : string {
        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.parameters_examples"));

        return TemplateProvider::get("main/parameters_examples");
    }

    /**
     * URL: /new_template returning the newtemplate template
     *
     * @param string $method
     * @return string
     */
    public function new_template(string $method = Request::ALL) : string {
        // If the template doesn't exists the framework creates the file automatically
        return TemplateProvider::get("main/new_template");
    }

    // Some examples of URL methods with the framework.

    /**
     * URL: /testing Method: GET
     *
     * @param string $method
     * @return string
     */
    public function testing(string $method = Request::GET) : string {
        return "Testing method: {$method}";
    }
    
    /**
     * URL: /testingPost Method: POST
     *
     * @param string $method
     * @return string
     */
    public function testingPost(string $method = Request::POST) : string {
        return "Testing method: {$method}";
    }

    /**
     * URL: /testingGetPost Method: GET or POST
     *
     * @param string $method
     * @return string
     */
    public function testingGetPost(string $method = "get|post") : string {
        return "Testing method: {$method}";
    }

    /**
     * URL: /testingGeneral Method: General
     *
     * @param string $method
     * @return string
     */
    public function testingGeneral(string $method = Request::GENERAL) : string {
        return "Testing method: {$method}";
    }

    /**
     * URL: /testingAny Method: General
     *
     * @param string $method
     * @return string
     */
    public function testingAny(string $method = Request::ANY) : string {
        return "Testing method: {$method}";
    }

    /**
     * URL: /testingAll Method: General
     *
     * @param string $method
     * @return string
     */
    public function testingAll(string $method = Request::ALL) : string {
        return "Testing method: {$method}";
    }

    /**
     * Url: /error404, this is going to be run by default when an URL doesn't exists or if you access /error404 url.
     *
     * @param string $method
     * @return string
     */
    public function error404(string $method = Request::ALL) : string {
        TemplateProvider::setParameter("title", Bootstrap::getLanguage()->get("title.404"));

        return TemplateProvider::get("main/error404");
    }
}
