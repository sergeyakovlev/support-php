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

namespace SergeYakovlev\Support\Number;

const HTML_MINUS = '&minus;';

const HTML_NBSP = '&nbsp;';

const UTF8_MINUS = "\xE2\x88\x92"; // UNICODE: "\u{2212}"

const UTF8_NBSP = "\xC2\xA0"; // UNICODE: "\u{00A0}"

/**
 * Formats a float number for HTML.
 *
 * It is the number_float_format() function with the defaults suited for HTML: the digits are grouped by the "&nbsp;"
 * entity (a no-break space, HTML_NBSP) and the minus sign is the "&minus;" entity (HTML_MINUS). Everything else is
 * the same.
 *
 * @param float $value The float value to format
 * @param int $decimals The number of decimal digits to display
 * @param string $decimalsSeparator The character used as decimal separator (default: ',')
 * @param string $thousandsSeparator The character used as thousands separator (default: HTML_NBSP, which is '&nbsp;')
 * @param string $signPlus The prefix for positive numbers (default: '')
 * @param string $signMinus The prefix for negative numbers (default: HTML_MINUS, which is '&minus;')
 * @return string The formatted number string
 * @throws NumberException If the value is not a finite number (NAN or infinity)
 *
 * @example
 *   html_float_format(1234.56, 2) // returns "1&nbsp;234,56"
 *   html_float_format(-1234.56, 2) // returns "&minus;1&nbsp;234,56"
 *   html_float_format(1234.56, 2, '.', ',', '+') // returns "+1,234.56"
 */
function html_float_format(
    float $value,
    int $decimals,
    string $decimalsSeparator = ',',
    string $thousandsSeparator = HTML_NBSP,
    string $signPlus = '',
    string $signMinus = HTML_MINUS,
): string {
    return number_float_format($value, $decimals, $decimalsSeparator, $thousandsSeparator, $signPlus, $signMinus);
}

/**
 * Formats an integer for HTML.
 *
 * It is the number_int_format() function with the defaults suited for HTML: the digits are grouped by the "&nbsp;"
 * entity (a no-break space, HTML_NBSP) and the minus sign is the "&minus;" entity (HTML_MINUS). Everything else is
 * the same.
 *
 * @param int $value The integer value to format
 * @param string $thousandsSeparator The character used as thousands separator (default: HTML_NBSP, which is '&nbsp;')
 * @param string $signPlus The prefix for positive numbers (default: '')
 * @param string $signMinus The prefix for negative numbers (default: HTML_MINUS, which is '&minus;')
 * @return string The formatted number string
 *
 * @example
 *   html_int_format(1234567) // returns "1&nbsp;234&nbsp;567"
 *   html_int_format(-1234567) // returns "&minus;1&nbsp;234&nbsp;567"
 *   html_int_format(1234567, ',', '+') // returns "+1,234,567"
 */
function html_int_format(
    int $value,
    string $thousandsSeparator = HTML_NBSP,
    string $signPlus = '',
    string $signMinus = HTML_MINUS,
): string {
    return number_int_format($value, $thousandsSeparator, $signPlus, $signMinus);
}

/**
 * Formats a float number with digit grouping and optional sign prefixes
 *
 * This function formats a float number by adding thousands separators and controlling decimal places. It also allows
 * adding custom sign prefixes for positive and negative numbers.
 *
 * By default, the result is plain text: the digits are not grouped (the thousands separator is empty), the decimal
 * separator is a comma and the minus sign is the hyphen-minus ("-"). Use html_float_format() for the defaults suited
 * for HTML ("&nbsp;" and "&minus;").
 *
 * The sign follows the number that is shown, not the original value. A number that is rounded to zero gets no sign:
 * -0.001 with 2 decimals is "0,00", not "-0,00", and 0.001 with the plus sign is "0,00", not "+0,00".
 *
 * @param float $value The float value to format
 * @param int $decimals The number of decimal digits to display
 * @param string $decimalsSeparator The character used as decimal separator (default: ',')
 * @param string $thousandsSeparator The character used as thousands separator (default: '', the digits are not grouped)
 * @param string $signPlus The prefix for positive numbers (default: '')
 * @param string $signMinus The prefix for negative numbers (default: '-')
 * @return string The formatted number string
 * @throws NumberException If the value is not a finite number (NAN or infinity)
 *
 * @example
 *   number_float_format(1234.56, 2) // returns "1234,56"
 *   number_float_format(-1234.56, 2) // returns "-1234,56"
 *   number_float_format(1234567.891, 2, '.', ',') // returns "1,234,567.89"
 *   number_float_format(1234.56, 0, '.', ' ', '+') // returns "+1 235"
 *   number_float_format(-0.001, 2) // returns "0,00"
 */
function number_float_format(
    float $value,
    int $decimals,
    string $decimalsSeparator = ',',
    string $thousandsSeparator = '',
    string $signPlus = '',
    string $signMinus = '-',
): string {
    if (!is_finite($value)) {
        throw new NumberException('Value must be a finite number.');
    }

    $stringValue = number_format(abs($value), $decimals, $decimalsSeparator, $thousandsSeparator);

    // number_format() rounds the same way, so the sign is the sign of the number that is actually shown
    $roundedValue = round($value, $decimals);

    return match (true) {
        $roundedValue > 0 && $signPlus !== '' => $signPlus . $stringValue,
        $roundedValue < 0 && $signMinus !== '' => $signMinus . $stringValue,
        default => $stringValue,
    };
}

/**
 * Formats an integer with thousands separators and optional sign prefixes
 *
 * This function formats an integer by adding thousands separators at every three digits from the right.
 * It also allows adding custom sign prefixes for positive and negative numbers.
 *
 * By default, the result is plain text: the digits are not grouped (the thousands separator is empty) and the minus
 * sign is the hyphen-minus ("-"). Use html_int_format() for the defaults suited for HTML ("&nbsp;" and "&minus;").
 *
 * @param int $value The integer value to format
 * @param string $thousandsSeparator The character used as thousands separator (default: '', the digits are not grouped)
 * @param string $signPlus The prefix for positive numbers (default: '')
 * @param string $signMinus The prefix for negative numbers (default: '-')
 * @return string The formatted number string
 *
 * @see https://sergeyakovlev.com/blog/php-int-format
 *
 * @example
 *   number_int_format(1234567) // returns "1234567"
 *   number_int_format(-1234567) // returns "-1234567"
 *   number_int_format(1234567, ' ') // returns "1 234 567"
 *   number_int_format(123, ',', '+') // returns "+123"
 */
function number_int_format(
    int $value,
    string $thousandsSeparator = '',
    string $signPlus = '',
    string $signMinus = '-',
): string {
    // Not abs(): abs(PHP_INT_MIN) is a float
    $stringValue = $value < 0 ? substr((string) $value, 1) : (string) $value;

    for ($i = strlen($stringValue) - 3; $i > 0; $i -= 3) {
        $stringValue = substr_replace($stringValue, $thousandsSeparator, $i, 0);
    }

    return match (true) {
        $value > 0 && $signPlus !== '' => $signPlus . $stringValue,
        $value < 0 && $signMinus !== '' => $signMinus . $stringValue,
        default => $stringValue,
    };
}

/**
 * Returns the appropriate plural form based on the number and language rules
 *
 * This function implements plural rules for Russian and other Slavic languages using the standard CLDR pluralization
 * rules. It selects the correct plural form based on the number's remainder when divided by 100 and by 10.
 *
 * @param int $value The number to determine the plural form for
 * @param array<int, string> $forms An array of plural forms in the order: [plural, singular, paucal]
 *   - $forms[0] - plural form (for 0, 5-20, 25-30, etc.)
 *   - $forms[1] - singular form (for 1, 21, 31, etc.)
 *   - $forms[2] - paucal form (for 2-4, 22-24, 32-34, etc.)
 *   There must be exactly these three elements, with the keys 0, 1 and 2.
 * @return string The appropriate plural form string
 * @throws NumberException If the number is negative or $forms is not exactly the three forms with the keys 0, 1 and 2
 *
 * @see https://docs.translatehouse.org/projects/localization-guide/en/latest/l10n/pluralforms.html
 *
 * @example
 *   number_ru_plural_things(1, ['яблок', 'яблоко', 'яблока']) // returns "яблоко"
 *   number_ru_plural_things(2, ['яблок', 'яблоко', 'яблока']) // returns "яблока"
 *   number_ru_plural_things(5, ['яблок', 'яблоко', 'яблока']) // returns "яблок"
 *   number_ru_plural_things(11, ['яблок', 'яблоко', 'яблока']) // returns "яблок"
 *   number_ru_plural_things(21, ['яблок', 'яблоко', 'яблока']) // returns "яблоко"
 */
function number_ru_plural_things(int $value, array $forms): string
{
    if ($value < 0) {
        throw new NumberException('Number must be non-negative for Russian plural forms.');
    }

    if (count($forms) !== 3 || !isset($forms[0], $forms[1], $forms[2])) {
        throw new NumberException(
            'Exactly three forms (plural, singular and paucal) are required for Russian plural forms.',
        );
    }

    $remainsOfDivBy100 = $value % 100;

    return match ($remainsOfDivBy100 <= 20 ? $remainsOfDivBy100 : $remainsOfDivBy100 % 10) {
        1 => $forms[1],
        2, 3, 4 => $forms[2],
        default => $forms[0],
    };
}
