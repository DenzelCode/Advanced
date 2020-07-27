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

namespace advanced\exceptions;

use advanced\Bootstrap;
use advanced\language\Language;

/**
* Exeption class
*/	
abstract class Exception extends \Exception {

    /**
     * @var int
     */
    protected $code;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $message_code;

    /**
     * @var array
     */
    protected $parameters = [];
    
    /**
     * Generate a translated exception.
     *
     * @param integer $code
     * @param string $message
     * @param mixed ...$parameters
     */
    public function __construct(int $code = 0, string $message = null, ...$parameters) {
        $this->code = $code;
        $this->message_code = $message;
        $this->parameters = $parameters;
        $this->message = $this->getTranslatedMessage();
    }

    /**
     * Get parameters.
     *
     * @return array|null
     */
    public function getParameters() : ?array {
        return $this->parameters;
    }

    /**
     * Translate message.
     *
     * @return string
     */
    public function getTranslatedMessage() : string {
        $arguments = [$this->message_code, null];

        foreach ($this->getParameters() as $parameter) $arguments[] = $parameter;

        $language = $this->getLanguage();

        $language->getConfig()->setIfNotExists($this->message_code, Bootstrap::getMainLanguage()->get("exception.created", $language->getName()))->saveIfModified();

        $return = @call_user_func_array([ $language, "get" ], $arguments);

        return $return ?? $this->message_code;
    }

    abstract function getLanguage() : Language;
}
