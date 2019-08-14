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
    public static function set(array $params, array $data = ['prefix' => true]) : void {
        foreach ($params as $key => $value) {
            self::$params[$key]['value'] = $value;
            
            if (empty($data['prefix']) && $data['prefix'] != false) $data['prefix'] = true;

            self::$params[$key]['prefix'] = $data['prefix'];
        }
    }

    /**
     * @return void
     */
    public static function unset(array $params) : void {
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
    public static function getParams() : array {
        return self::$params;
    }

    /**
     * @return string
     */
    public static function filter(string $data) : string {
        foreach (self::getParams() as $key => $param) if (is_string($param['value']) && !$param['prefix']) $data = str_replace($key, $param['value'], $data); else if (is_string($param['value']) && $param['prefix']) $data = str_replace('{@' . $key . '}', $param['value'], $data);

        return $data;
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

        if ($create) File::check($templatePath, Bootstrap::getLanguageProvider(false)->getText('template.default', null, str_replace('/', DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $templatePath))));
        
        self::defaults();

        $templateName = $template;

        $params = [];

        if (file_exists(PROJECT . 'Project.php')) $params['project'] = Project::getInstance();

        foreach (self::getParams() as $key => $param) $params[$key] = $param['value'];

        extract($params);

        switch (true) {
            case !file_exists($templatePath):
                return Bootstrap::getLanguageProvider(false)->getText('template.not_exists', null, $templateName);
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

                    file_put_contents($templateCache, $data);
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
        return in_array($param, array_keys(self::getParams()));
    }

    public static function defaults(bool $force = false) {
        $params = [
            'title' => Bootstrap::getLanguageProvider(false)->getText('template.undefined'),
            'bootstrap' => Bootstrap::getInstance(),
            'language' => new class {
                public function getText(string $key, $default = false, ...$params) {
                    $lang = Bootstrap::getLanguageProvider()->getText($key, ($default == false ? $key : $default), $params);

                    return $lang;
                }
            },
            'advancedLanguage' => Bootstrap::getLanguageProvider(false),
            'template' => TemplateProvider::getInstance(),
            'config' => Bootstrap::getConfig(),
            'request' => Request::getInstance()
        ];

        if (!$force) foreach ($params as $key => $value) if (!self::paramExists($key)) self::set([ $key => $value ]);

        if ($force) foreach ($params as $key => $value) self::set([ $key => $value ]);
    }
}