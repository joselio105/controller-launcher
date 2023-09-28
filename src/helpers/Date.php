<?php

namespace Plugse\Ctrl\helpers;

class Date
{
    public const MinutesPerHour = 60;
    public const SecondsPerMinute = 60;

    public static function convertIntToHour(int $hour): string
    {
        $hourValue = floor($hour / self::MinutesPerHour);
        $minuteValue = $hourValue % self::SecondsPerMinute;

        $hourValue = str_pad($hourValue, 2, '0');
        $minuteValue = str_pad($minuteValue, 2, '0');

        return "{$hourValue}:{$minuteValue}:00";
    }

    public static function covertTimeToInt(string $hour): int
    {
        [$hourValue, $minuteValue, $secondValue] = explode(':', $hour);

        $hours = $hourValue * self::MinutesPerHour * self::SecondsPerMinute;
        $minutes = $minuteValue * self::SecondsPerMinute;

        return $hours + $minutes + $secondValue;
    }
}
