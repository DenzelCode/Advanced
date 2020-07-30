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

namespace advanced\user;

use advanced\Bootstrap;
use advanced\exceptions\{UserException, MailerException};
use advanced\user\auth\Auth;
use advanced\mailer\{Mailer, Receipient, Attachment, ReplyTo};

/**
 * User class
 */
class User extends AbstractUser {

    /**
     * Create a user instance.
     *
     * @param array $data
     * @param string|null $password If you want to sign in using $user->authenticate(), put the non-hashed password here.
     * @throws UserException
     */
    public function __construct(array $data, ?string $password = null) {
        $this->data = $data;

        $this->password = $password;

        UserFactory::setup();

        if (!$this->exists()) {
            $config = Bootstrap::getMainConfig();

            $min = $config->get("sign_up.min_characters", 4);

            $max = $config->get("sign_up.max_characters", 32);

            if (strlen($this->getName()) < $min || strlen($this->getName()) > $max) {
                throw new UserException(0, "exception.user.characters", $min, $max);
            } if (!self::isValidName($this->getName())) {
                throw new UserException(1, "exception.user.invalid_name");
            } else if (!empty($this->getMail()) && !self::isValidMail($this->getMail())) {
                throw new UserException(2, "exception.user.invalid_email");
            } else if (!$this->create()) throw new UserException(3, "exception.database.error", Bootstrap::getSQL()->getLastError());
        }

        $fetch = UserFactory::getProvider()->getAll($this);

        if ($fetch) $this->data = $fetch;
    }

    /**
     * Send mail into the user.
     *
     * @param string $server
     * @param string $subject
     * @param string $body
     * @param ReplyTo $replyTo
     * @param Attachment|Attachment[] $attachments
     * @throws MailerException
     * @return void
     */
    public function sendMail(string $server, string $subject, string $body, $replyTo = null, $attachments = null, bool $html = true) : bool {
        return Mailer::sendMail($server, $subject, $body, $replyTo, $attachments, new Receipient($this->getName(), $this->getMail()));
    }
    
    /**
     * Update the data from the table.
     *
     * @return void
     */
    public function updateData() : void {
        $this->data = UserFactory::getProvider()->getAll($this);
    }

    /**
     * Delete user.
     * 
     * @return bool
     */
    public function delete() : bool {
        if (!$this->exists()) return false;

        return UserFactory::getProvider()->delete($this);
    }

    /**
     * Create user.
     * 
     * @return boolean
     */
    public function create() : bool {
        return UserFactory::getProvider()->create($this->data);
    }

    /**
     * Check if user exists.
     * 
     * @return boolean
     */
    public function exists() : bool {
        $data = UserFactory::getProvider()->getAll($this);

        return !empty($data);
    }

    /**
     * Set user object values by array.
     * 
     * @param array $data
     * @return void
     */
    public function setByArray(array $data) : bool {
        foreach ($data as $key => $value) $this->data[$key] = $value;

        return UserFactory::getProvider()->set($this, $data);
    }

    /**
     * Get all data.
     * 
     * @return array
     */
    public function getAll() : array {
        return !empty($this->data) ? $this->data : [];
    }
}

