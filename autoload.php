<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soul)
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