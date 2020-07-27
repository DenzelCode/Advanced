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
    
    public function getName() : string;
    public function getExtension() : string;
    public function encode(array $data) : string;
    public function decode(string $data) : ?array;
    public function prettyPrint(array $data) : string;
}