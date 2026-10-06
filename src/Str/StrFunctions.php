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

const UTF8_BOM = "\xEF\xBB\xBF"; // UNICODE: "\u{FEFF}"

/**
 * Returns the flag emoji of a country by its ISO 3166-1 alpha-2 code.
 *
 * The code must be two Latin letters in the same case: "RU" or "ru". Any other value ("Ru", "RUS", " RU", "R1", "РУ")
 * is invalid. The code is not checked against the list of existing countries: any two letters give a flag sequence.
 *
 * @param string $countryIsoAlpha2 The ISO 3166-1 alpha-2 country code.
 * @return string The flag, or an empty string if the code is invalid.
 */
function str_emoji_country_flag(string $countryIsoAlpha2): string
{
    if (!preg_match('/^(?:[A-Z]{2}|[a-z]{2})$/D', $countryIsoAlpha2)) {
        return '';
    }

    // The flag is two regional indicator symbols, which start at U+1F1E6 (the letter "A")
    $regionalIndicatorA = 0x1F1E6;

    $emoji = '';
    foreach (str_split(strtoupper($countryIsoAlpha2)) as $letter) {
        $emoji .= mb_chr($regionalIndicatorA + ord($letter[0]) - ord('A'));
    }

    return $emoji;
}

/**
 * Returns the given string as is, or null if it is null or an empty string.
 *
 * @param string|null $string The string to check.
 * @return non-empty-string|null A non-empty string, or null if the input
 *                               was null or an empty string.
 */
function str_empty_to_null(?string $string): ?string
{
    return $string !== '' ? $string : null;
}

/**
 * Returns the string that starts with the given prefix: the prefix is added only if it is missing.
 *
 * The prefix is compared as a whole byte sequence and is case-sensitive. An empty prefix changes nothing.
 * If $collapse is true, the repeated prefixes at the start are replaced with a single one ("//a" -> "/a").
 * If $collapse is false, a string that already starts with the prefix is returned as is ("//a" -> "//a").
 * A null string stays null: the prefix is not added to it.
 *
 * @param string|null $string The string to check.
 * @param string $prefix The prefix the result must start with.
 * @param bool $collapse Whether to collapse the repeated prefixes at the start into one.
 * @return ($string is null ? null : string) The string that starts with the prefix,
 *                                           or null if the input was null.
 */
function str_ensure_starts_with(?string $string, string $prefix, bool $collapse = false): ?string
{
    if ($string === null || $prefix === '') {
        return $string;
    }

    if (!$collapse) {
        return str_starts_with($string, $prefix) ? $string : $prefix . $string;
    }

    $prefixLength = strlen($prefix);
    $offset = 0;
    while (substr_compare($string, $prefix, $offset, $prefixLength) === 0) {
        $offset += $prefixLength;
    }

    return $prefix . substr($string, $offset);
}

/**
 * Returns the string that ends with the given suffix: the suffix is added only if it is missing.
 *
 * The suffix is compared as a whole byte sequence and is case-sensitive. An empty suffix changes nothing.
 * If $collapse is true, the repeated suffixes at the end are replaced with a single one ("a//" -> "a/").
 * If $collapse is false, a string that already ends with the suffix is returned as is ("a//" -> "a//").
 * A null string stays null: the suffix is not added to it.
 *
 * @param string|null $string The string to check.
 * @param string $suffix The suffix the result must end with.
 * @param bool $collapse Whether to collapse the repeated suffixes at the end into one.
 * @return ($string is null ? null : string) The string that ends with the suffix,
 *                                           or null if the input was null.
 */
function str_ensure_ends_with(?string $string, string $suffix, bool $collapse = false): ?string
{
    if ($string === null || $suffix === '') {
        return $string;
    }

    if (!$collapse) {
        return str_ends_with($string, $suffix) ? $string : $string . $suffix;
    }

    $suffixLength = strlen($suffix);
    $end = strlen($string);
    while ($suffixLength <= $end && substr_compare($string, $suffix, $end - $suffixLength, $suffixLength) === 0) {
        $end -= $suffixLength;
    }

    return substr($string, 0, $end) . $suffix;
}

/**
 * Replaces CRLF ("\r\n") and CR ("\r") line breaks with LF ("\n").
 *
 * Only these three line break variants are recognized. Other characters that look like
 * line breaks (VT, FF, NEL, U+2028, U+2029) are left untouched.
 *
 * @param string|null $string The string to normalize.
 * @return ($string is null ? null : string) The string in which every line break is "\n",
 *                                           or null if the input was null.
 */
function str_normalize_newlines(?string $string): ?string
{
    return $string === null ? null : str_replace(["\r\n", "\r"], "\n", $string);
}
