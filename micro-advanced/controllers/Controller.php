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

namespace advanced\controllers;

use advanced\Bootstrap;
use advanced\body\template\TemplateProvider;
use advanced\http\router\Request;

/**
* Controller abstract class
*/
abstract class Controller {

    public function index(string $method = Request::ALL) : string {
        TemplateProvider::setParameters([
            "title" => Bootstrap::getMainLanguage()->get("general.description"),
            "name" => Bootstrap::getConfig()->get('web.name'),
            "cdn" => str_replace("{@url}", Request::getFullURL(), Bootstrap::getConfig()->get('web.cdn'))
        ]);

        return TemplateProvider::getRootTemplate('main/index');
    }
}
