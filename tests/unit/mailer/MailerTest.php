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

namespace tests\unit\data;

use advanced\body\template\TemplateProvider;
use advanced\mailer\Mailer;
use advanced\mailer\Receipient;
use advanced\mailer\ReplyTo;
use tests\TestCase;

class MailerTest extends TestCase {
    
    public function testSendMail() : void {
        $send = Mailer::sendMail("default", "Testing", TemplateProvider::get("mail/test"), new ReplyTo("Testing", "test@example.com"), new Receipient("Denzel Code", "denzelcodedev@gmail.com"));
        
        $this->assertTrue($send);
    }
}
