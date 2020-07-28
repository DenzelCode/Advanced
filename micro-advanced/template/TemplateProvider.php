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

namespace advanced\template;

use advanced\Bootstrap;
use advanced\file\File;
use project\Project;
use advanced\http\router\Request;
use advanced\user\auth\Auth;
use advanced\user\UserFactory;

class TemplateProvider{

    public const PATH_ADVANCED = "advanced";
    public const PATH_PROJECT = "project";
    
    /**
     * @var array
     */
    private static $params = [];

    /**
     * @var TemplateProvider
     */
    private static $instance = null;

    /**
     * @var string
     */
    private static $path;

    public function __construct() {
        self::$instance = $this;

        TemplateProvider::setProjectMode();
    }

    /**
     * @return TemplateProvider
     */
    public static function getInstance() : TemplateProvider {
        if (!self::$instance) self::$instance = new TemplateProvider();

        return self::$instance;
    }

    /**
     * Set parameter into templates.
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $prefix
     * @return void
     */
    public static function setParameter(string $key, $value, bool $prefix = true) : void {
        self::$params[$key]["value"] = $value;

        self::$params[$key]["prefix"] = $prefix;
    }

    /**
     * Set parameters into templates.
     *
     * @param array $params
     * @param boolean $prefix
     * @return void
     */
    public static function setParameters(array $params, bool $prefix = true) : void {
        foreach ($params as $key => $value) self::setParameter($key, $value, $prefix);
    }
    
    /**
     * Unset parameter.
     *
     * @param string $key
     * @return void
     */
    public static function unsetParameter(string $key) : void {
        unset(self::$params[$key]);
    }

    /**
     * Unset parameters
     *
     * @param array $params
     * @return void
     */
    public static function unsetParameters(array $params) : void {
        foreach ($params as $key) self::unsetParameter($key);
    }

    /**
     * Get parameter value.
     *
     * @param string $param
     * @return string|null
     */
    public static function getParameter(string $param) : ?string {
        return !empty(self::$params[$param]) ? self::$params[$param] : null;
    }

    /**
     * @return array
     */
    public static function getParameters() : array {
        return self::$params;
    }

    /**
     * @return string
     */
    public static function filter(string $data) : string {
        error_reporting(E_ALL);
        foreach (self::getParameters() as $key => $param) {
            foreach (self::getParameters() as $k => $v) {
                if ($key == $k) break;

                if  (!is_string($param["value"])) break; 

                $prefix = $v["prefix"] == true ? "{@" . $k . "}" : $k;

                $value = is_string($v["value"]) ? $v["value"] : Bootstrap::getMainLanguage()->get("error.parameter_not_string", null, $prefix);

                $param["value"] = str_replace($prefix, $value, $param["value"]);
            }

            $prefix = $param["prefix"] == true ? "{@" . $key . "}" : $key;

            $value = is_string($param["value"]) ? $param["value"] : Bootstrap::getMainLanguage()->get("error.parameter_not_string", null, $prefix);

            $data = str_replace($prefix, $value, $data);
        }

        return $data;
    }

    /**
     * Get template from the Advanced path.
     *
     * @param string $template
     * @return string
     */
    public static function getRootTemplate(string $template) : string {
        TemplateProvider::setProjectMode(false);

        $template = TemplateProvider::get($template);

        TemplateProvider::setProjectMode();

        return $template;
    }

    /**
     * Get templates from the Advanced path.
     *
     * @param array $templates
     * @return string
     */
    public static function getRootTemplates(array $templates) : string {
        TemplateProvider::setProjectMode(false);

        $template = TemplateProvider::getByArray($templates);

        TemplateProvider::setProjectMode();

        return $template;
    }

    /**
     * Filter template code.
     *
     * @param string $data
     * @return string
     */
    public static function filterTemplate(string $data) : string {
        $data = preg_replace("/{#\s*(if\s*\(.*\))\s*#}/i", "{#$1:#}", $data);
        $data = preg_replace("/{#\s*\/if\s*#}/i", "{#endif;#}", $data);
        $data = preg_replace("/{#\s*(elseif\s*\(.*\))\s*#}/i", "{#$1:#}", $data);
        $data = preg_replace("/{#\s*else\s*#}/i", "{#else:#}", $data);
        $data = preg_replace("/{#\s*(foreach\s*\(.*\))\s*#}/i", "{#$1:#}", $data);
        $data = preg_replace("/{#\s*\/foreach\s*#}/i", "{#endforeach;#}", $data);
        $data = preg_replace("/{#\s*(switch\s*\(.*\))\s*#}/i", "{#$1:#}", $data);
        $data = preg_replace("/{#\s*\/switch\s*#}/i", "{#endswitch;#}", $data);
        $data = preg_replace("/{#\s*(case\s*.*)\s*#}/i", "{#$1:#}", $data);
        $data = preg_replace("/{#\s*\/case\s*#}/i", "{#break;#}", $data);
        $data = preg_replace("/{#\s*(for\s*\(.*\))\s*#}/i", "{#$1:#}", $data);
        $data = preg_replace("/{#\s*\/for\s*#}/i", "{#endfor;#}", $data);
        $data = preg_replace("/{#\s*(while\s*\(.*\))\s*#}/i", "{#$1:#}", $data);
        $data = preg_replace("/{#\s*\/while\s*#}/i", "{#endwhile;#}", $data);
        $data = preg_replace("/{#\s*(\\$[a-zA-Z\[\]\"\_\$]*)\s*#}/i", "{#=$1#}", $data);
        $data = str_replace("{*", "{# /* ", $data);
        $data = str_replace("*}", "*/ #}", $data);
        $data = str_replace("{#=", "<?= ", $data);
        $data = str_replace("{#", "<?php ", $data);
        $data = str_replace("#}", "?>", $data);

        return $data;
    }

    /**
     * Get a list of templates together by order.
     *
     * @param array $templates
     * @return string
     */
    public static function getByArray(array $templates) : string {
        $returns = [];

        foreach ($templates as $template) $returns[] = self::get($template);

        return implode(false, $returns);
    }

    /**
     * Enable/disable project mode.
     *
     * @param boolean $value
     * @return void
     */
    public static function setProjectMode(bool $value = true) : void {
        self::$path = $value ? self::PATH_PROJECT : self::PATH_ADVANCED;
    }

    /**
     * @return string
     */
    public static function getPath() : string {
        return (self::$path == self::PATH_ADVANCED ? ADVANCED : PROJECT) . "template" . DIRECTORY_SEPARATOR;
    }

    /**
     * Get a template.
     *
     * @param string $template
     * @param boolean $cache
     * @param boolean $create
     * @return string
     */
    public static function get(string $template, bool $cache = true, bool $create = true, string $extension = "atpl") : string {
        $templateName = self::getPath() . "views" . DIRECTORY_SEPARATOR . $template;

        $templatePath = new File($templateName . "." . $extension);

        $templateCache = new File(self::getPath() . "cache" . DIRECTORY_SEPARATOR . $template .  ".php");

        if (!$templatePath->exists() && ($oldTemplate = new File($templateName . ".tpl"))->exists()) {
            $names = explode(".", $oldTemplate->getName());

            $names[(count($names) - 1)] = "atpl";

            $oldTemplate->setName(implode(".", $names));

            $templateCache->delete();
        }

        $oldPath = (self::$path == self::PATH_ADVANCED ? ADVANCED : PROJECT) . "body" . DIRECTORY_SEPARATOR . "template" . DIRECTORY_SEPARATOR;

        if (!$templatePath->exists() && ($oldTemplate = new File($oldPath . $templateNam . "." . $extension))->exists()) $oldTemplate->setPath($oldPath . $templateName . "." . $extension);
        
        if ($create) $templatePath->create(Bootstrap::getMainLanguage()->get("template.default", null, str_replace("/", DIRECTORY_SEPARATOR, str_replace("\\", DIRECTORY_SEPARATOR, $templatePath->getPath()))));

        self::setDefaultParameters();

        $parameters = [];

        if (file_exists(PROJECT . "Project.php")) $parameters["project"] = Project::getInstance();

        foreach (self::getParameters() as $key => $parameter) $parameters[$key] = $parameter["value"];

        switch ($templatePath->exists()) {
            case true:
                if ((is_file($templateCache->getPath()) && filemtime($templateCache->getPath()) <= filemtime($templatePath->getPath()) || !is_file($templateCache->getPath())) && $cache) {
                    $data = $templatePath->read();

                    $data = self::filterTemplate($data);

                    ($templateCache->exists() ? $templateCache->write($data) : $templateCache->create($data));
                }

                $data = ($cache ? $templateCache : $templatePath)->read($parameters) . "\n";

                return self::filter($data);

            case false:
                return Bootstrap::getMainLanguage()->get("template.not_exists", null, $template->getPath());
        }
    }

    /**
     * Check if a parameter exists.
     *
     * @param string $param
     * @return boolean
     */
    public static function parameterExists(string $param) : bool {
        return in_array($param, array_keys(self::getParameters())) || self::getParameter($param) != null;
    }
 
    /**
     * Get templates default parameters.
     *
     * @return array
     */
    public static function getDefaultParameters() : array {
        return [
            "title" => Bootstrap::getMainLanguage()->get("template.undefined"),
            "bootstrap" => Bootstrap::getInstance(),
            "language" => Bootstrap::getLanguage(),
            "advancedLanguage" => Bootstrap::getMainLanguage(),
            "template" => self::getInstance(),
            "isAuthenticated" => Auth::isAuthenticated(),
            "auth" => Auth::getInstance(),
            "authUser" => Auth::getUser(),
            "config" => Bootstrap::getConfig(),
            "request" => Request::getInstance(),
            "url" => Request::getInstance()->getFullURL(),
            "database" => Bootstrap::getDatabase(),
            "sql" => Bootstrap::getSQL(),
            "userFactory" => UserFactory::getInstance()
        ];
    }

    /**
     * Set templates default parameters.
     *
     * @param boolean $force
     * @return void
     */
    public static function setDefaultParameters(bool $force = false) {
        foreach (self::getDefaultParameters() as $key => $value) {
            if (!$force && !self::parameterExists($key) || $force) self::setParameter($key, $value);
        }
    }
}
