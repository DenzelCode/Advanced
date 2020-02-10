<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace advanced\body\template;

use advanced\Bootstrap;
use advanced\http\router\RequestProvider;
use advanced\utils\File;
use project\Project;
use advanced\http\router\Request;
use advanced\session\Auth;

class TemplateProvider{
      
    private static $params = [];

    private static $instance;

    private static $path;

    public function __construct() {
        self::$instance = $this;

        self::setPath('project');
    }

    /**
     * @return TemplateProvider
     */
    public static function getInstance() : TemplateProvider {
        return self::$instance;
    }

    /**
     * @return void
     */
    public static function setParameter(string $key, $value, bool $prefix = true) : void {
        self::$params[$key]['value'] = $value;
        self::$params[$key]['prefix'] = $prefix;
    }

    /**
     * @return void
     */
    public static function setParameters(array $params, bool $prefix = true) : void {
        foreach ($params as $key => $value) {
            self::$params[$key]['value'] = $value;

            self::$params[$key]['prefix'] = $prefix;
        }
    }

    /**
     * @return void
     */
    public static function unsetParams(array $params) : void {
        foreach ($params as $key) unset(self::$params[$key]);
    }

    /**
     * @return string|null
     */
    public static function getParam(string $param) : ?string {
        if (self::paramExists($param)) return self::$params[$param]; else return null;
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
        foreach (self::getParameters() as $key => $param) if (is_string($param['value']) && !$param['prefix']) $data = str_replace($key, $param['value'], $data); else if (is_string($param['value']) && $param['prefix']) $data = str_replace('{@' . $key . '}', $param['value'], $data);

        return $data;
    }

    public static function getRootTemplate(string $template) : string {
        TemplateProvider::setPath('advanced');

        $template = TemplateProvider::get($template);

        TemplateProvider::setPath('project');

        return $template;
    }

    public static function getRootTemplates(array $templates) : string {
        TemplateProvider::setPath('advanced');

        $template = TemplateProvider::getByArray($templates);

        TemplateProvider::setPath('project');

        return $template;
    }

    /**
     * @return string
     */
    public static function filterTemplate(string $data) : string {
        $data = preg_replace("/{#\s*(if\s*\(.*\))\s*#}/i", '{#$1:#}', $data);
        $data = preg_replace("/{#\s*\/if\s*#}/i", '{#endif;#}', $data);
        $data = preg_replace("/{#\s*(elseif\s*\(.*\))\s*#}/i", '{#$1:#}', $data);
        $data = preg_replace("/{#\s*else\s*#}/i", '{#else:#}', $data);
        $data = preg_replace("/{#\s*(foreach\s*\(.*\))\s*#}/i", '{#$1:#}', $data);
        $data = preg_replace("/{#\s*\/foreach\s*#}/i", '{#endforeach;#}', $data);
        $data = preg_replace("/{#\s*(switch\s*\(.*\))\s*#}/i", '{#$1:#}', $data);
        $data = preg_replace("/{#\s*\/switch\s*#}/i", '{#endswitch;#}', $data);
        $data = preg_replace("/{#\s*(case\s*.*)\s*#}/i", '{#$1:#}', $data);
        $data = preg_replace("/{#\s*\/case\s*#}/i", '{#break;#}', $data);
        $data = preg_replace("/{#\s*(for\s*\(.*\))\s*#}/i", '{#$1:#}', $data);
        $data = preg_replace("/{#\s*\/for\s*#}/i", '{#endfor;#}', $data);
        $data = preg_replace("/{#\s*(while\s*\(.*\))\s*#}/i", '{#$1:#}', $data);
        $data = preg_replace("/{#\s*\/while\s*#}/i", '{#endwhile;#}', $data);
        $data = preg_replace("/{#\s*(\\$[a-zA-Z\[\]\'\_\$]*)\s*#}/i", '{#=$1#}', $data);
        $data = str_replace("{*", "{# /* ", $data);
        $data = str_replace("*}", "*/ #}", $data);
        $data = str_replace("{#=", "<?= ", $data);
        $data = str_replace("{#", "<?php ", $data);
        $data = str_replace("#}", "?>", $data);

        return $data;
    }

    /**
     * @return string
     */
    public static function getByArray(array $templates) : string {
        $returns = [];

        foreach ($templates as $template) $returns[] = self::get($template);

        return implode(false, $returns);
    }

    /**
     * @return void
     */
    public static function setPath(string $path) : void {
        self::$path = $path;
    }

    /**
     * @return string
     */
    public static function getPath() : string {
        return (self::$path == 'advanced' ? ADVANCED : PROJECT) . 'body' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public static function get(string $template, bool $cache = true, bool $create = true) : string {
        $templatePath = self::getPath() . 'views' . DIRECTORY_SEPARATOR . $template .  '.tpl';

        $templateCache = self::getPath() . 'cache' . DIRECTORY_SEPARATOR . $template .  '.php';

        if ($create) File::check($templatePath, Bootstrap::getMainLanguage()->get('template.default', null, str_replace('/', DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $templatePath))));
        
        self::setDefaultParameters();

        $templateName = $template;

        $params = [];

        if (file_exists(PROJECT . 'Project.php')) $params['project'] = Project::getInstance();

        foreach (self::getParameters() as $key => $param) $params[$key] = $param['value'];

        extract($params);

        switch (true) {
            case !file_exists($templatePath):
                return Bootstrap::getMainLanguage()->get('template.not_exists', null, $templateName);
            default:
                $write_cache = true;

                if (is_file($templateCache)) {
                    $mtime_cache = filemtime($templateCache);

                    $mtime_view = filemtime($templatePath);

                    if ($mtime_view <= $mtime_cache) $write_cache = false;
                }

                if ($write_cache && $cache) {
                    $data = file_get_contents($templatePath);

                    $data = self::filterTemplate($data);

                    if (!is_dir(dirname($templateCache))) mkdir(dirname($templateCache), 777, true);

                    File::write($templateCache, $data);
                }

                // Start
                ob_start();
                // Include
                if ($cache) include($templateCache); else include($templatePath);
                echo "\n";
                // Content
                $data = ob_get_contents();
                // Clean
                ob_end_clean();
                // Return
                return self::filter($data);
        }
    }

    public static function paramExists(string $param) : bool {
        return in_array($param, array_keys(self::getParameters()));
    }

    public static function getDefaultParameters() : array {
        $params = [
            'title' => Bootstrap::getMainLanguage()->get('template.undefined'),
            'bootstrap' => Bootstrap::getInstance(),
            'language' => Bootstrap::getLanguage(),
            'advancedLanguage' => Bootstrap::getMainLanguage(),
            'template' => self::getInstance(),
            'isAuthenticated' => Auth::isAuthenticated(),
            'auth' => Auth::getInstance(),
            'authUser' => Auth::getUser(),
            'config' => Bootstrap::getConfig(),
            'request' => Request::getInstance()
        ];

        return $params;
    }

    public static function setDefaultParameters(bool $force = false) {
        foreach (self::getDefaultParameters() as $key => $value) {
            if (!$force && !self::paramExists($key) || $force) self::setParameter($key, $value);
        }
    }
}