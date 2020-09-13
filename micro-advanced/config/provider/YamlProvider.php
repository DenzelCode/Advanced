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

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlProvider implements IProvider {

    /** {@inheritDoc} */
    public function getName(): string {
        return "Yaml";
    }

    /** {@inheritDoc} */
    public function getExtension(): string {
        return ".yml";
    }

    /** {@inheritDoc} */
    public function encode(array $data): string {
        return json_encode($data);
    }

    /** {@inheritDoc} */
    public function decode(string $data): ?array {
        try {
            return Yaml::parse($data, Yaml::PARSE_DATETIME);
        } catch(ParseException $exception) {
            return null;
        }
    }

    /** {@inheritDoc} */
    public function prettyPrint(array $data): string {
        return Yaml::dump($data, 2, 4, Yaml::DUMP_NULL_AS_TILDE);
    }
}
