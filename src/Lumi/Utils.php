<?php

namespace Lumi;

class Utils {
    const PATTERN_PATH_FILE = "/.+\/(?<filename>.*)/";

    /**
     * Receive filename or folder from path
     *
     * @param string $path Path to file
     * <br>Example: /var/www/1.php
     * @return string Name of file
     * <br>Example: 1.php
     */
    public static function getFileOrFolderName(string $path): string
    {
        preg_match(self::PATTERN_PATH_FILE, $path, $matches);
        return $matches['filename'];
    }
}