<?php

namespace Lumi;


use DateTime;
use InvalidArgumentException;
use LogLevel;

class LogFile {

    /**
     * Path to the file
     */
    private string $path = '';

    const PATTERN_LOG_NAME = '/(?<increment>\d+)\s(?<created_at>\d{2}:\d{2}:\d{2})/';
    const NEW_FILE_EXTENSION = 'log';
    const INCREMENT_START_INDEX = 1;
    const TIME_FORMAT = 'h:i:s';

    /**
     * Init a file
     *
     * @param $path
     */
    public function __construct($path)
    {
        $logContext = [$path, Utils::getFileOrFolderName($path)];

        if (!file_exists($path)) {
            throw new InvalidArgumentException(
                vsprintf('Path: %s. File %s is not exist', $logContext)
            );
        }

        if (is_dir($path)) {
            throw new InvalidArgumentException(
                vsprintf('Path: %s. %s is a directory, not a file', $logContext)
            );
        }

        if (!is_writable($path)) {
            throw new InvalidArgumentException(
                vsprintf('Path: %s. File %s is not writable', $logContext)
            );
        }

        $this->path = $path;
    }


    /**
     * Returning exist (or new) file for write logs.
     *
     * @param LogFolder $logFolder
     * @param int $maxFileRows
     * @return LogFile Will return existing file when his not filled yet,
     * Otherwise - new file will be created and returned
     */
    public static function getActualFile(LogFolder $logFolder, int $maxFileRows): LogFile
    {
        $lastFileName = $logFolder->getFiles(true);
        $isEmptyFolder = is_null($lastFileName);

        if (!$isEmptyFolder) {
            $existFile = new self($logFolder->getPath() . '/' . $lastFileName);

            if (!$existFile->isFilled($maxFileRows)) {
                return $existFile;
            }
        }

        $payload = $isEmptyFolder ? [] : self::getPayloadFromName($lastFileName);
        return self::createEmptyFile($logFolder, self::getNewFileName($payload));
    }

    /**
     * Get count rows in file
     *
     * @return int
     */
    private function getCountRows():int
    {
        $file = fopen($this->path, 'rb');
        $lines = 0;

        while (!feof($file)) {
            $lines += substr_count(fread($file, 8192), "\n");
        }

        fclose($file);

        return $lines;
    }

    /**
     * File is filled or you still can write into file
     *
     * @param int $maxFileRows Limit rows in file
     * @return bool True - file is filled, false - file still ready to write
     */
    private function isFilled(int $maxFileRows):bool {
        return $this->getCountRows() >= $maxFileRows;
    }


    /**
     * Return name for new creating file
     *
     * @param array $payload
     * @return string
     */
    private static function getNewFileName(array $payload): string
    {
        $currentTime = new DateTime();
        $payload = [
            'increment' => isset($payload['increment'])
                ? ++$payload['increment']
                : self::INCREMENT_START_INDEX,
            'created_at' => $currentTime->format(self::TIME_FORMAT),
        ];

        return implode(' ', $payload);
    }


    /**
     * Append new log message into file
     *
     * @param LogLevel $logLevel Log level of the message
     * @param LogMessage $message Message for log
     * @return void
     */
    public function append(LogLevel $logLevel, LogMessage $message)
    {
        file_put_contents($this->path, vsprintf('%s. %s' . PHP_EOL, [
            $logLevel->name,
            $message->getText()
        ]), FILE_APPEND);
    }

    /**
     * Get file info from their name
     *
     * @param string $filename
     * @return array
     */
    private static function getPayloadFromName(string $filename): array
    {
        preg_match(self::PATTERN_LOG_NAME, $filename, $matches);
        return array_diff($matches, ['increment', 'created_at']);
    }

    /**
     * Create new empty file
     *
     * @param LogFolder $logFolder Folder, when need create file
     * @param string $filename Filename for new file
     * @return LogFile New created file
     */
    private static function createEmptyFile(LogFolder $logFolder, string $filename):LogFile
    {
        $filePath = $logFolder->getPath() . '/' . $filename . '.' . self::NEW_FILE_EXTENSION;
        file_put_contents($filePath, '');
        return new self($filePath);
    }
}