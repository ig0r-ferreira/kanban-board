<?php

namespace App\Enums;

enum TaskPriority: string
{
    case LOWEST = 'Lowest';
    case LOW = 'Low';
    case MEDIUM = 'Medium';
    case HIGH = 'High';
    case HIGHEST = 'Highest';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
