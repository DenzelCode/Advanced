<?php

namespace advanced\http;

/**
* Post class
*/	
class Post{

    private static $params = [];

    public static function get(array $pop, bool $common = true) {
        self::populate($common);

        foreach(self::$params as $key => $value) {
            $pop[$key] = $value;
        }

        return $pop;
    }

    public static function getParameters() : array {
        return self::$params;
    }

    private static function populate(bool $common = true) {
        $body = file_get_contents('php://input');

        $body = json_decode($body, true);

        if ($common) $body = $_POST;

        foreach ($body as $key => $value) {
            self::$params[$key] = $value;
        }
    }
}
