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

namespace advanced\mailer;

use PHPMailer\PHPMailer\PHPMailer;

class Receipient {

    private $name;
    private $mail;

    public function __construct(string $name, string $mail) {
        $this->name = $name;
        $this->mail = $mail;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getMail() : string {
        return $this->mail;
    }
}