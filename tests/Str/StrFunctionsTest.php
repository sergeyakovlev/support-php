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

namespace SergeYakovlev\Support\Tests\Str;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function SergeYakovlev\Support\Str\str_emoji_country_flag;
use function SergeYakovlev\Support\Str\str_non_empty;
use function SergeYakovlev\Support\Str\str_normalize_newlines;

final class StrFunctionsTest extends TestCase
{
    /**
     * The texts added around a flag: the left text, the right text and the result for the "RU" or "ru" code.
     * The null value and the empty string both mean "no text".
     *
     * @return list<array{0: string|null, 1: string|null, 2: string}>
     */
    private static function strEmojiCountryFlagExtensions(): array
    {
        return [
            [null, null, '🇷🇺'],
            ['', '', '🇷🇺'],
            [null, '', '🇷🇺'],
            ['', null, '🇷🇺'],

            // One text
            ['&nbsp;', null, '&nbsp;🇷🇺'],
            ['&nbsp;', '', '&nbsp;🇷🇺'],
            [null, '&nbsp;', '🇷🇺&nbsp;'],
            ['', '&nbsp;', '🇷🇺&nbsp;'],
            [' ', null, ' 🇷🇺'],
            [' ', '', ' 🇷🇺'],
            [null, ' ', '🇷🇺 '],
            ['', ' ', '🇷🇺 '],

            // Two texts
            ['&nbsp;', '&nbsp;', '&nbsp;🇷🇺&nbsp;'],
            ['&nbsp;', ' ', '&nbsp;🇷🇺 '],
            [' ', '&nbsp;', ' 🇷🇺&nbsp;'],
            [' ', ' ', ' 🇷🇺 '],
        ];
    }

    /**
     * @return Iterator<array{0: string, 1: string|null, 2: string|null, 3: string}>
     */
    public static function dataStrEmojiCountryFlagValidCode(): iterable
    {
        foreach (['RU', 'ru'] as $code) {
            foreach (self::strEmojiCountryFlagExtensions() as [$extLeft, $extRight, $expected]) {
                yield [$code, $extLeft, $extRight, $expected];
            }
        }
    }

    #[DataProvider('dataStrEmojiCountryFlagValidCode')]
    public function testStrEmojiCountryFlagWithValidCode(
        string $countryIsoAlpha2,
        ?string $extLeft,
        ?string $extRight,
        string $expected,
    ): void {
        $actual = str_emoji_country_flag($countryIsoAlpha2, $extLeft, $extRight);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{0: string, 1: string|null, 2: string|null}>
     */
    public static function dataStrEmojiCountryFlagInvalidCode(): iterable
    {
        $invalidCodes = [
            // Wrong length
            '',
            'R',
            'RUS',
            'InvalidISO',

            // Not letters
            '12',
            'R1',
            '1R',
            '--',
            'РУ', // Cyrillic
            '🇷🇺', // already a flag

            // Letters in different case
            'Ru',
            'rU',

            // Extra characters around the code
            ' RU',
            'RU ',
            "RU\n",
            "\nRU",
            "RU\0",
        ];

        foreach ($invalidCodes as $code) {
            foreach (self::strEmojiCountryFlagExtensions() as [$extLeft, $extRight]) {
                yield [$code, $extLeft, $extRight];
            }
        }
    }

    #[DataProvider('dataStrEmojiCountryFlagInvalidCode')]
    public function testStrEmojiCountryFlagWithInvalidCode(
        string $countryIsoAlpha2,
        ?string $extLeft,
        ?string $extRight,
    ): void {
        $actual = str_emoji_country_flag($countryIsoAlpha2, $extLeft, $extRight);

        self::assertSame('', $actual);
    }

    /**
     * @return Iterator<array{0: string, 1: string}>
     */
    public static function dataStrEmojiCountryFlagCodes(): iterable
    {
        yield ['RU', '🇷🇺'];
        yield ['US', '🇺🇸'];
        yield ['GB', '🇬🇧'];
        yield ['DE', '🇩🇪'];

        // The first and the last letters of the alphabet
        yield ['AZ', '🇦🇿'];
        yield ['ZA', '🇿🇦'];

        // The code is not checked against the list of countries
        yield ['AA', '🇦🇦'];
        yield ['ZZ', '🇿🇿'];
    }

    #[DataProvider('dataStrEmojiCountryFlagCodes')]
    public function testStrEmojiCountryFlagCodes(string $countryIsoAlpha2, string $expected): void
    {
        self::assertSame($expected, str_emoji_country_flag($countryIsoAlpha2));
        self::assertSame($expected, str_emoji_country_flag(strtolower($countryIsoAlpha2)));
    }

    public function testStrEmojiCountryFlagForAllLetterPairs(): void
    {
        $regionalIndicatorA = 0x1F1E6;
        $letters = range('A', 'Z');

        foreach ($letters as $firstIndex => $first) {
            foreach ($letters as $secondIndex => $second) {
                foreach ([$first . $second, strtolower($first . $second)] as $code) {
                    $actual = str_emoji_country_flag($code);

                    self::assertSame(2, mb_strlen($actual), "Failed test code: {$code}");
                    self::assertSame($regionalIndicatorA + $firstIndex, mb_ord($actual), "Failed test code: {$code}");
                    self::assertSame(
                        $regionalIndicatorA + $secondIndex,
                        mb_ord(mb_substr($actual, 1)),
                        "Failed test code: {$code}",
                    );
                }
            }
        }
    }

    public function testStrEmojiCountryFlagWithOmittedExtensions(): void
    {
        self::assertSame('🇷🇺', str_emoji_country_flag('RU'));
        self::assertSame('&nbsp;🇷🇺', str_emoji_country_flag('RU', '&nbsp;'));
        self::assertSame('', str_emoji_country_flag('', '&nbsp;'));
        self::assertSame('', str_emoji_country_flag('InvalidISO', '&nbsp;'));
    }

    public function testStrEmojiCountryFlagWithNamedExtensions(): void
    {
        self::assertSame('🇷🇺&nbsp;', str_emoji_country_flag('RU', extRight: '&nbsp;'));
        self::assertSame('&nbsp;🇷🇺', str_emoji_country_flag('RU', extLeft: '&nbsp;'));
        self::assertSame('[🇷🇺]', str_emoji_country_flag(countryIsoAlpha2: 'RU', extLeft: '[', extRight: ']'));
    }

    /**
     * @return Iterator<array{0: string|null, 1: string|null}>
     */
    public static function dataStrNonEmpty(): iterable
    {
        yield [null, null];
        yield ['', null];

        yield ['a', 'a'];
        yield ['abc', 'abc'];
        yield ['абв', 'абв'];
        yield ['😀', '😀'];

        // Falsy-looking values are not empty strings
        yield ['0', '0'];
        yield ['00', '00'];
        yield ['0.0', '0.0'];

        // Whitespace is not trimmed, so whitespace-only strings are not empty
        yield [' ', ' '];
        yield ["\t", "\t"];
        yield ["\n", "\n"];
        yield ["\0", "\0"];
        yield [' abc ', ' abc '];
    }

    #[DataProvider('dataStrNonEmpty')]
    public function testStrNonEmpty(?string $string, ?string $expected): void
    {
        $actual = str_non_empty($string);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{0: string|null, 1: string|null}>
     */
    public static function dataStrNormalizeNewlines(): iterable
    {
        yield [null, null];

        yield ['', ''];
        yield ['abc', 'abc'];

        // Single line breaks
        yield ["\n", "\n"];
        yield ["\r\n", "\n"];
        yield ["\r", "\n"];

        // Line breaks between text
        yield ["a\nb", "a\nb"];
        yield ["a\r\nb", "a\nb"];
        yield ["a\rb", "a\nb"];

        // Line breaks at the edges
        yield ["\r\nabc", "\nabc"];
        yield ["abc\r\n", "abc\n"];
        yield ["\rabc\r", "\nabc\n"];

        // Mixed line breaks
        yield ["a\r\nb\rc\nd", "a\nb\nc\nd"];

        // Each break is replaced separately, nothing is merged
        yield ["\n\n", "\n\n"];
        yield ["\r\n\r\n", "\n\n"];
        yield ["\r\r", "\n\n"];
        yield ["\r\r\n", "\n\n"];
        yield ["\r\n\r", "\n\n"];
        yield ["\n\r", "\n\n"]; // LF followed by CR is two line breaks, not CRLF
        yield ["\r\n\n", "\n\n"];
        yield ["\r\n\r\n\r\n", "\n\n\n"];

        // Multibyte text
        yield ["абв\r\nгде\rёжз\nийк", "абв\nгде\nёжз\nийк"];
        yield ["😀\r\n😀", "😀\n😀"];

        // Characters whose UTF-8 encoding contains the byte 0x85 (NEL in Latin-1) or 0x0D must stay intact
        yield ["Ѕ\r\nѕ", "Ѕ\nѕ"];
        yield ["Ѕ", "Ѕ"];

        // Invalid UTF-8 is passed through
        yield ["\xFF\r\n\xFE", "\xFF\n\xFE"];

        // Other whitespace and Unicode line separators are not line breaks here
        yield ["a\vb", "a\vb"];
        yield ["a\fb", "a\fb"];
        yield ["a\u{0085}b", "a\u{0085}b"];
        yield ["a\u{2028}b", "a\u{2028}b"];
        yield ["a\u{2029}b", "a\u{2029}b"];
        yield ["a\tb c", "a\tb c"];
    }

    #[DataProvider('dataStrNormalizeNewlines')]
    public function testStrNormalizeNewlines(?string $string, ?string $expected): void
    {
        $actual = str_normalize_newlines($string);

        self::assertSame($expected, $actual);
    }

    #[DataProvider('dataStrNormalizeNewlines')]
    public function testStrNormalizeNewlinesIsIdempotent(?string $string, ?string $expected): void
    {
        $actual = str_normalize_newlines(str_normalize_newlines($string));

        self::assertSame($expected, $actual);
    }
}
