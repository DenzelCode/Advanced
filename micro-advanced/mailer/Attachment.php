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

namespace advanced\mailer;

class Attachment {

    private $path;
    private $name;

    public function __construct(string $path, string $name) {
        $this->path = $path;
        $this->name = $name;
    }

    public function getPath() : string {
        return $this->path;
    }

    public function getName() : string {
        return $this->name;
    }
}