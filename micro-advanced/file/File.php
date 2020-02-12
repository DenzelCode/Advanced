<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace advanced\file;

use advanced\Bootstrap;

class File implements IFile{

    private $path;
    private $name;
    private $directory;
    private $mode;

    public function __construct(string $file, string $mode = 'w') {
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
        $handle = fopen($this->path, 'w');
        fwrite($handle, $content);
        fclose($handle);
    }

    public function create(string $default = null, int $permission = 0777): void {
        if ($this->exists()) return;

        $this->directory->create($permission);

        if (!$this->exists()) {
            $handle = fopen($this->path, $this->mode);
            $data = $default;
            fwrite($handle, $data);
            fclose($handle);

            $this->setPermission($permission);
        }
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

