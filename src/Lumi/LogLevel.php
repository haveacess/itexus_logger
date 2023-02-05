<?php

enum LogLevel: int
{

    /**
     * Depth of logging level
     */
    case DEBUG = 7;
    case INFO = 6;
    case NOTICE = 5;
    case WARNING = 4;
    case ERROR = 3;
    case CRITICAL = 2;
    case ALERT = 1;
    case EMERGENCY = 0;

    /**
     * Get enum from name
     *
     * @param string $name
     * @return IntBackedEnum|null
     */
    public static function fromName(string $name): LogLevel|null
    {
        foreach (self::cases() as $level) {
            if($name === $level->name){
                return $level;
            }
        }
        return null;
    }

    public static function getCasesAsString(): array
    {
        return array_map(function (LogLevel $case) {
            return $case->name;
        }, self::cases());
    }
}