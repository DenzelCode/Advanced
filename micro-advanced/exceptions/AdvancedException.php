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

namespace advanced\exceptions;

use advanced\Bootstrap;

/**
* AdvancedException class
*/	
abstract class AdvancedException extends \Exception {

    protected $code;
    protected $message;
    protected $message_code;
    protected $parameters = [];
    
    public function __construct(int $code = 0, string $message = null, ...$parameters) {
        $this->code = $code;
        $this->message_code = $message;
        $this->parameters = $parameters;
        $this->message = $this->getTranslatedMessage();
    }

    public function getParameters() : ?array {
        return $this->parameters;
    }

    public function getTranslatedMessage() : string {
        $arguments = [$this->message_code, null];

        foreach ($this->getParameters() as $parameter) $arguments[] = $parameter;

        $return = @call_user_func_array([ Bootstrap::getMainLanguage(), 'get' ], $arguments);

        return ($return ? $return : $this->message_code);
    }
}
