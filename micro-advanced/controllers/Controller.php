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
        TemplateProvider::setPath('advanced');

        return TemplateProvider::get('main/index');
    }
}
