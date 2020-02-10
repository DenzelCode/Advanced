<?php 
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace project;

use advanced\body\template\TemplateProvider;
use advanced\project\Project as BaseProject;

class Project extends BaseProject {

    public function init() : void {
        TemplateProvider::setParameters(self::getConfig()->get('web'));

        self::initRouter();
    }
}
