<?php
/**
 * Advanced microFramework
 * -
 * @copyright Copyright (c) 2019 Advanced microFramework
 * @author    Advanced microFramework Team (Denzel Code, Soull Darknezz)
 */

namespace advanced\utils;

use advanced\Bootstrap;

class File{

    public static function write(string $file, string $content) : void {
        $handle = fopen($file, 'w');
        fwrite($handle, $content);
        fclose($handle);
    }

    public static function check(string $file, string $default = null): void {
        $directory = str_replace("\\", DIRECTORY_SEPARATOR, $file);
        $directory = str_replace("/", DIRECTORY_SEPARATOR, $file);

        $directories = explode(DIRECTORY_SEPARATOR, $directory);

        $file = $directories[count($directories) - 1];

        unset($directories[count($directories) - 1]);

        self::checkDirectory(($str = implode(DIRECTORY_SEPARATOR, $directories)));

        if (!file_exists($str . DIRECTORY_SEPARATOR . $file)) {
            $handle = fopen($str . DIRECTORY_SEPARATOR . $file, 'w');
            $data = $default;
            fwrite($handle, $data);
            fclose($handle);
        }
    }

    public static function checkDirectory(string $directory): void {
        $directory = str_replace("\\", DIRECTORY_SEPARATOR, $directory);
        $directory = str_replace("/", DIRECTORY_SEPARATOR, $directory);

        $directories = explode(DIRECTORY_SEPARATOR, $directory);

        if (empty($directories)) return;

        $str = "";

        foreach ($directories as $key => $dir) {
            if (empty($dir)) continue;

            $str .= (empty($str) && \strpos($dir, ":") ? $dir : DIRECTORY_SEPARATOR . $dir);

            if (!file_exists($str)) mkdir($str, 0777);
        }
    }
}

