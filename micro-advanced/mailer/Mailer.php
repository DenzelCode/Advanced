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

use advanced\config\Config;
use advanced\exceptions\MailerException;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer {

    public static function sendMail(string $server, string $subject, string $body, $recipients, $attachments = null) : bool {
        $config = new Config(PROJECT . "resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "mailer");

        if (!$config->has("server.{$server}")) {
            $config->set("server.{$server}", [
                "name" => "Testing",
                "host" => "host",
                "port" => 587,
                "secure" => "tls",
                "username" => "mail@example.com",
                "password" => "password",
                "address" => "mail@example.com"
            ])->saveIfModified();

            return false;
        }
        
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $config->get("server.{$server}.host");
        $mail->Port = $config->get("server.{$server}.port");
        $mail->SMTPSecure = $config->get("server.{$server}.secure");
        $mail->CharSet = $config->get("server.{$server}.charset", "utf-8");
        $mail->Encoding = $config->get("server.{$server}.encoding", "base64");
        $mail->SMTPAuth = true;
        $mail->Username = $config->get("server.{$server}.username");
        $mail->Password = $config->get("server.{$server}.password");
        $mail->From = $config->get("server.{$server}.address");
        $mail->FromName = $config->get("server.{$server}.name");
        $mail->addReplyTo($config->get("server.{$server}.address"), $config->get("server.{$server}.name"));

        $mail->isHTML(true);
        $mail->msgHTML($body);
        $mail->Subject = $subject;

        if ($recipients instanceof Receipient) 
            $mail->addAddress($recipients->getMail(), $recipients->getName()); 
        else if (is_array($recipients))
            foreach ($recipients as $receipient) $mail->addAddress($receipient->getMail(), $receipient->getName());

        if ($attachments instanceof Attachment) 
            $mail->addAttachment($attachments->getPath(), $attachments->getName()); 
        else if (is_array($attachments))
            foreach ($attachments as $attachment) $mail->addAttachment($attachment->getPath(), $attachment->getName());

        if (!$mail->send()) throw new MailerException(0, "exception.mailer.error", $mail->ErrorInfo);
        
        return true;
    }
}