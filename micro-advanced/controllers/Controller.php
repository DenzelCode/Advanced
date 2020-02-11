<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace advanced\controllers;

use advanced\Bootstrap;
use advanced\body\template\TemplateProvider;

/**
* Controller abstract class
*/
abstract class Controller {

    public function index() : string {
        TemplateProvider::setParameters([
            "title" => Bootstrap::getMainLanguage()->get("general.description"),
            "name" => Bootstrap::getConfig()->get('web.name'),
            "cdn" => str_replace("{@url}", Bootstrap::getConfig()->get('web.url'), Bootstrap::getConfig()->get('web.cdn'))
        ]);

        return TemplateProvider::getRootTemplate('main/index');
    }
}
