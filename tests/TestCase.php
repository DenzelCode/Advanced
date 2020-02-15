<?php

namespace tests;

use PHPUnit\Framework\TestCase as FrameworkTestCase;
use environment;

abstract class TestCase extends FrameworkTestCase {

    public function __construct() {
        parent::__construct();

        environment::init(__DIR__);
    }
}

