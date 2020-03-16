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

use advanced\exceptions\RouterException;
use advanced\http\Response;
use ReflectionClass;
use ReflectionMethod;

class Router{

    /**
     * @var array
     */
    private static $http_methods = ["GET", "POST", "DELETE", "PUT", "CONNECT","TRACE", "HEAD", "*", "general", "any", "all"];

    /**
     * Run Router from request.
     *
     * @param Request $request
     * @param string $preffix
     * @return void
     * @throws RouterException
     */
    public static function run(Request $request, string $preffix = "advanced") : void {
        if ($preffix == "advanced" && !file_exists($request->getFile("advanced"))){
            self::run($request, "project");

            return;
        }

        $set404 = function (Request $request) {
            Response::setCode(404);
            
            $request->setController("main");
            $request->setMethod("error404");
        };

        $object_name = $request->getObjectName($preffix);

        if (!method_exists($object_name, $request->getMethod())) $set404($request);

        if (in_array($request->getMethod(), self::getPrivateMethods($object_name))) $set404($request);

        $parameters = (new ReflectionMethod($object_name, $request->getMethod()))->getParameters();
        
        $parameter = (empty($parameters[0]) ? null : $parameters[0]);

        $request->setRequestMethod(strtolower($_SERVER["REQUEST_METHOD"]));

        if ($parameter && $parameter->getName() == "method" && !self::checkMethods(explode("|", $parameter->getDefaultValue()))) 
            throw new RouterException(0, "exception.router.method_not_exists", $parameter->getDefaultValue());

        if ($parameter && $parameter->getName() == "method" && strtolower($parameter->getDefaultValue()) != "*" && strtolower($parameter->getDefaultValue()) != strtolower(Request::GENERAL) && strtolower($parameter->getDefaultValue()) != strtolower(Request::ALL) && strtolower($parameter->getDefaultValue()) != strtolower(Request::ANY) && !in_array($request->getRequestMethod(), explode("|", strtolower($parameter->getDefaultValue())))) $set404($request);

        Response::setCode(Response::HTTP_OK);
        
        $execute = $request->getArguments();

        array_unshift($execute, $request->getRequestMethod());

        echo @call_user_func_array([ $request->getObject($preffix), $request->getMethod() ], $execute);
    }

    /**
     * @return array
     */
    private static function getPrivateMethods(string $object_name) : array {
        $private_methods = [];

        $class_methods = (new ReflectionClass($object_name))->getMethods(ReflectionMethod::IS_PRIVATE | ReflectionMethod::IS_PROTECTED);

        foreach ($class_methods as $method) $private_methods[] = $method->getName();

        return $private_methods;
    }

    /**
     * @param array $methods
     * @return boolean
     */
    private static function checkMethods(array $methods) : bool {
        $methods = array_map("strtolower", $methods);

        $main = array_map("strtolower", self::$http_methods);

        foreach ($methods as $method) if (!in_array($method, $main)) return false;

        return true;
    }
}