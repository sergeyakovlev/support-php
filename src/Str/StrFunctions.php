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
 * @param string|null $extLeft The text added before the flag.
 * @param string|null $extRight The text added after the flag.
 * @return string The flag with the added texts, or an empty string if the code is invalid
 *                (the texts are not added to an invalid code).
 */
function str_emoji_country_flag(string $countryIsoAlpha2, ?string $extLeft = null, ?string $extRight = null): string
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

    return ($extLeft ?? '') . $emoji . ($extRight ?? '');
}

/**
 * Returns the given string as is, or null if it is null or an empty string.
 *
 * @param string|null $string The string to check.
 * @return non-empty-string|null A non-empty string, or null if the input
 *                               was null or an empty string.
 */
function str_non_empty(?string $string): ?string
{
    return $string !== '' ? $string : null;
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
