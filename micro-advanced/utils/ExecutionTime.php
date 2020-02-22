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

namespace advanced\utils;

use advanced\Bootstrap;

/**
 * ExecutionTime class
 */
class ExecutionTime {

    private $startTime;
    private $endTime;

    public function start() {
        $this->startTime = microtime(true);
    }

    public function end() {
        $this->endTime = microtime(true);
    }

    public function __toString() : string {
        return Bootstrap::getMainLanguage()->get("execution_time", null, $this->endTime - $this->startTime);
    }
}
