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

use advanced\config\Config;
use advanced\exceptions\MailerException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer {

    /**
     * @var string
     */
    private static $configPath = PROJECT . "resources" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "mailer";

    /**
     * @var IPerson[]
     */
    private $recipients = [];

    /**
     * @var Attachments[]
     */
    private $attachments = [];

    /**
     * @var IPerson
     */
    private $replyTo = null;

    /**
     * @var string|null
     */
    private $subject;

    /**
     * @var string|null
     */
    private $body;

    /**
     * @var string
     */
    private $server;

    /**
     * @var boolean
     */
    private $html = true;

    /**
     * @var Config
     */
    private $config;

    public function __construct(string $server = "default") {
        $this->server = $server;

        $this->config = new Config(self::$configPath);

        $this->config->setIfNotExists("server.{$server}", [
            "name" => "Testing",
            "host" => "host",
            "port" => 587 ,
            "secure" => "tls",
            "username" => "mail@example.com",
            "password" => "password",
            "address" => "mail@example.com"
        ])->saveIfModified();
    }

    /**
     * Send mail.
     *
     * @param string $server
     * @return boolean
     * @throws MailerException
     */
    public function send() : bool {
        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();

            $mail->Host = $this->config->get("server.{$this->server}.host");
            $mail->Port = $this->config->get("server.{$this->server}.port");
            $mail->SMTPSecure = $this->config->get("server.{$this->server}.secure");
            $mail->CharSet = $this->config->get("server.{$this->server}.charset", "utf-8");
            $mail->Encoding = $this->config->get("server.{$this->server}.encoding", "base64");

            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get("server.{$this->server}.username");
            $mail->Password = $this->config->get("server.{$this->server}.password");

            $mail->From = $this->config->get("server.{$this->server}.address");
            $mail->FromName = $this->config->get("server.{$this->server}.name");

            if ($this->replyTo instanceof IPerson) $mail->addReplyTo($this->replyTo->getMail(), $this->replyTo->getName()); else $mail->addReplyTo($this->config->get("server.{$this->server}.address"), $this->config->get("server.{$this->server}.name"));

            $mail->Subject = $this->subject;
            $mail->isHTML($this->html);
            $mail->msgHTML($this->body);

            foreach ($this->recipients as $receipient) $mail->addAddress($receipient->getMail(), $receipient->getName());

            foreach ($this->attachments as $attachment) $mail->addAttachment($attachment->getPath(), $attachment->getName());

            $mail->send();
        } catch (Exception $e) {
            throw new MailerException(0, "exception.mailer.error", $e->getMessage());
        } 
        
        return true;
    }

    /**
     * Add receipient.
     *
     * @param IPerson $recipient
     * @return Mail
     */
    public function addRecipient(IPerson $recipient) : Mail {
        $this->recipients[] = $recipient;

        return $this;
    }

    /**
     * Add receipients.
     *
     * @param IPerson[] $recipients
     * @return Mail
     */
    public function addRecipients(array $recipients) : Mail {
        foreach ($recipients as $recipient) $this->addRecipient($recipient);

        return $this;
    }

    /**
     * Add attachment.
     *
     * @param Attachment $attachment
     * @return void
     */
    public function addAttachment(Attachment $attachment) : Mail {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * Add attachments.
     *
     * @param Attachment[] $attachments
     * @return Mail
     */
    public function addAttachments(array $attachments) : Mail {
        foreach ($attachments as $attachment) $this->addAttachment($attachment);

        return $this;
    }

    /**
     * Get receipients
     *
     * @return IPerson[]
     */
    public function getReceipients() : array {
        return $this->recipients;
    }

    /**
     * Get mail subject.
     *
     * @return string|null
     */
    public function getSubject() : ?string {
        return $this->subject;

        return $this;
    }

    /**
     * Set mail subject.
     *
     * @param string|null $subject
     * @return Mail
     */
    public function setSubject(?string $subject) : Mail {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get mail body.
     *
     * @return string|null
     */
    public function getBody() : ?string {
        return $this->body;
    }

    /**
     * Set mail body.
     *
     * @param string|null $body
     * @return Mail
     */
    public function setBody(?string $body) : Mail {
        $this->body = $body;

        return $this;
    }

    /**
     * Check if the body is HTML.
     *
     * @return boolean
     */
    public function isHTML() : bool {
        return $this->html;
    }

    /**
     * Set the body into HTML.
     *
     * @return Mail
     */
    public function setHTML(bool $html = true) : Mail {
        $this->html = $html;

        return $this;
    }

    /**
     * Set the person that the receiver is going to reply to.
     *
     * @param IPerson|null $person
     * @return Mail
     */
    public function setReplyTo(?IPerson $person) : Mail {
        $this->replyTo = $person;

        return $this;
    }

    /**
     * Send a mail.
     *
     * @param string $server
     * @param string $subject
     * @param string $body
     * @param ReplyTo $replyTo
     * @param Recipient|Recipient[]                                                                                                                                                                                                                                                                                                                                                                                                                                                 $recipients
     * @param Attachment|Attachment[] $attachments
     * @param boolean $html
     * @return boolean
     * @throws MailerException
     */
    public static function sendMail(string $server = "default", string $subject, string $body, $replyTo = null, $recipients = null, $attachments = null, bool $html = true) : bool {
        $recipients = (is_null($recipients) ? [] : ($recipients instanceof Receipient ? [$recipients] : (is_array($recipients) ? $recipients : [])));

        $attachments = (is_null($attachments) ? [] : ($attachments instanceof Attachment ? [$attachments] : (is_array($attachments) ? $attachments : [])));

        return (new Mail($server))
            ->setSubject($subject)
            ->setBody($body)
            ->setReplyTo($replyTo)
            ->addRecipients($recipients)
            ->addAttachments($attachments)
            ->setHTML($html)
            ->send();
    }

    /**
     * Generate a new instance of a mail
     *
     * @param string $server
     * @return Mail
     */
    public function newInstance(string $server = "default") : Mail {
        return new Mail($server);
    }
        
    /**
     * Get config path.
     * 
     * @return string
     */
    public static function getConfigPath() : string {
        return self::$configPath;
    }

    /**
     * Set config path.
     * 
     * @param string $configPath
     * @return void
     */
    public static function setConfigPath(string $configPath) : void {
        self::$configPath = $configPath;
    }
}