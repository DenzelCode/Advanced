<?php

namespace advanced\file;

class Directory implements IFile {

    /**
     * @var string
    */
    private $name;

    /**
     * @var string
    */
    private $path;

    /**
     * Create directory object.
     *
     * @param string $directory
     */
    public function __construct(string $directory) {
        $this->path = $directory;

        $this->path = str_replace("\\", DIRECTORY_SEPARATOR, $this->path);
        $this->path = str_replace("/", DIRECTORY_SEPARATOR, $this->path);

        $directories = explode(DIRECTORY_SEPARATOR, $this->path);

        $this->name = end($directories);
    }

    /**
     * Get directory name.
     *
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * Set directory name.
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void {
        rename($this->path, dirname($this->path) . DIRECTORY_SEPARATOR . $name);

        $this->name = $name;
    }

    /**
     * Change directory path.
     *
     * @param string $path
     * @return void
     */
    public function setPath(string $path): void {
        rename($this->path, $path);

        $this->path = $path;

        $this->name = end(explode(DIRECTORY_SEPARATOR, $this->path));
    }

    /**
     * Get directory path.
     *
     * @return string
     */
    public function getPath() : string {
        return $this->path;
    }

    /**
     * Get directory permissions.
     *
     * @return integer
     */
    public function getPermission(): int {
        return fileperms($this->path);
    }

    /**
     * Set directory permissions.
     *
     * @param integer $permission
     * @return void
     */
    public function setPermission(int $permission): void {
        chmod($this->path, $permission);
    }

    /**
     * Check if directory exists.
     *
     * @return boolean
     */
    public function exists(): bool {
        return is_dir($this->path);
    }

    /**
     * Create direcrtory if not exists.
     *
     * @param integer $permission
     * @return void
     */
    public function create(int $permission = 0777): void {
        if ($this->exists()) return;

        $directories = explode(DIRECTORY_SEPARATOR, $this->path);

        $str = "";

        foreach ($directories as $key => $dir) {
            if (empty($dir)) continue;

            $str .= (empty($str) && \strpos($dir, ":") ? $dir : DIRECTORY_SEPARATOR . $dir);

            if (!file_exists($str)) @mkdir($str, $permission);
        }
    }
    
    /**
     * Delete directory.
     *
     * @return boolean
     */
    public function delete(): bool {
        $class_func = array(__CLASS__, __FUNCTION__);
        
        return is_file($this->path) ? @unlink($this->path) : array_map($class_func, glob($this->path.'/*')) == @rmdir($this->path);
    }
}