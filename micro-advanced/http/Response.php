<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace advanced\http;

class Response{
    
    private static $json = false;
        
    public function write($data) {
        if (self::isJSON()) {
            self::setHeader('text/json');
            
            return json_encode($data);
        }

        return $data;
    }

    public static function setCode(int $code) : Response {
        http_response_code($code);

        return new Response();
    }

    public static function setHeader($header) {
        header("Content-Type: {$header}");
    }

    public static function setJSON(bool $value = true) : Response {
        self::$json = $value;

        return new Response();
    }

    public static function isJSON() : bool {
        return self::$json;
    }

    public static function redirect(string $url) : void {
        header("Location: {$url}");

        exit;
    }
}