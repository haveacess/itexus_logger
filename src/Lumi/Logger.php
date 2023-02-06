<?php

namespace Lumi;

use InvalidArgumentException;

class Logger {

    /**
     * Path to folder for save logs
     */
    private LogFolder $pathFolder;

    /**
     * Current set logging level
     */
    private LogLevel $currentLevel;

    /**
     * Limit of rows in file
     */
    private int $maxFileRows;


    /**
     * Create new instance of Logger
     *
     * @param Settings $settings Init settings for logger
     */
    public function __construct(Settings $settings)
    {
        $this->pathFolder = new LogFolder($settings->pathFolder);
        $this->currentLevel = self::getCurrentLevel($settings->currentLevel);
        $this->maxFileRows = $settings->maxFileRows;
    }

    /**
     * Get current level for keep logs
     *
     * @param string $currentLevel Init log level for store logs
     * @return LogLevel Level for store logs
     */
    private static function getCurrentLevel(string $currentLevel): LogLevel
    {
        if (!$logLevel = LogLevel::fromName($currentLevel)) {
            $errMessage = implode(PHP_EOL, [
                'Level: %s is unsupported.',
                'Please follow the list: %s'
            ]);

            throw new InvalidArgumentException(
                vsprintf($errMessage, [$currentLevel, implode(',', LogLevel::getCasesAsString())])
            );
        }

        return $logLevel;
    }

    /**
     * Return log instance for static called
     *
     * @return Logger
     */
    private static function getInstance(): Logger
    {
        $settings = new Settings();

        $settings->currentLevel = getenv('LUMI_LOG_LEVEL');
        $settings->pathFolder = getenv('LUMI_LOG_FOLDER');
        $settings->maxFileRows = (int)getenv('LUMI_MAX_FILE_ROWS');

        return (new self($settings));
    }

    private static function addRecord($msgLevel, $message, $context = [])
    {
        $instance = self::getInstance();

        if (LogMessage::isLoggable($instance->currentLevel, $msgLevel)) {
            $parentLogFolder = new LogFolder(($instance->pathFolder)->getPath());
            $logFolder = $parentLogFolder->getFolderByToday();
            $logFile = LogFile::getActualFile($logFolder, $instance->maxFileRows);

            $logFile->append($msgLevel, new LogMessage($message, $context));
        }
    }

    public static function debug($message, $context = [])
    {
        self::addRecord(LogLevel::DEBUG, $message, $context);
    }

    public static function info($message, $context = [])
    {
        self::addRecord(LogLevel::INFO, $message, $context);
    }

    public static function notice($message, $context = [])
    {
        self::addRecord(LogLevel::NOTICE, $message, $context);
    }

    public static function warning($message, $context = [])
    {
        self::addRecord(LogLevel::WARNING, $message, $context);
    }

    public static function error($message, $context = [])
    {
        self::addRecord(LogLevel::ERROR, $message, $context);
    }

    public static function critical($message, $context = [])
    {
        self::addRecord(LogLevel::CRITICAL, $message, $context);
    }

    public static function alert($message, $context = [])
    {
        self::addRecord(LogLevel::ALERT, $message, $context);
    }

    public static function emergency($message, $context = [])
    {
        self::addRecord(LogLevel::EMERGENCY, $message, $context);
    }
}