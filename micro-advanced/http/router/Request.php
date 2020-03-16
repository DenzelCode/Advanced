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

namespace advanced\http\router;

use advanced\controllers\Controller;

class Request{

    public const GET = "GET";
    public const POST = "POST";
    public const DELETE = "DELETE";
    public const PUT = "PUT";
    public const CONNECT = "CONNECT";
    public const TRACE = "TRACE";
    public const HEAD = "HEAD";
    public const GENERAL = "*";
    public const ALL = "ALL";
    public const ANY = "ANY";

    /**
     * @var string
     */
    private static $controller = "main";

    /**
     * @var string
     */
    private static $method = "index";

    /**
     * @var string
     */
    private static $requestMethod = "get";

    /**
     * @var array
     */
    private static $arguments = [];

    /**
     * @var Request
     */
    private static $instance;

    /**
     * @param string $url
     */
    public function __construct(string $url = null) {
        self::$instance = $this;

        $url = urldecode($url);

        $route = explode("/", substr($url, 0, ($str = strrpos($url, "?")) ? $str : strlen($url)));

        array_shift($route);

        self::$controller = (empty($route[0]) ? "main" : strtolower($route[0]));

        self::$method = (empty($route[1]) ? "index" : $route[1]);

        if (count($route) && $route[0] == "index" || count($route) && $route[0] == "index.php") self::$controller = "main";

        if (!file_exists(($controller = self::getFile(ADVANCED)))) $controller = self::getFile(PROJECT);

        $i = 2;

        if (!file_exists($controller)) {
            self::$controller = "main";

            self::$method = !empty($route[0]) ? $route[0] : "index";

            $i = 1;
        }

        for ($i; $i < count($route); $i++) self::$arguments[$i] = $route[$i];
    }

    /**
     * @return Request
     */
    public static function getInstance() : Request {
        return self::$instance;
    }

    /**
    * @return string
    */
    public static function getController() : string {
        return self::$controller;
    }

    /**
     * @param string $data
     * @return void
     */
    public static function setController(string $data) {
        return self::$controller = $data;
    }

    /**
    * @return string
    */
    public static function getObjectName(string $preffix) : string {
        return "{$preffix}\\controllers\\" . self::getController() . "Controller";
    }

    /**
     * Get controller object.
     *
     * @param string $preffix
     * @return Controller
     */
    public static function getObject(string $preffix) : Controller {
        $obj = self::getObjectName($preffix);
        
        return new $obj();
    }

    /**
     * Get file.
     *
     * @param string $preffix
     * @return string
     */
    public static function getFile(string $preffix) : string {
        return $preffix . "controllers" . DIRECTORY_SEPARATOR . self::$controller . "Controller.php";
    }

    /**
    * @return string|null
    */
    public static function getMethod() : ?string {
        return self::$method;
    }

    /**
     * @param string $data
     * @return void
     */
    public static function setMethod(string $data = null) {
        return self::$method = $data;
    }

    /**
     * @return string
     */
    public static function getSecure() : string {
        return (string) (!empty($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on") ? "https://" : "http://");
    }

    /**
     * @return string
     */
    public static function getURL() : string {
        return (string) $_SERVER["HTTP_HOST"];
    }

    /**
     * @return array
     */
    public static function getArguments() : array {
        return self::$arguments;
    }

    /**
     * @param array $data
     * @return void
     */
    public static function setArguments(array $data) {
        return self::$arguments = $data;
    }

    /**
    * @return string|null
    */
    public static function getRequestMethod() : ?string {
        return self::$requestMethod;
    }

    /**
     * @param string $method
     * @return void
     */
    public static function setRequestMethod(string $method = null) {
        return self::$requestMethod = $method;
    }

    /**
    * @return string
    */
    public static function getFullURL() : string {
        return self::getSecure() . self::getURL();
    }

    /**
     * Get app user IP.
     *
     * @return string
     */
    public static function getIp() : string {
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) $userIP = $_SERVER["HTTP_CF_CONNECTING_IP"]; else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) $userIP = $_SERVER["HTTP_X_FORWARDED_FOR"]; else if (isset($_SERVER["HTTP_CLIENT_IP"])) $userIP = $_SERVER["HTTP_CLIENT_IP"]; else $userIP = $_SERVER["REMOTE_ADDR"];
        } else if (getenv("HTTP_CF_CONNECTING_IP")) $userIP = getenv("HTTP_CF_CONNECTING_IP"); else if (getenv("HTTP_X_FORWARDED_FOR")) $userIP = getenv("HTTP_X_FORWARDED_FOR"); else if (getenv("HTTP_CLIENT_IP")) $userIP = getenv("HTTP_CLIENT_IP"); else $userIP = getenv("REMOTE_ADDR");

        if (filter_var($userIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) $userIP = hexdec(substr($userIP, 0, 2)). "." . hexdec(substr($userIP, 2, 2)). "." . hexdec(substr($userIP, 5, 2)). "." . hexdec(substr($userIP, 7, 2));

        if ($userIP == "::1" || $userIP = "0.1.0.0" || empty($userIP)) $userIP = "127.0.0.1";

        return $userIP;
    }

}