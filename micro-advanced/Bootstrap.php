<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace advanced;

use advanced\data\Config;
use advanced\http\Response;
use advanced\http\Router\Request;
use advanced\body\template\TemplateProvider;
use advanced\components\Language;
use advanced\data\Database;
use advanced\accounts\Users;
use advanced\exceptions\AdvancedException;
use advanced\session\Auth;
use advanced\session\SessionManager;

/**
* Bootstrap class
*/
class Bootstrap{

    private static $instance;

    private static $classes = [];
    
    public function __construct() {
        // Classes
        self::$instance = $this;

        self::$classes = [
            'request' => new Request($_SERVER['REQUEST_URI']),
            'auth' => new Auth(),
            'templateProvider' => new TemplateProvider(),
            'response' => new Response(),
            'config' => new Config(PROJECT . 'resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config'),
            'defaultConfig' => ($config = new Config(ADVANCED . 'resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config'))
        ];

        if (!SessionManager::get('language')) SessionManager::set('language', substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2), true);

        if (!in_array(SessionManager::get('language'), $config->get('languages'))) SessionManager::set('language', 'en', true);

        self::$classes['languageMain'] = new Language(SessionManager::get('language'));
        self::$classes['languageMain']->setPath('advanced');
        
        self::$classes['language'] = new Language(SessionManager::get('language'));
        self::$classes['language']->setPath('project');

        $handler = function ($exception) {
            if (!$exception instanceof \Exception) {
                die($exception);
                
                return;
            }

            die($this->getMainLanguage()->get('exceptions.exception', null, ($exception instanceof AdvancedException ? $exception->getTranslatedMessage() : $exception->getMessage()), $exception->getFile(), $exception->getLine()));
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
        self::$classes['templateProvider']->setPath('project');
        
        return self::$classes['templateProvider'];
    }

    /**
    * @return TemplateProvider
    */
    public static function getMainTemplateProvider() : TemplateProvider {
        self::$classes['templateProvider']->setPath('advanced');
        
        return self::$classes['templateProvider'];
    }

    /**
    * @return LanguageProvider
    */
    public static function getLanguage() : Language {
        return self::$classes['language'];
    }

    /**
    * @return LanguageProvider
    */
    public static function getMainLanguage() : Language {
        return self::$classes['languageMain'];
    }

    /**
    * @return Response
    */
    public static function getResponse() : Response {
        return self::$classes['response'];
    }

    /**
    * @return Auth
    */
    public static function getAuth() : Auth {
        return self::$classes['auth'];
    }

    /**
     * @return Database
     */
    public static function setDatabase(Database $database) : void {
        self::$classes['database'] = $database;
    }

    /**
     * @return Database
     */
    public static function getDatabase(): ?Database {
        return (!self::$classes['database'] ? null : self::$classes['database']);
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