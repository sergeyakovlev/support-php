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

namespace SergeYakovlev\Support\Tests\Number;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SergeYakovlev\Support\Number\NumberException;

use function SergeYakovlev\Support\Number\html_float_format;
use function SergeYakovlev\Support\Number\html_int_format;
use function SergeYakovlev\Support\Number\number_float_format;
use function SergeYakovlev\Support\Number\number_int_format;
use function SergeYakovlev\Support\Number\number_ru_plural_things;

use const SergeYakovlev\Support\Number\HTML_MINUS;
use const SergeYakovlev\Support\Number\HTML_NBSP;
use const SergeYakovlev\Support\Number\UTF8_MINUS;
use const SergeYakovlev\Support\Number\UTF8_NBSP;

final class NumberFunctionsTest extends TestCase
{
    private const string PLAIN_THOUSANDS_SEPARATOR = '';

    private const string PLAIN_MINUS = '-';

    private const string HTML_NBSP = '&nbsp;';

    private const string HTML_MINUS = '&minus;';

    /**
     * Fills the placeholders of the expected value: {thousands} is the default thousands separator
     * and {minus} is the default minus sign.
     */
    private static function expand(string $template, string $thousands, string $minus): string
    {
        return strtr($template, [
            '{thousands}' => $thousands,
            '{minus}' => $minus,
        ]);
    }

    /**
     * Only the default separators and signs are used here, so the result depends on the function:
     * plain text for number_float_format() (no digit grouping, hyphen-minus) and HTML entities for html_float_format().
     *
     * @return Iterator<array{
     *     params: array<string, float|string>,
     *     expected: string,
     * }>
     */
    public static function dataFloatFormat(): iterable
    {
        yield [
            'params' => [
                'value' => 0.0,
                'decimals' => 0,
            ],
            'expected' => '0',
        ];

        yield [
            'params' => [
                'value' => 1.0,
                'decimals' => 0,
            ],
            'expected' => '1',
        ];

        yield [
            'params' => [
                'value' => 0.0,
                'decimals' => 1,
            ],
            'expected' => '0,0',
        ];

        yield [
            'params' => [
                'value' => 1.0,
                'decimals' => 1,
            ],
            'expected' => '1,0',
        ];

        yield [
            'params' => [
                'value' => 3.1415,
                'decimals' => 2,
            ],
            'expected' => '3,14',
        ];

        yield [
            'params' => [
                'value' => 3.1415,
                'decimals' => 3,
            ],
            'expected' => '3,142',
        ];

        yield [
            'params' => [
                'value' => -3.1415,
                'decimals' => 2,
            ],
            'expected' => '{minus}3,14',
        ];

        yield [
            'params' => [
                'value' => -3.1415,
                'decimals' => 3,
            ],
            'expected' => '{minus}3,142',
        ];

        yield [
            'params' => [
                'value' => 1_234.0,
                'decimals' => 2,
            ],
            'expected' => '1{thousands}234,00',
        ];

        yield [
            'params' => [
                'value' => 1_234_567.0,
                'decimals' => 2,
            ],
            'expected' => '1{thousands}234{thousands}567,00',
        ];

        yield [
            'params' => [
                'value' => -1_234_567.0,
                'decimals' => 0,
            ],
            'expected' => '{minus}1{thousands}234{thousands}567',
        ];

        yield [
            'params' => [
                'value' => -1.0,
                'decimals' => 2,
            ],
            'expected' => '{minus}1,00',
        ];

        // Some of the defaults are replaced
        yield [
            'params' => [
                'value' => 0.0,
                'decimals' => 2,
                'decimalsSeparator' => '.',
            ],
            'expected' => '0.00',
        ];

        yield [
            'params' => [
                'value' => 1.0,
                'decimals' => 2,
                'decimalsSeparator' => '.',
            ],
            'expected' => '1.00',
        ];

        yield [
            'params' => [
                'value' => 1_234.5,
                'decimals' => 1,
                'decimalsSeparator' => '.',
            ],
            'expected' => '1{thousands}234.5',
        ];

        yield [
            'params' => [
                'value' => 1.0,
                'decimals' => 2,
                'signPlus' => '+',
            ],
            'expected' => '+1,00',
        ];

        yield [
            'params' => [
                'value' => -1,
                'decimals' => 2,
                'signMinus' => '-',
            ],
            'expected' => '-1,00',
        ];
    }

    /**
     * @param array<string, float|string> $params
     */
    #[DataProvider('dataFloatFormat')]
    public function testNumberFloatFormat(array $params, string $expected): void
    {
        $actual = number_float_format(...$params);

        self::assertSame(self::expand($expected, self::PLAIN_THOUSANDS_SEPARATOR, self::PLAIN_MINUS), $actual);
    }

    /**
     * @param array<string, float|string> $params
     */
    #[DataProvider('dataFloatFormat')]
    public function testHtmlFloatFormat(array $params, string $expected): void
    {
        $actual = html_float_format(...$params);

        self::assertSame(self::expand($expected, self::HTML_NBSP, self::HTML_MINUS), $actual);
    }

    /**
     * All the arguments are given explicitly, so the result is the same for both functions.
     *
     * @return Iterator<array{
     *     params: array<string, float|int|string>,
     *     expected: string,
     * }>
     */
    public static function dataFloatFormatWithAllArguments(): iterable
    {
        yield [
            'params' => [
                'value' => 1234.56,
                'decimals' => 2,
                'decimalsSeparator' => ',',
                'thousandsSeparator' => ' ',
                'signPlus' => '',
                'signMinus' => '-',
            ],
            'expected' => '1 234,56',
        ];

        yield [
            'params' => [
                'value' => -1234.56,
                'decimals' => 2,
                'decimalsSeparator' => '.',
                'thousandsSeparator' => ',',
                'signPlus' => '',
                'signMinus' => '-',
            ],
            'expected' => '-1,234.56',
        ];

        yield [
            'params' => [
                'value' => 1234.56,
                'decimals' => 0,
                'decimalsSeparator' => '.',
                'thousandsSeparator' => ' ',
                'signPlus' => '+',
                'signMinus' => '-',
            ],
            'expected' => '+1 235',
        ];

        // The no-break space and the minus sign can be asked for explicitly, in both notations
        yield [
            'params' => [
                'value' => -1234.5,
                'decimals' => 1,
                'decimalsSeparator' => ',',
                'thousandsSeparator' => '&nbsp;',
                'signPlus' => '',
                'signMinus' => '&minus;',
            ],
            'expected' => '&minus;1&nbsp;234,5',
        ];

        yield [
            'params' => [
                'value' => -1234.5,
                'decimals' => 1,
                'decimalsSeparator' => ',',
                'thousandsSeparator' => "\u{00A0}",
                'signPlus' => '',
                'signMinus' => "\u{2212}",
            ],
            'expected' => "\u{2212}1\u{00A0}234,5",
        ];
    }

    /**
     * @param array<string, float|int|string> $params
     */
    #[DataProvider('dataFloatFormatWithAllArguments')]
    public function testNumberFloatFormatWithAllArguments(array $params, string $expected): void
    {
        $actual = number_float_format(...$params);

        self::assertSame($expected, $actual);
    }

    /**
     * @param array<string, float|int|string> $params
     */
    #[DataProvider('dataFloatFormatWithAllArguments')]
    public function testHtmlFloatFormatWithAllArguments(array $params, string $expected): void
    {
        $actual = html_float_format(...$params);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{
     *     params: array<string, int|string>,
     *     expected: string,
     * }>
     */
    public static function dataIntFormat(): iterable
    {
        yield [
            'params' => [
                'value' => 0,
            ],
            'expected' => '0',
        ];

        yield [
            'params' => [
                'value' => 1,
            ],
            'expected' => '1',
        ];

        yield [
            'params' => [
                'value' => 123,
            ],
            'expected' => '123',
        ];

        yield [
            'params' => [
                'value' => 1_234,
            ],
            'expected' => '1{thousands}234',
        ];

        yield [
            'params' => [
                'value' => 1_234_567,
            ],
            'expected' => '1{thousands}234{thousands}567',
        ];

        yield [
            'params' => [
                'value' => -1,
            ],
            'expected' => '{minus}1',
        ];

        yield [
            'params' => [
                'value' => -1_234_567,
            ],
            'expected' => '{minus}1{thousands}234{thousands}567',
        ];

        // Some of the defaults are replaced
        yield [
            'params' => [
                'value' => 1,
                'signPlus' => '+',
            ],
            'expected' => '+1',
        ];

        yield [
            'params' => [
                'value' => -1,
                'signMinus' => '-',
            ],
            'expected' => '-1',
        ];

        yield [
            'params' => [
                'value' => 1_234,
                'thousandsSeparator' => ',',
            ],
            'expected' => '1,234',
        ];
    }

    /**
     * @param array<string, int|string> $params
     */
    #[DataProvider('dataIntFormat')]
    public function testNumberIntFormat(array $params, string $expected): void
    {
        $actual = number_int_format(...$params);

        self::assertSame(self::expand($expected, self::PLAIN_THOUSANDS_SEPARATOR, self::PLAIN_MINUS), $actual);
    }

    /**
     * @param array<string, int|string> $params
     */
    #[DataProvider('dataIntFormat')]
    public function testHtmlIntFormat(array $params, string $expected): void
    {
        $actual = html_int_format(...$params);

        self::assertSame(self::expand($expected, self::HTML_NBSP, self::HTML_MINUS), $actual);
    }

    /**
     * All the arguments are given explicitly, so the result is the same for both functions.
     *
     * @return Iterator<array{
     *     params: array<string, int|string>,
     *     expected: string,
     * }>
     */
    public static function dataIntFormatWithAllArguments(): iterable
    {
        yield [
            'params' => [
                'value' => 1_234_567,
                'thousandsSeparator' => ' ',
                'signPlus' => '',
                'signMinus' => '-',
            ],
            'expected' => '1 234 567',
        ];

        yield [
            'params' => [
                'value' => 123,
                'thousandsSeparator' => ',',
                'signPlus' => '+',
                'signMinus' => '-',
            ],
            'expected' => '+123',
        ];

        yield [
            'params' => [
                'value' => -1_234,
                'thousandsSeparator' => '&nbsp;',
                'signPlus' => '',
                'signMinus' => '&minus;',
            ],
            'expected' => '&minus;1&nbsp;234',
        ];

        yield [
            'params' => [
                'value' => -1_234,
                'thousandsSeparator' => "\u{00A0}",
                'signPlus' => '',
                'signMinus' => "\u{2212}",
            ],
            'expected' => "\u{2212}1\u{00A0}234",
        ];
    }

    /**
     * @param array<string, int|string> $params
     */
    #[DataProvider('dataIntFormatWithAllArguments')]
    public function testNumberIntFormatWithAllArguments(array $params, string $expected): void
    {
        $actual = number_int_format(...$params);

        self::assertSame($expected, $actual);
    }

    /**
     * @param array<string, int|string> $params
     */
    #[DataProvider('dataIntFormatWithAllArguments')]
    public function testHtmlIntFormatWithAllArguments(array $params, string $expected): void
    {
        $actual = html_int_format(...$params);

        self::assertSame($expected, $actual);
    }

    public function testConstants(): void
    {
        self::assertSame('&nbsp;', HTML_NBSP);
        self::assertSame('&minus;', HTML_MINUS);
        self::assertSame("\u{00A0}", UTF8_NBSP);
        self::assertSame("\u{2212}", UTF8_MINUS);
    }

    public function testDefaultsOfHtmlFunctionsAreTheHtmlConstants(): void
    {
        self::assertSame(
            number_int_format(-1_234_567, HTML_NBSP, '', HTML_MINUS),
            html_int_format(-1_234_567),
        );
        self::assertSame(
            number_float_format(-1_234_567.5, 2, ',', HTML_NBSP, '', HTML_MINUS),
            html_float_format(-1_234_567.5, 2),
        );
    }

    public function testUtf8ConstantsAsArgumentsOfNumberFunctions(): void
    {
        self::assertSame("\u{2212}1\u{00A0}234\u{00A0}567", number_int_format(-1_234_567, UTF8_NBSP, '', UTF8_MINUS));
        self::assertSame("\u{2212}1\u{00A0}234,50", number_float_format(-1_234.5, 2, ',', UTF8_NBSP, '', UTF8_MINUS));
    }

    public function testDefaultsOfNumberFunctionsArePlainText(): void
    {
        self::assertSame('-1234567', number_int_format(-1_234_567));
        self::assertSame('-1234567,50', number_float_format(-1_234_567.5, 2));
    }

    public function testDefaultsOfHtmlFunctionsAreHtmlEntities(): void
    {
        self::assertSame('&minus;1&nbsp;234&nbsp;567', html_int_format(-1_234_567));
        self::assertSame('&minus;1&nbsp;234&nbsp;567,50', html_float_format(-1_234_567.5, 2));
    }

    /**
     * @return Iterator<array{0: int, 1: string, 2: string}>
     */
    public static function dataIntFormatOfLimits(): iterable
    {
        yield [PHP_INT_MAX, '9 223 372 036 854 775 807', '9&nbsp;223&nbsp;372&nbsp;036&nbsp;854&nbsp;775&nbsp;807'];
        yield [PHP_INT_MIN, '-9 223 372 036 854 775 808', '&minus;9&nbsp;223&nbsp;372&nbsp;036&nbsp;854&nbsp;775&nbsp;808'];
        yield [PHP_INT_MIN + 1, '-9 223 372 036 854 775 807', '&minus;9&nbsp;223&nbsp;372&nbsp;036&nbsp;854&nbsp;775&nbsp;807'];
    }

    #[DataProvider('dataIntFormatOfLimits')]
    public function testIntFormatOfLimits(int $value, string $expectedNumber, string $expectedHtml): void
    {
        self::assertSame($expectedNumber, number_int_format($value, ' '));
        self::assertSame($expectedHtml, html_int_format($value));
    }

    public function testIntFormatOfIntMinWithDefaults(): void
    {
        self::assertSame('-9223372036854775808', number_int_format(PHP_INT_MIN));
    }

    /**
     * A number that is rounded to zero is shown without a sign: there is no "-0,00" and no "+0,00".
     *
     * @return Iterator<array{0: float, 1: int, 2: string}>
     */
    public static function dataFloatFormatOfNumbersRoundedToZero(): iterable
    {
        yield [-0.001, 2, '0,00'];
        yield [-0.0049, 2, '0,00'];
        yield [-0.4, 0, '0'];
        yield [-0.0, 2, '0,00'];
        yield [0.001, 2, '0,00'];
        yield [0.0049, 2, '0,00'];
        yield [0.4, 0, '0'];
        yield [-4.0, -1, '0'];
        yield [4.0, -1, '0'];
    }

    #[DataProvider('dataFloatFormatOfNumbersRoundedToZero')]
    public function testFloatFormatOfNumbersRoundedToZero(float $value, int $decimals, string $expected): void
    {
        self::assertSame($expected, number_float_format($value, $decimals, ',', '', '+', '-'));
        self::assertSame($expected, html_float_format($value, $decimals, ',', '', '+', '&minus;'));
    }

    /**
     * A number that is not rounded to zero keeps its sign.
     *
     * @return Iterator<array{0: float, 1: int, 2: string}>
     */
    public static function dataFloatFormatOfNumbersNotRoundedToZero(): iterable
    {
        yield [-0.005, 2, '-0,01'];
        yield [0.005, 2, '+0,01'];
        yield [-0.5, 0, '-1'];
        yield [0.5, 0, '+1'];
        yield [-5.0, -1, '-10'];
        yield [5.0, -1, '+10'];
        yield [-1.0E-6, 6, '-0,000001'];
    }

    #[DataProvider('dataFloatFormatOfNumbersNotRoundedToZero')]
    public function testFloatFormatOfNumbersNotRoundedToZero(float $value, int $decimals, string $expected): void
    {
        self::assertSame($expected, number_float_format($value, $decimals, ',', '', '+', '-'));
    }

    public function testFloatFormatShowsTheSignOnlyForNumbersThatAreNotZero(): void
    {
        $values = [0.0, -0.0, 0.001, -0.001, 0.0049, -0.0049, 0.005, -0.005, 0.5, -0.5, 1.5, -1.5, 2.5, -2.5, 99.995,
            -99.995, 1.0E-9, -1.0E-9, 123_456.789, -123_456.789, 1_234_567.891, -1_234_567.891];

        foreach ($values as $value) {
            foreach ([-1, 0, 1, 2, 3, 6] as $decimals) {
                $actual = number_float_format($value, $decimals, '.', '', '+', '-');

                $hasSign = $actual[0] === '+' || $actual[0] === '-';
                $isNotZero = preg_match('/[1-9]/', $actual) === 1;

                self::assertSame($isNotZero, $hasSign, "Failed test value: {$value}, decimals: {$decimals}");

                if ($hasSign) {
                    self::assertSame($value < 0, $actual[0] === '-', "Failed test value: {$value}, decimals: {$decimals}");
                }
            }
        }
    }

    /**
     * @return Iterator<array{0: float}>
     */
    public static function dataNotFiniteFloat(): iterable
    {
        yield [NAN];
        yield [INF];
        yield [-INF];
    }

    #[DataProvider('dataNotFiniteFloat')]
    public function testNumberFloatFormatFailedBecauseValueIsNotFinite(float $value): void
    {
        $this->expectException(NumberException::class);

        number_float_format($value, 2);
    }

    #[DataProvider('dataNotFiniteFloat')]
    public function testHtmlFloatFormatFailedBecauseValueIsNotFinite(float $value): void
    {
        $this->expectException(NumberException::class);

        html_float_format($value, 2);
    }

    public function testRuPluralThingsFailed(): void
    {
        $this->expectException(NumberException::class);

        number_ru_plural_things(-1, ['штук', 'штука', 'штуки']);
    }

    /**
     * @return Iterator<array{0: int, 1: string}>
     */
    public static function dataRuPluralThingsSuccess(): iterable
    {
        yield [0, 'штук'];
        yield [1, 'штука'];
        yield [2, 'штуки'];
        yield [3, 'штуки'];
        yield [4, 'штуки'];
        yield [5, 'штук'];
        yield [6, 'штук'];
        yield [7, 'штук'];
        yield [8, 'штук'];
        yield [9, 'штук'];
        yield [10, 'штук'];
        yield [11, 'штук'];
        yield [12, 'штук'];
        yield [13, 'штук'];
        yield [14, 'штук'];
        yield [15, 'штук'];
        yield [16, 'штук'];
        yield [17, 'штук'];
        yield [18, 'штук'];
        yield [19, 'штук'];
        yield [20, 'штук'];
        yield [21, 'штука'];
        yield [22, 'штуки'];
        yield [23, 'штуки'];
        yield [24, 'штуки'];
        yield [25, 'штук'];
        yield [26, 'штук'];
        yield [27, 'штук'];
        yield [28, 'штук'];
        yield [29, 'штук'];
    }

    /**
     * @return Iterator<array{0: array<int|string, string>}>
     */
    public static function dataRuPluralThingsFailedBecauseFormsAreNotExactlyThree(): iterable
    {
        // Fewer than three forms
        yield [[]];
        yield [['штук']];
        yield [['штук', 'штука']];

        // More than three forms
        yield [['штук', 'штука', 'штуки', 'лишняя']];
        yield [['штук', 'штука', 'штуки', 'лишняя', 'ещё одна']];

        // Three elements, but not the forms with the keys 0, 1 and 2
        yield [
            [
                'a' => 'штук',
                'b' => 'штука',
                'c' => 'штуки',
            ],
        ];
        yield [
            [
                0 => 'штук',
                1 => 'штука',
                5 => 'штуки',
            ],
        ];
        yield [
            [
                1 => 'штука',
                2 => 'штуки',
                'x' => 'штук',
            ],
        ];

        // Fewer than three elements with the keys 1 and 2 only
        yield [
            [
                1 => 'штука',
                2 => 'штуки',
            ],
        ];
    }

    /**
     * @param array<int|string, string> $forms
     */
    #[DataProvider('dataRuPluralThingsFailedBecauseFormsAreNotExactlyThree')]
    public function testRuPluralThingsFailedBecauseFormsAreNotExactlyThree(array $forms): void
    {
        $this->expectException(NumberException::class);

        number_ru_plural_things(2, $forms);
    }

    public function testRuPluralThingsWithKeysInAnyOrder(): void
    {
        $forms = [
            2 => 'штуки',
            0 => 'штук',
            1 => 'штука',
        ];

        self::assertSame('штук', number_ru_plural_things(5, $forms));
        self::assertSame('штука', number_ru_plural_things(1, $forms));
        self::assertSame('штуки', number_ru_plural_things(2, $forms));
    }

    #[DataProvider('dataRuPluralThingsSuccess')]
    public function testRuPluralThingsSuccess(int $value, string $expected): void
    {
        $actual = number_ru_plural_things($value, ['штук', 'штука', 'штуки']);

        self::assertSame($expected, $actual);
    }
}
