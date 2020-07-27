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

namespace advanced\controllers;

use advanced\Bootstrap;
use advanced\template\TemplateProvider;
use advanced\http\Response;
use advanced\http\router\Request;

/**
* Controller abstract class
*/
abstract class Controller {

    /**
     * @var Response
     */
    protected $response = null;

    /**
     * @var Request
     */
    protected $request = null;

    public function __construct() {
        $this->response = Bootstrap::getResponse();

        $this->request = Request::getInstance();
    }

    /**
     * Create URL /index or / and let ALL methods request.
     *
     * @param string $method
     * @return string
     */
    public function index(string $method = Request::ALL) : string {
        TemplateProvider::setParameters([
            "title" => Bootstrap::getMainLanguage()->get("general.description"),
            "name" => Bootstrap::getConfig()->get('web.name'),
            "cdn" => Bootstrap::getConfig()->get('web.cdn')
        ]);

        return TemplateProvider::getRootTemplate('main/index');
    }

    abstract public function error404(string $method = Request::ALL) : string;

    /**
     * Set response code.
     *
     * @param integer $code
     * @return void
     */
    protected function setResponseCode(int $code) : void {
        $this->response->setCode($code);
    }

    /**
     * Get response Object.
     *
     * @return Response
     */
    protected function getResponse() : Response {
        return $this->response;
    }

    /**
     * Get response Object.
     *
     * @return Request
     */
    protected function getRequest() : Request {
        return $this->request;
    }
}
