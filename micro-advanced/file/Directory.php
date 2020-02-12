<?php

namespace advanced\file;

class Directory implements IFile {

    private $name;
    private $path;

    public function __construct(string $directory) {
        $this->path = $directory;

        $this->path = str_replace("\\", DIRECTORY_SEPARATOR, $this->path);
        $this->path = str_replace("/", DIRECTORY_SEPARATOR, $this->path);

        $directories = explode(DIRECTORY_SEPARATOR, $this->path);

        $this->name = end($directories);
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
        return is_dir($this->path);
    }

    public function create(int $permission = 0777): void {
        if ($this->exists()) return;

        $directories = explode(DIRECTORY_SEPARATOR, $this->path);

        $str = "";

        foreach ($directories as $key => $dir) {
            if (empty($dir)) continue;

            $str .= (empty($str) && \strpos($dir, ":") ? $dir : DIRECTORY_SEPARATOR . $dir);

            if (!file_exists($str)) mkdir($str, $permission);
        }
    }
}