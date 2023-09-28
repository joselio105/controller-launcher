<?php

namespace Plugse\Ctrl\helpers;

class StringServices
{
    public static function joinPrefix(string $key, string $prefix): string
    {
        return strlen($prefix) === 0 ? $key : "{$prefix}_{$key}";
    }

    public static function removePrefix(string $word, string $prefix): string
    {
        $fieldWithoutPrefix = str_replace("{$prefix}_", '', $word);

        return $fieldWithoutPrefix;
    }

    public static function getValue(string $field, string $prefix, array $data)
    {
        $fieldName = self::joinPrefix($field, $prefix);
        if (
            !key_exists($fieldName, $data) or
            strlen($data[$fieldName]) === 0
        ) {
            return null;
        }

        return $data[$fieldName];
    }
}
