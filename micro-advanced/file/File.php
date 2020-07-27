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

use advanced\exceptions\FileException;
use Exception;

class File implements IFile{

    /**
     * @var string
    */
    private $path;

    /**
     * @var string
    */
    private $name;

    /**
     * @var Directory
    */
    private $directory;

    /**
     * @var string
    */
    private $mode;

    /**
     * Initialize File object
     *
     * @param string $file
     * @param string $mode
     */
    public function __construct(string $file, string $mode = "w") {
        $this->path = $file;
        $this->path = str_replace("\\", DIRECTORY_SEPARATOR, $this->path);
        $this->path = str_replace("/", DIRECTORY_SEPARATOR, $this->path);

        $this->directory = new Directory(dirname($this->path));

        $this->mode = $mode;
    }

    /**
     * Write file content.
     *
     * @param string $content
     * @return void
     * @throws FileException
     */
    public function write(string $content) : void {
        if (!($handle = @fopen($this->path, $this->mode))) throw new FileException(0, "exception.file.open", $this->path);

        if (!fwrite($handle, (string) $content)) throw new FileException(1, "exception.file.write", $this->path);

        fclose($handle);
    }

    /**
     * Append content into a file.
     *
     * @param string $content
     * @return void
     * @throws FileException
     */
    public function append(string $content) : void {
        $this->write($this->read() . $content);
    }

    /**
     * Create file and directories if not exists.
     *
     * @param string $default
     * @param integer $permission
     * @return void
     * @throws FileException
     */
    public function create(string $default = null, int $permission = 0777): void {
        if ($this->exists()) return;

        $this->directory->create($permission);

        $this->write($default);

        $this->setPermission($permission);
    }

    /**
     * Get file name.
     *
     * @return string
     */
    public function getName() : string {
        return $this->name;
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
     * Set file name.
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void {
        rename($this->path, $this->directory->getPath() . DIRECTORY_SEPARATOR . $name);

        $this->name = $name;
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
     * Get file path.
     * 
     * @return string
     */
    public function getPath() : string {
        return $this->path;
    }

    /**
     * Set file path.
     *
     * @param string $path
     * @return void
     */
    public function setPath(string $path): void {
        rename($this->path, $path);

        $this->path = $path;

        $this->name = end(explode(DIRECTORY_SEPARATOR, $this->path));

        $this->directory = new Directory(dirname($this->path));
    }

    /**
     * Get file permissions.
     *
     * @return integer
     */
    public function getPermission(): int {
        return fileperms($this->path);
    }

    /**
     * Set file permissions.
     *
     * @param integer $permission
     * @return void
     */
    public function setPermission(int $permission): void {
        chmod($this->path, $permission);
    }

    /**
     * Get file size
     *
     * @return integer
     */
    public function getSize(): int {
        return filesize($this->path);
    }

    /**
     * Check if file exists.
     *
     * @return boolean
     */
    public function exists(): bool {
        return file_exists($this->path);
    }

    /**
     * Delete a file
     *
     * @return void
     */
    public function delete(): bool {
        return @unlink($this->directory->getPath() . DIRECTORY_SEPARATOR . $this->name);
    }

    /**
     * Return file content.
     *
     * @param array $extract
     * @return string
     */
    public function read(array $extract = null) : string {
        extract($extract ?? []);

        ob_start();
        include($this->path);
        $data = ob_get_contents(); 
        ob_end_clean();

        return $data;
    }
}

