<?php

namespace Lumi;

use LogLevel;

class LogMessage {

    private string $message = '';
    private array $context = [];

    public function __construct($message, $context=[])
    {
        $this->message = $message;
        $this->context = $context;
    }

    /**
     * Do I need write into file this log message
     *
     * @param LogLevel $currentLevel Current logging level
     *
     * @param LogLevel $msgLevel Message logging level
     * @return bool True - message need to record into log,
     * false - message will skip
     */
    public static function isLoggable(LogLevel $currentLevel, LogLevel $msgLevel): bool
    {
        $currentDepth = $currentLevel->value;
        $messageDepth = $msgLevel->value;

        return $messageDepth <= $currentDepth;
    }

    /**
     * Get message object as text
     *
     * @return string
     */
    public function getText(): string
    {
        $formattedContext = [];
        foreach ($this->context as $key => $value) {
            $formattedContext[":{$key}"] = $value;
        }

        return strtr($this->message, $formattedContext);
    }
}