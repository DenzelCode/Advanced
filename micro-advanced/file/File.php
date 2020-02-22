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

namespace advanced\file;

use advanced\exceptions\FileException;
use Exception;

class File implements IFile{

    private $path;
    private $name;
    private $directory;
    private $mode;

    public function __construct(string $file, string $mode = "w") {
        $this->path = $file;
        $this->path = str_replace("\\", DIRECTORY_SEPARATOR, $this->path);
        $this->path = str_replace("/", DIRECTORY_SEPARATOR, $this->path);
        
        $directories = explode(DIRECTORY_SEPARATOR, $this->path);

        $this->name = end($directories);

        unset($directories[count($directories) - 1]);

        $this->directory = new Directory(implode(DIRECTORY_SEPARATOR, $directories));

        $this->mode = $mode;
    }

    public function write(string $content) : void {
        $handle = fopen($this->path, "w");

        if (!$handle) throw new FileException(1, "exception.file.open", $this->path);

        if (!fwrite($handle, $content)) throw new FileException(1, "exception.file.write", $this->path);

        fclose($handle);
    }

    public function create(string $default = null, int $permission = 0777): void {
        if ($this->exists()) return;

        $this->directory->create($permission);

        $handle = fopen($this->path, $this->mode);
        $fwrite = fwrite($handle, (string) $default);

        if (!$fwrite) throw new FileException(1, "exception.file.write", $this->path);

        fclose($handle);

        $this->setPermission($permission);
    }

    public function setName(string $name): void {
        rename($this->directory->getPath() . $this->name, $this->directory->getPath() . $name);

        $this->name = $name;
    }

    public function getName() : string {
        return $this->name;
    }

    public function setPath(string $path): void {
        rename($this->path, $path);

        $this->path = $path;

        $this->name = end(explode(DIRECTORY_SEPARATOR, $this->path));
    }

    public function getPath() : string {
        return $this->path;
    }

    public function setPermission(int $permission): void {
        chmod($this->path, $permission);
    }

    public function getPermission(): int {
        return fileperms($this->path);
    }

    public function exists(): bool {
        return file_exists($this->path);
    }

    public function read(array $extract = null) : string {
        extract($extract == null ? [] : $extract);

        ob_start();
        include($this->path);
        $data = ob_get_contents(); 
        ob_end_clean();

        return $data;
    }
}

