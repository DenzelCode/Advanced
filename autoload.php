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

use advanced\exceptions\FileException;

class autoload {

    /**
     * An associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     *
     * @var array
     */
    protected $prefixes = [];

    /**
     * Register loader with SPL autoloader stack.
     *
     * @return void
	 * @throws FileException
     */
    public function register() : void {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * Adds a base directory for a namespace prefix.
     *
     * @param string $prefix The namespace prefix.
     * @param string $base_dir A base directory for class files in the
     * namespace.
     * @param bool $prepend If true, prepend the base directory to the stack
     * instead of appending it; this causes it to be searched first rather
     * than last.
     * @return void
     */
    public function addNamespace(string $prefix, string $base_dir, $prepend = false) : void {
        $prefix = trim($prefix, '\\') . '\\';

        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

        if (isset($this->prefixes[$prefix]) === false) $this->prefixes[$prefix] = [];

        if ($prepend) array_unshift($this->prefixes[$prefix], $base_dir); else array_push($this->prefixes[$prefix], $base_dir);
    }

    /**
     * Loads the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     * @return string|null The mapped file name on success, or boolean false on
     * failure.
     */
    public function loadClass($class) : ?string {
        $prefix = $class;

        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);

            $relative_class = substr($class, $pos + 1);

			$mapped_file = $this->loadMappedFile($prefix, $relative_class);
			
			if ($mapped_file) return $mapped_file;
			
            $prefix = rtrim($prefix, '\\');
        }

        return null;
    }

    /**
     * Load the mapped file for a namespace prefix and relative class.
     *
     * @param string $prefix The namespace prefix.
     * @param string $relative_class The relative class name.
     * @return string|null Null if no mapped file can be loaded, or the
     * name of the mapped file that was loaded.
     */
    protected function loadMappedFile($prefix, $relative_class) {
        if (isset($this->prefixes[$prefix]) === false) return false;

        foreach ($this->prefixes[$prefix] as $base_dir) {
            $file = $base_dir
                  . str_replace('\\', '/', $relative_class)
				  . '.php';
				  
            if ($this->requireFile($file)) return $file;
        }

        return null;
    }

    /**
     * If a file exists, require it from the file system.
     *
     * @param string $file The file to require.
	 * @throws FileException
     */
    protected function requireFile($file) : bool {
        if (file_exists($file)) require_once $file;
		
        throw new FileException(0, "exception.file.not_exist", $file);
    }
}