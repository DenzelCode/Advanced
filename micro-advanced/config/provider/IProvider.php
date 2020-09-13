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

namespace advanced\config\provider;

interface IProvider {

    /**
     * Get providername.
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Get provider extension.
     *
     * @return string
     */
    public function getExtension() : string;

    /**
     * Encode data.
     *
     * @param array $data
     * @return string
     */
    public function encode(array $data) : string;

    /**
     * Decode data.
     *
     * @param string $data
     * @return array|null
     */
    public function decode(string $data) : ?array;

    /**
     * Pretty print data
     *
     * @param array $data
     * @return string
     */
    public function prettyPrint(array $data) : string;
}