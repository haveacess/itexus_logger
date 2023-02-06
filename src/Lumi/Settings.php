<?php

namespace Lumi;

class Settings {
    /**
     * Directory for logging files
     *
     * *Example: /var/log/lumi
     */
    public string $pathFolder;

    /**
     * Level for create logs
     */
    public string $currentLevel;

    /**
     * Max lines of the file
     */
    public int $maxFileRows = 500;
}