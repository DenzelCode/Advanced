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

class Router{

    private static $http_methods = ["GET", "POST", "DELETE", "PUT", "CONNECT","TRACE", "HEAD", "*", "general", "any", "all"];

    public static function run(Request $request, string $preffix = "advanced") : void {
        if ($preffix == "advanced" && !file_exists($request->getFile("advanced"))){
            self::run($request, "project");

            return;
        }

        if (!method_exists($request->getObjectName($preffix), $request->getMethod())) {
            Response::setCode(404);
            $request->setController("main");
            $request->setMethod("error404");
        }

        $parameters = (new \ReflectionMethod($request->getObjectName($preffix), $request->getMethod()))->getParameters();

        $parameter = (empty($parameters[0]) ? null : $parameters[0]);

        $request->setRequestMethod(strtolower($_SERVER["REQUEST_METHOD"]));

        if ($parameter && $parameter->getName() == "method" && !self::checkMethods(explode("|", $parameter->getDefaultValue()))) 
            throw new RouterException(0, "exception.router.method_not_exists", $parameter->getDefaultValue());

        if ($parameter && $parameter->getName() == "method" && strtolower($parameter->getDefaultValue()) != "*" && strtolower($parameter->getDefaultValue()) != "general" && strtolower($parameter->getDefaultValue()) != "all" && strtolower($parameter->getDefaultValue()) != "any" && !in_array($request->getRequestMethod(), explode("|", strtolower($parameter->getDefaultValue())))) {
            Response::setCode(404);
            $request->setController("main");
            $request->setMethod("error404");
        }

        Response::setCode(Response::HTTP_OK);
        
        $execute = $request->getArguments();

        array_unshift($execute, $request->getRequestMethod());

        echo @call_user_func_array([ $request->getObject($preffix), $request->getMethod() ], $execute);
    }

    private static function checkMethods(array $methods) : bool {
        $methods = array_map("strtolower", $methods);

        $main = array_map("strtolower", self::$http_methods);

        foreach ($methods as $method) if (!in_array($method, $main)) return false;

        return true;
    }
}