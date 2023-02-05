<?php

namespace Lumi;

use DateTime;
use InvalidArgumentException;

class LogFolder {

    private const NEW_FOLDER_PERMISSIONS = 0777;

    /**
     * Path to the folder
     */
    private string $path = '';

    const DATE_FORMAT = 'Y-m-d';

    const DIR_CURRENT = '.';
    const DIR_PARENT = '..';

    /**
     * Init a directory
     *
     * @param string $path Path to folder
     * @throws InvalidArgumentException Folder is invalid
     */
    public function __construct(string $path)
    {
        if (!file_exists($path) && !mkdir($path, self::NEW_FOLDER_PERMISSIONS)) {
            throw new InvalidArgumentException(
                sprintf('Path: %s. Cannot create a folder', $path)
            );
        }

        if (!is_dir($path)) {
            $fileName = Utils::getFileOrFolderName($path);
            throw new InvalidArgumentException(
                sprintf('%s is a file. Not a folder', $fileName)
            );
        }

        if (!is_writable($path)) {
            throw new InvalidArgumentException(
                sprintf('Path: %s. Not have permission for write', $path)
            );
        }

        $this->path = $path;
    }

    /**
     * Return the folder based on today's date.
     *
     * If folder is exist - will returned exist folder,
     * otherwise - will create new folder (if is possible)
     *
     * @return LogFolder Instance of new folder
     */
    public function getFolderByToday():LogFolder
    {
        $currentDate = new DateTime();
        $folderName = $currentDate->format(self::DATE_FORMAT);

        return new self($this->path . '/' . $folderName);
    }


    /**
     * Get list of files in folder
     *
     * @param bool $lastOnly Get only last file in folder
     * @param int $sort (opt.) Sort files in folder SCANDIR_SORT_*
     * @return array|string|null List of files or one file
     * Null - directory is empty
     */
    public function getFiles(bool $lastOnly = false, int $sort = SCANDIR_SORT_ASCENDING): array|string|null
    {
        $files = scandir($this->path, $sort);
        $files = array_diff($files, [self::DIR_CURRENT, self::DIR_PARENT]);

        if (empty($files)) {
            return null;
        }

        if ($lastOnly) {
            return end($files);
        }

        return $files;
    }

    /**
     * Get path to folder
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}