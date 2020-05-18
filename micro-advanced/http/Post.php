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

namespace advanced\http;

class Post{

    /**
     * @var array
     */
    private static $params = [];

    /**
     * Get parameters from the $_POST var or php://input.
     *
     * @param array $pop
     * @param boolean True = $_POST, False = php://input.
     * @return mixed
     */
    public static function get(array $pop, bool $common = true) {
        self::populate($common);

        foreach(self::$params as $key => $value) {
            $pop[$key] = $value;
        }

        return $pop;
    }

    /**
     * Get parameters.
     *
     * @return array
     */
    public static function getParameters() : array {
        return self::$params;
    }

    /**
     * Populate data.
     *
     * @param boolean $common
     * @return void
     */
    private static function populate(bool $common = true) {
        $body = file_get_contents('php://input');

        $body = json_decode($body, true);

        if ($common) $body = $_POST;

        foreach ($body as $key => $value) {
            self::$params[$key] = $value;
        }
    }
}
