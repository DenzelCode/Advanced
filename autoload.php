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

define('ADVANCED', __DIR__ . DIRECTORY_SEPARATOR . 'micro-advanced' . DIRECTORY_SEPARATOR);

spl_autoload_register(function ($class) {
	$path = __DIR__ . (explode('\\', $class)[0] == 'advanced' ? '/micro-advanced/' : DIRECTORY_SEPARATOR) . str_replace('\\', '/', str_replace('advanced', '', $class)) . '.php';
	
	try {
		if(!file_exists($path)){
			throw new Exception("File {$class} does not exists.");
		}else{
			require($path);
		}
	} catch (Exception $e) {
		echo $e->getMessage();
	}
});