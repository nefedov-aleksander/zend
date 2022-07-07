<?php
namespace Bpm\Common;

class Str
{
    public static function isEmptyOrWhiteSpace(string $string) : bool
    {
        return empty(preg_replace('/\s+/','',$string));
    }

    public static function isNullOrEmpty(?string $string): bool
    {
        return is_null($string) || empty($string);
    }

    public static function isMatch(string $string, string $pattern): bool
    {
        return (bool) preg_match($pattern, $string);
    }

    public static function isNotMatch(string $string, string $pattern): bool
    {
        return !self::isMatch($string, $pattern);
    }

    public static function replace($string, $search, $replace)
    {
        return str_replace($search, $replace, $string);
    }
}