<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soul)
 */

namespace advanced\accounts\base;

/**
 * User abstract class
 */
abstract class User {

    private $data = [ 'birth_date' => '0/0/2000' ];

    private $authData = [];

    public function __construct(array $data, array $authData = []) {
        $this->data = $data;

        $this->authData = $authData;

        if (!$this->exists()) {
            $config = self::getBootrap()->getConfig();

            $userChars = strlen($this->getName());

            $min = $config->get('sign_up.min_characters');

            $max = $config->get('sign_up.max_characters');

            if ($userChars < $min || $userChars > $max) {
                throw new UserException(0, 'user.characters', $min, $max);
            } if (!self::isValidName($this->getName())) {
                throw new UserException(1, 'user.invalid_name');
            } else if (!empty($this->getMail()) && !self::isValidMail($this->getMail())) {
                throw new UserException(1, 'user.invalid_email');
            } else {
                $create = $this->create();

                if (!$create) throw new UserException(1, 'database.error');
            }
        }

        $name = strtolower($this->getName());

        $query = self::getBootrap()->getDatabase()->setTable('users')->select(['*'], "WHERE id = ? AND username = ?", [$this->getId(), $name]);

        switch (true) {
            case $this->getName() != "" && $this->getId() == 0:
                $query = self::getBootrap()->getDatabase()->setTable('users')->select(['*'], "WHERE username = ?", [$name]);
                break;
            case $this->getName() == "" && $this->getId() != 0:
                $query = self::getBootrap()->getDatabase()->setTable('users')->select(['*'], "WHERE id = ?", [$this->getId()]);
                break;
        }

        $data = array_merge($this->getDataArray(), $query->fetch());

        if (!empty($data)) $this->setDataArray($data);
    }

    /**
     * @return Boostrap
     */
    public static function getBootstrap() : Boostrap {
        return Bootstrap::getInstance();
    }

    public function getDataArray() : array {
        return $this->data;
    }

    public function setDataArray(array $data) {
        foreach ($data as $key => $value) $this->data[$key] = $value;
    }

    public function get(string $data) {
        return $this->data[$data];
    }

    public function setData(string $name, $value) {
        $this->data[$name] = $value;
    }

    public function getAuthData(string $data) {
        return $this->authData[$data];
    }

    public function setAuthData(string $name, $value) {
        $this->authData[$name] = $value;
    }

    public function getAuthDataArray() : array {
        return $this->authData;
    }

    public function setAuthDataArray(array $authData = []) {
        foreach ($authData as $key => $value) $this->authData[$key] = $value;
    }

    /**
     * @return int
     */
    public function getId() : int {
        return (int) $this->get('id');
    }

    /**
     * @return string
     */
    public function getName() : string {
        return (string) $this->get('username');
    }

    /**
     * @return string
     */
    public function getDisplayName() : string {
        return (string) $this->get('display_name');
    }

    /**
     * @return string
     */
    public function getGender() : string {
        return (string) $this->get('gender');
    }

    /**
     * @return string
     */
    public function getMail() : string {
        return (string) $this->get('mail');
    }

    /**
     * @return string
     */
    public function getPassword() : string {
        return (string) $this->get('password');
    }

    /**
     * @return int
     */
    public function getAccountCreated() : int {
        return (int) $this->get('account_created');
    }

    /**
     * @return string
     */
    public function getConnectionId() : string {
        return (string) $this->get('connection_id');
    }
    
    /**
     * @return User
     */
    public static function getInstance() : User {
        return self::$instance;
    }

    /**
     * @return bool
     */
    private function create() : bool {
        $insert = self::getBootrap()->getDatabase()->setTable('users')->insert($this->getDataArray());

        return $insert;
    }

    /**
     * @return bool
     */
    public function exists() {
        $name = strtolower($this->getName());

        $query = self::getBootrap()->getDatabase()->setTable('users')->select(['username'], "WHERE username = ?", [$name]);

        $exist = $query->fetchAll();

        return (count($exist) ? true : false);
    }

    /**
     * @return bool
     */
    public function set(array $data) : bool {
        $return = self::getBootrap()->getDatabase()->setTable('users')->update($data, "WHERE id = ?", [$this->getId()]);

        if ($return) foreach ($data as $key => $value) $this->setData($key, $value);

        return $return;
    }

    /**
     * @return array
     */
    public function getAll() {
        $query = self::getBootrap()->getDatabase()->setTable('users')->select(['*'], "WHERE id = ? AND username = ?", [$this->getId(), $this->getName()]);

        $this->setDataArray($query->fetch());

        if (!empty($this->getDataArray())) return $this->getDataArray(); else return false;
    }

    /**
     * @return bool
     */
    public static function isValidName(string $name) : bool {
        $config = self::getBootrap()->getConfig();

        $userCheck = preg_match('/^(?=.*[a-zA-Z]{1,})(?=.*[\d]{0,})[a-zA-Z0-9=?!@:.-]{' . $config->get('sign_up')['min_characters'] . ',' . $config->get('sign_up')['max_characters'] . '}$/', $name);

        $isMail = filter_var($name, FILTER_VALIDATE_EMAIL);

        $invalidNames = ['Guest', 'guest', $config->get('web')['name'], (new Guest())->getName()];

        if (!$userCheck || $isMail || in_array($name, $invalidNames)) return false;

        return true;
    }

    public static function isValidDisplayName(string $name) : bool {
        return !preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $name);
    }

    /**
     * @return string
     */
    public static function generateToken(int $length = 40) : string {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz+';
        $token = "";

        for ($i = 0; $i < $length; $i++) $token .= $characters[mt_rand(0, strlen($characters) - 1)];

        return $token;
    }

    /**
     * @return bool
     */
    public static function isValidMail(string $mail) : bool {
        return filter_var($mail, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @return bool
     */
    public function delete() : bool {
        if (!$this->exists()) return false;

        return self::getBootstrap()->getDatabase()->setTable('users')->delete("WHERE id = ?", [$this->getId()]);
    }
}

