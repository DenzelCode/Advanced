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

namespace advanced\mailer;

abstract class Person implements IPerson {

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $mail;

    /**
     * Initialize person.
     * 
     * @param string $name
     * @param string $mail
     */
    public function __construct(string $name, string $mail) {
        $this->name = $name;
        $this->mail = $mail;
    }

    /**
     * Get person name.
     * 
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * Get person mail.
     * 
     * @return string
     */
    public function getMail() : string {
        return $this->mail;
    }
}