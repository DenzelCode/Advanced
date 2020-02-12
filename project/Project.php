<?php 
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace project;

use advanced\project\Project as BaseProject;

class Project extends BaseProject {

    public function init() : void {
        self::initRouter();
    }
}
