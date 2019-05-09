<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soul)
 */

namespace advanced\http\router;

use advanced\exceptions\RouterException;

class Router{

    private static $http_methods = ["GET", "POST", "DELETE", "PUT", "CONNECT","TRACE", "HEAD", "*", "general", "any", "all"];

    public static function run(Request $request, string $preffix = "advanced") : void {
        if ($preffix == "advanced" && !file_exists($request->getFile("advanced"))){
            self::run($request, "project");

            return;
        }

        if (!method_exists($request->getObjectName($preffix), $request->getMethod())) {
            $request->setController('main');
            $request->setMethod('error404');
        }

        $parameters = (new \ReflectionMethod($request->getObjectName($preffix), $request->getMethod()))->getParameters();

        $parameter = (empty($parameters[0]) ? null : $parameters[0]);

        $request->setRequestMethod(strtolower($_SERVER['REQUEST_METHOD']));

        if ($parameter && $parameter->getName() == 'method' && !self::checkMethods(explode('|', $parameter->getDefaultValue()))) 
            throw new RouterException(0, "exceptions.router.method_not_exists", $parameter->getDefaultValue());

        if ($parameter && $parameter->getName() == 'method' && $parameter->getDefaultValue() != '*' && $parameter->getDefaultValue() != 'general' && $parameter->getDefaultValue() != 'all' && $parameter->getDefaultValue() != 'any' && !in_array($request->getRequestMethod(), explode('|', strtolower($parameter->getDefaultValue())))) {
            $request->setController('main');
            $request->setMethod('error404');
        }
        
        $execute = $request->getArguments();

        array_unshift($execute, $request->getRequestMethod());

        echo @call_user_func_array([ $request->getObject($preffix), $request->getMethod() ], $execute);
    }

    private static function checkMethods(array $methods) : bool {
        $methods = array_map('strtolower', $methods);

        $main = array_map('strtolower', self::$http_methods);

        foreach ($methods as $method) if (!in_array($method, $main)) return false;

        return true;
    }
}