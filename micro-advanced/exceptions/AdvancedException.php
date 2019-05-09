<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soul)
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
        $this->message = $this->getMsg();
    }

    public function getParameters() : ?array {
        return $this->parameters;
    }

    public function getMsg() : string {
        $arguments = [$this->message_code, null];

        foreach ($this->getParameters() as $parameter) $arguments[] = $parameter;

        $return = @call_user_func_array([ Bootstrap::getLanguageProvider(false), 'getText' ], $arguments);

        return ($return ? $return : $this->message_code);
    }
}
