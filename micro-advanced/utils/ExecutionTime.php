<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
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
        return Bootstrap::getLanguageProvider(false)->getText('execution_time', null, $this->endTime - $this->startTime);
    }
}
