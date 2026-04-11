<?php

/**
 * This file is part of the Support package.
 *
 * @author Serge Yakovlev <serge.yakovlev@gmail.com>
 * @link https://github.com/sergeyakovlev/support-php
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

declare(strict_types=1);

namespace SergeYakovlev\Support\Str;

final class Str
{
    /**
     * Checks if the haystack string contains the given needle(s).
     *
     * @param string $haystack The string to search in
     * @param string|string[] $needle The substring(s) to search for
     * @return bool True if the needle is found, false otherwise
     */
    public static function contains(string $haystack, string|array $needle): bool
    {
        return is_string($needle)
            ? str_contains($haystack, $needle)
            : array_any($needle, static fn(string $v): bool => str_contains($haystack, $v));
    }

    /**
     * Checks if the haystack string does not contain the given needle(s).
     *
     * @param string $haystack The string to search in
     * @param string|string[] $needle The substring(s) to search for
     * @return bool True if the needle is not found, false otherwise
     */
    public static function doesntContain(string $haystack, string|array $needle): bool
    {
        return is_string($needle)
            ? !str_contains($haystack, $needle)
            : !array_any($needle, static fn(string $v): bool => str_contains($haystack, $v));
    }

    /**
     * Returns the length of the string.
     *
     * @param string $string The input string
     * @return int The length of the string in characters
     */
    public static function length(string $string): int
    {
        return mb_strlen($string);
    }

    /**
     * Checks if the haystack string starts with the given needle(s).
     *
     * @param string $haystack The string to search in
     * @param string|string[] $needle The substring(s) to check at the beginning
     * @return bool True if the haystack starts with the needle, false otherwise
     */
    public static function startsWith(string $haystack, string|array $needle): bool
    {
        return is_string($needle)
            ? str_starts_with($haystack, $needle)
            : array_any($needle, static fn(string $v): bool => str_starts_with($haystack, $v));
    }

    /**
     * Checks if the haystack string does not start with the given needle(s).
     *
     * @param string $haystack The string to search in
     * @param string|string[] $needle The substring(s) to check at the beginning
     * @return bool True if the haystack does not start with the needle, false otherwise
     */
    public static function doesntStartWith(string $haystack, string|array $needle): bool
    {
        return is_string($needle)
            ? !str_starts_with($haystack, $needle)
            : !array_any($needle, static fn(string $v): bool => str_starts_with($haystack, $v));
    }

    /**
     * Checks if the haystack string ends with the given needle(s).
     *
     * @param string $haystack The string to search in
     * @param string|string[] $needle The substring(s) to check at the end
     * @return bool True if the haystack ends with the needle, false otherwise
     */
    public static function endsWith(string $haystack, string|array $needle): bool
    {
        return is_string($needle)
            ? str_ends_with($haystack, $needle)
            : array_any($needle, static fn(string $v): bool => str_ends_with($haystack, $v));
    }

    /**
     * Checks if the haystack string does not end with the given needle(s).
     *
     * @param string $haystack The string to search in
     * @param string|string[] $needle The substring(s) to check at the end
     * @return bool True if the haystack does not end with the needle, false otherwise
     */
    public static function doesntEndWith(string $haystack, string|array $needle): bool
    {
        return is_string($needle)
            ? !str_ends_with($haystack, $needle)
            : !array_any($needle, static fn(string $v): bool => str_ends_with($haystack, $v));
    }

    /**
     * Prepends a string to the beginning of another string.
     *
     * @param string $string The original string
     * @param string $prependString The string to prepend
     * @return string The resulting string with the prepended value
     */
    public static function prepend(string $string, string $prependString): string
    {
        return match (true) {
            $string === '' => $prependString,
            $prependString === '' => $string,
            default => $prependString . $string,
        };
    }

    /**
     * Appends a string to the end of another string.
     *
     * @param string $string The original string
     * @param string $appendString The string to append
     * @return string The resulting string with the appended value
     */
    public static function append(string $string, string $appendString): string
    {
        return match (true) {
            $string === '' => $appendString,
            $appendString === '' => $string,
            default => $string . $appendString,
        };
    }

    /**
     * Adds a string to the beginning if the string does not already start with it.
     *
     * @param string $string The original string
     * @param string $startString The string to add at the beginning
     * @return string The resulting string that starts with the given string
     */
    public static function start(string $string, string $startString): string
    {
        return str_starts_with($string, $startString)
            ? $string
            : $startString . $string;
    }

    /**
     * Adds a string to the end if the string does not already end with it.
     *
     * @param string $string The original string
     * @param string $endString The string to add at the end
     * @return string The resulting string that ends with the given string
     */
    public static function end(string $string, string $endString): string
    {
        return str_ends_with($string, $endString)
            ? $string
            : $string . $endString;
    }

    /**
     * Converts the string to uppercase.
     *
     * @param string $string The input string
     * @return string The uppercase string
     */
    public static function upper(string $string): string
    {
        return mb_strtoupper($string);
    }

    /**
     * Converts the string to lowercase.
     *
     * @param string $string The input string
     * @return string The lowercase string
     */
    public static function lower(string $string): string
    {
        return mb_strtolower($string);
    }

    /**
     * Creates a new FluentStr instance from the given string.
     *
     * @param string $string The string to wrap in a FluentStr instance
     * @return FluentStr A FluentStr instance for fluent string operations
     */
    public static function of(string $string): FluentStr
    {
        return new FluentStr($string);
    }
}
