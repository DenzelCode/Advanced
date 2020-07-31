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

namespace advanced\http\router;

use advanced\controllers\Controller;
use advanced\file\UploadFile;
use advanced\http\Post;
use advanced\http\Get;

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
    private $controller = "main";

    /**
     * @var string
     */
    private $method = "index";

    /**
     * @var string
     */
    private $requestMethod = "get";

    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @var string
     */
    private $uri;

    /**
     * @var Request
     */
    private static $instance;

    /**
     * Initialize a Request.
     * 
     * @param string|null $uri
     */
    public function __construct(?string $uri = null) {
        self::$instance = $this;

        $uri = urldecode($uri);

        $this->uri = $uri;

        $route = explode("/", substr($this->uri, 0, ($str = strrpos($this->uri, "?")) ? $str : strlen($this->uri)));

        array_shift($route);

        $this->controller = (empty($route[0]) ? "main" : strtolower($route[0]));

        $this->method = (empty($route[1]) ? "index" : $route[1]);

        if (count($route) && $route[0] == "index" || count($route) && $route[0] == "index.php") $this->controller = "main";

        $controller = (file_exists($this->getControllerFile(ADVANCED)) ? $this->getControllerFile(ADVANCED) : $this->getControllerFile(PROJECT));

        if (!file_exists($controller)) {
            $this->controller = "main";

            $this->method = !empty($route[0]) ? $route[0] : "index";

            $i = 1;
        } else $i = 2;

        for ($i; $i < count($route); $i++) $this->arguments[$i] = $route[$i];
    }

    /**
     * Get an instance of a Request.
     * 
     * @return Request
     */
    public static function getInstance() : Request {
        if (!self::$instance) self::$instance = new Request();

        return self::$instance;
    }

    /**
     * Get current controller.
     *
     * @return string
     */
    public function getController() : string {
        return $this->controller;
    }

    /**
     * Change current controller.
     * 
     * @param string $controller
     * @return void
     */
    public function setController(string $controller) {
        return $this->controller = $controller;
    }

    /**
     * Get controller namespace.
     *
     * @param string $preffix
     * @return string
     */
    public function getControllerNamespace(string $preffix) : string {
        return "{$preffix}\\controllers\\" . $this->controller . "Controller";
    }

    /**
     * Get controller object.
     *
     * @param string $preffix
     * @return Controller
     */
    public function getControllerObject(string $preffix) : Controller {
        $obj = $this->getControllerNamespace($preffix);
        
        return new $obj();
    }

    /**
     * Get controller file.
     *
     * @param string $preffix
     * @return string
     */
    public function getControllerFile(string $preffix) : string {
        return $preffix . "controllers" . DIRECTORY_SEPARATOR . $this->controller . "Controller.php";
    }

    /**
     * Get current controller method.
     *
     * @return string|null
     */
    public function getMethod() : ?string {
        return $this->method;
    }

    /**
     * Set current controller method.
     * 
     * @param string $data
     * @return void
     */
    public function setMethod(string $data = null) {
        return $this->method = $data;
    }

    /**
     * Get secure type. http:// or https://
     * 
     * @return string
     */
    public function getSecure() : string {
        return (string) (!empty($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on") ? "https://" : "http://");
    }

    /**
     * Get app host.
     *
     * @return string
     */
    public function getHost() : string {
        return (string) !empty($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "";
    }

    /**
     * Get controller method arguments.
     *
     * @return array
     */
    public function getArguments() : array {
        return $this->arguments;
    }

    /**
     * Set controller method arguments.
     *
     * @param array $data
     * @return void
     */
    public function setArguments(array $data) {
        return $this->arguments = $data;
    }

    /**
     * Get request method.
     *
     * @return string|null
     */
    public function getRequestMethod() : ?string {
        return $this->requestMethod;
    }

    /**
     * Set request method.
     *
     * @param string $method
     * @return void
     */
    public function setRequestMethod(string $method = null) : void {
        $this->requestMethod = $method;
    }

    /**
     * Get full app URL. example: https://example.com
     *
     * @return string
     */
    public function getFullURL() : string {
        return $this->getSecure() . $this->getHost();
    }

    /**
     * Get current request URI. example: /index
     *
     * @return string
     */
    public function getURI() : string {
        return $this->uri;
    }

    /**
     * Get uploaded file.
     *
     * @param string $file Type the name of the file uploader.
     * @return UploadFile|null
     */
    public function file(string $file) : ?UploadFile {
        if (empty($_FILES) || !file_exists($_FILES[$file]["tmp_name"]) || !is_uploaded_file($_FILES[$file]["tmp_name"])) return null;

        return new UploadFile($_FILES[$file]);
    }

    /**
     * Get uploaded files.
     *
     * @param string $file Type the name of the file uploader.
     * @return UploadFile[]
     */
    public function files(string $file) : array {
        $arranged = [];

        for ($i = 0; $i < count($_FILES["name"]); $i++) {
            $arranged[$i] = [];

            foreach (array_keys($_FILES) as $key) $arranged[$i][$key] = $_FILES[$key][$i];
        }

        $files = [];

        foreach ($arranged as $f) $files[] = new UploadFile($f);

        return $files;
    }

    /**
     * Get app user IP.
     *
     * @return string
     */
    public function getIp() : string {
        $userIP = "127.0.0.1";

        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) $userIP = $_SERVER["HTTP_CF_CONNECTING_IP"]; else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) $userIP = $_SERVER["HTTP_X_FORWARDED_FOR"]; else if (isset($_SERVER["HTTP_CLIENT_IP"])) $userIP = $_SERVER["HTTP_CLIENT_IP"]; else if (isset($_SERVER["REMOTE_ADDR"])) $userIP = $_SERVER["REMOTE_ADDR"];

        if ($userIP != "::1" && filter_var($userIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) $userIP = hexdec(substr($userIP, 0, 2)). "." . hexdec(substr($userIP, 2, 2)). "." . hexdec(substr($userIP, 5, 2)). "." . hexdec(substr($userIP, 7, 2));

        if ($userIP == "::1" || $userIP == "0.1.0.0" || empty($userIP)) $userIP = "127.0.0.1";

        return $userIP;
    }

    /**
     * Get POST parameter/s.
     *
     * @param string|string[] $parameter name/names of the POST parameters that you want to get.
     * @param mixed $default Default value on the POST parameter.
     * @param boolean $common True = $_POST, False = php://input.
     * @return mixed
     */
    public function post($parameter, $default = null, bool $common = true) {
        return (is_array($parameter) ? Post::get($parameter, $common) : Post::get([ $parameter => $default ], $common)[$parameter]);
    }

    /**
     * Get GET parameter/s.
     *
     * @param string|string[] $parameter name/names of the GET parameters that you want to get.
     * @param mixed $default Default value on the GET parameter.
     * @return mixed
     */
    public function get($parameter, $default = null) {
        return (is_array($parameter) ? Get::get($parameter) : Get::get([ $parameter => $default ])[$parameter]);
    }
}