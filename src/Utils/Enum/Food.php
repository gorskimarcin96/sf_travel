<?php

namespace App\Utils\Enum;

use App\Exception\EnumNotExistsException;

enum Food: string
{
    case ALL_INCLUSIVE = 'all-inclusive';
    case BREAKFAST_LAUNCH_AND_DINNER = 'breakfast, launch and dinner';
    case BREAKFAST_AND_DINNER = 'breakfast and dinner';
    case BREAKFAST = 'breakfast';
    case WITHOUT_FOOD = 'without food';

    public static function fromValue(string $value): Food
    {
        return match (strtolower(trim($value))) {
            'all inclusive' => self::ALL_INCLUSIVE,
            'trzy posiłki' => self::BREAKFAST_LAUNCH_AND_DINNER,
            'śniadania i obiadokolacje' => self::BREAKFAST_AND_DINNER,
            'śniadania' => self::BREAKFAST,
            'według programu', 'własne', 'bez wyżywienia', 'zgodne z programem"' => self::WITHOUT_FOOD,
            default => throw new EnumNotExistsException($value)
        };
    }
}
