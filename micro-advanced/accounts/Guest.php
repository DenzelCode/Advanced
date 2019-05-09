<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace advanced\accounts;

use advanced\Bootstrap;

/**
 * Guest class
 */
class Guest extends BaseUser {

    public function __construct() {
        self::$instance = $this;

        $config = Config::getInstance()->get('sign_up');

        $data = [
            'id' => 0,
            'username' => Bootstrap::getInstance()->getLanguageProvider()->getText('general.guest'),
            'rank' => 1,
            'prefer' => (Auth::get('prefer') ? Auth::get('prefer') : 0),
            'gender' => (Auth::get('gender') ? Auth::get('gender') : "M"),
            'ip_last' => Request::getIp(),
            'ip_reg' => Request::getIp(),
            'connection_id' => (Auth::get('connection_id') ? Auth::get('connection_id') : 0),
            'display_name' => Boostrap::getInstance()->getLanguageProvider()->getText('general.guest')
        ];

        foreach ($config['user'] as $key => $value) $data[$key] = $value;

        $this->set($data);
    }

    /**
     * @return bool
     */
    public function set(array $data) : bool {
        $keys = ['username', 'id', 'password'];

        foreach ($data as $key => $value) {
            if (!in_array($key, array_keys($keys))) Auth::set([ $key => $value ]);

            $this->data[$key] = $value;
        }

        return true;
    }
}

