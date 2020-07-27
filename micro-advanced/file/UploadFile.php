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

namespace advanced\file;

class UploadFile {

    public const KB = 1024;
    public const MB = 1048576;
    public const GB = 1073741824;
    public const TB = 1099511627776;

    /**
     * @var array
     */
    private $file = [];

    /**
     * Initialize File object
     *
     * @param string $file
     * @param string $mode
     */
    public function __construct(array $file) {
        $this->file = $file;
    }

    /**
     * Get file name.
     *
     * @return string
     */
    public function getName() : string {
        return $this->file["name"];
    }

    /**
     * Get file name without extension.
     *
     * @return string
     */
    public function getNameWithoutExtension() : string {
        $name = explode(".", $this->getName());

        unset($name[(count($name) - 1)]);

        return implode(".", $name);
    }

    /**
     * Get file extension.
     *
     * @return string
     */
    public function getExtension() : string {
        return end(explode(".", $this->getName()));
    }

    /**
     * Get file temporary name.
     *
     * @return string
     */
    public function getTemporaryName() : string {
        return $this->file["tmp_name"];
    }

    /**
     * Get file type.
     *
     * @return string
     */
    public function getType() : string {
        return $this->file["type"];
    }

    /**
     * Get file size.
     *
     * @return integer
     */
    public function getSize(): int {
        return $this->file["size"];
    }

    /**
     * Get file error.
     *
     * @return integer
     */
    public function getError(): int {
        return $this->file["error"];
    }

    /**
     * Upload file.
     *
     * @param string $path The path where you want to put the file.
     * @return boolean
     */
    public function upload(string $path): bool {
        return move_uploaded_file($this->getTemporaryName(), $path);
    }
}

