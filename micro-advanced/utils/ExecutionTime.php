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

namespace advanced\utils;

use advanced\Bootstrap;

/**
 * ExecutionTime class
 */
class ExecutionTime {

    /**
     * @var float
     */
    private $startTime;

    /**
     * @var float
     */
    private $endTime;

    /**
     * Start counting process time.
     *
     * @return void
     */
    public function start() : void {
        $this->startTime = microtime(true);
    }

    /**
     * Stop counting process time.
     *
     * @return void
     */
    public function end() : void {
        $this->endTime = microtime(true);
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return Bootstrap::getMainLanguage()->get("execution_time", null, $this->endTime - $this->startTime);
    }
}
