<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soul)
 */

namespace advanced;

use advanced\data\Config;
use advanced\http\Response;
use advanced\http\Router\Request;
use advanced\body\template\TemplateProvider;
use advanced\components\LanguageProvider;
use advanced\data\Database;
use advanced\accounts\Users;
use advanced\exceptions\AdvancedException;

/**
* Bootstrap class
*/
class Bootstrap{

    private static $instance;

    private static $classes = [];

    private $data = [];

    public function __construct() {
        // Classes
        self::$instance = $this;

        self::$classes = [
            'request' => Request::getInstance(),
            'templateProvider' => new TemplateProvider(),
            'response' => new Response(),
            'config' => new Config(PROJECT . 'resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config'),
            'defaultConfig' => new Config(ADVANCED . 'resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config')
        ];

        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

        if (!in_array($lang, [])) $lang = 'en';

        self::$classes['languageProvider'] = new LanguageProvider($lang);
        self::$classes['languageProvider']->setPath('advanced');
        
        self::$classes['languageProviderProject'] = new LanguageProvider($lang);
        self::$classes['languageProviderProject']->setPath('project');

        $handler = function ($exception) {
            if (!$exception instanceof \Exception) {
                die($exception);
                
                return;
            }

            die($this->getLanguageProvider(false)->getText('exceptions.exception', null, ($exception instanceof AdvancedException ? $exception->getMsg() : $exception->getMessage()), $exception->getFile(), $exception->getLine()));
        };

        // set_exception_handler($handler);
        // set_error_handler($handler, -1 & ~E_NOTICE & ~E_USER_NOTICE);
    }

    /**
    * @return Bootstrap
    */
    public static function getInstance() : Bootstrap {
        return self::$instance;
    }

    /**
    * @return Request
    */
    public static function getRequest() : Request {
        return self::$classes['request'];
    }

    /**
    * @return Config
    */
    public static function getConfig() : Config {
        return self::$classes['config'];
    }

    /**
    * @return TemplateProvider
    */
    public static function getTemplateProvider() : TemplateProvider {
        return self::$classes['templateProvider'];
    }

    /**
    * @return LanguageProvider
    */
    public static function getLanguageProvider(bool $project = true) : LanguageProvider {
        return self::$classes['languageProvider'];
    }

    /**
    * @return Response
    */
    public static function getResponse() : Response {
        return self::$classes['response'];
    }

    /**
     * @return Database
     */
    public static function getDatabase(): ?Database {
        return self::$classes['database'];
    }

    /**
     * @return Database
     */
    public static function setDatabase(Database $database) : void {
        self::$classes['database'] = $database;
    }

    /**
     * @return Users
     */
    public static function getUsers() : Users {
        if (!self::$classes['users']) self::$classes['users'] = new Users();
        
        return self::$classes['users'];
    }

    public static function setClass(string $name, $object) : void {
        self::$classes[$name] = $object;
    }

    public static function getClass(string $name) {
        return self::$classes[$name];
    }
}

new Bootstrap();