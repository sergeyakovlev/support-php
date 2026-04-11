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

final class NumberFormatter implements NumberFormatterInterface
{
    /**
     * Formats a float number with digit grouping and optional sign prefixes
     *
     * This method formats a float number by adding thousands separators and controlling decimal places. It also allows
     * adding custom sign prefixes for positive and negative numbers, with support for Unicode minus sign.
     *
     * @param float $value The float value to format
     * @param int $decimals The number of decimal digits to display
     * @param string $decimalsSeparator The character used as decimal separator (default: ',')
     * @param string $thousandsSeparator The character used as thousands separator (default: '&nbsp;')
     * @param string $signPlus The prefix for positive numbers (default: '')
     * @param string $signMinus The prefix for negative numbers (default: '&minus;')
     * @return string The formatted number string
     *
     * @example
     *   NumberFormatter::formatFloat(1234.56, 2) // returns "1&nbsp;234,56"
     *   NumberFormatter::formatFloat(-1234.56, 2, '.', ',', '', '-') // returns "-1,234.56"
     *   NumberFormatter::formatFloat(1234.56, 0, '.', ' ', '+', '-') // returns "+1 235"
     */
    public function formatFloat(
        float $value,
        int $decimals,
        string $decimalsSeparator = ',',
        string $thousandsSeparator = '&nbsp;',
        string $signPlus = '',
        string $signMinus = '&minus;',
    ): string {
        $stringValue = number_format(abs($value), $decimals, $decimalsSeparator, $thousandsSeparator);

        return match (true) {
            $value > 0 && $signPlus !== '' => $signPlus . $stringValue,
            $value < 0 && $signMinus !== '' => $signMinus . $stringValue,
            default => $stringValue,
        };
    }

    /**
     * Formats an integer with thousands separators and optional sign prefixes
     *
     * This method formats an integer by adding thousands separators at every three digits from the right.
     * It also allows adding custom sign prefixes for positive and negative numbers.
     *
     * @param int $value The integer value to format
     * @param string $thousandsSeparator The character used as thousands separator (default: '&nbsp;')
     * @param string $signPlus The prefix for positive numbers (default: '')
     * @param string $signMinus The prefix for negative numbers (default: '&minus;')
     * @return string The formatted number string
     *
     * @see https://sergeyakovlev.com/blog/php-int-format
     *
     * @example
     *   Number::formatInt(1234567) // returns "1&nbsp;234&nbsp;567"
     *   Number::formatInt(-1234567) // returns "&minus;1&nbsp;234&nbsp;567"
     *   Number::formatInt(123, ',', '+', '-') // returns "+123"
     */
    public function formatInt(
        int $value,
        string $thousandsSeparator = '&nbsp;',
        string $signPlus = '',
        string $signMinus = '&minus;',
    ): string {
        $stringValue = (string) abs($value);

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
     * This method implements plural rules for Russian and other Slavic languages using the standard CLDR pluralization
     * rules. It selects the correct plural form based on the number's remainder when divided by 100 and by 10.
     *
     * @param int $value The number to determine the plural form for
     * @param string[] $forms An array of plural forms in the order: [plural, singular, paucal]
     *   - $forms[0] - plural form (for 0, 5-20, 25-30, etc.)
     *   - $forms[1] - singular form (for 1, 21, 31, etc.)
     *   - $forms[2] - paucal form (for 2-4, 22-24, 32-34, etc.)
     * @return string The appropriate plural form string
     * @throws NumberException
     *
     * @see https://docs.translatehouse.org/projects/localization-guide/en/latest/l10n/pluralforms.html
     *
     * @example
     *   Number::pluralThings(1, ['яблок', 'яблоко', 'яблока']) // returns "яблоко"
     *   Number::pluralThings(2, ['яблок', 'яблоко', 'яблока']) // returns "яблока"
     *   Number::pluralThings(5, ['яблок', 'яблоко', 'яблока']) // returns "яблок"
     *   Number::pluralThings(11, ['яблок', 'яблоко', 'яблока']) // returns "яблок"
     *   Number::pluralThings(21, ['яблок', 'яблоко', 'яблока']) // returns "яблоко"
     */
    public function ruPluralThings(int $value, array $forms): string
    {
        if ($value < 0) {
            throw new NumberException('Number must be non-negative for Russian plural forms.');
        }

        $remainsOfDivBy100 = $value % 100;

        return match ($remainsOfDivBy100 <= 20 ? $remainsOfDivBy100 : $remainsOfDivBy100 % 10) {
            1 => $forms[1],
            2, 3, 4 => $forms[2],
            default => $forms[0],
        };
    }
}
