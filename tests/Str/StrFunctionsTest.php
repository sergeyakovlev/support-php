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
use function SergeYakovlev\Support\Str\str_empty_to_null;
use function SergeYakovlev\Support\Str\str_ensure_ends_with;
use function SergeYakovlev\Support\Str\str_ensure_starts_with;
use function SergeYakovlev\Support\Str\str_normalize_newlines;

final class StrFunctionsTest extends TestCase
{
    /**
     * @return Iterator<array{0: string}>
     */
    public static function dataStrEmojiCountryFlagInvalidCode(): iterable
    {
        // Wrong length
        yield [''];
        yield ['R'];
        yield ['RUS'];
        yield ['InvalidISO'];

        // Not letters
        yield ['12'];
        yield ['R1'];
        yield ['1R'];
        yield ['--'];
        yield ['РУ']; // Cyrillic
        yield ['🇷🇺']; // already a flag

        // Letters in different case
        yield ['Ru'];
        yield ['rU'];

        // Extra characters around the code
        yield [' RU'];
        yield ['RU '];
        yield ["RU\n"];
        yield ["\nRU"];
        yield ["RU\0"];
    }

    #[DataProvider('dataStrEmojiCountryFlagInvalidCode')]
    public function testStrEmojiCountryFlagWithInvalidCode(string $countryIsoAlpha2): void
    {
        $actual = str_emoji_country_flag($countryIsoAlpha2);

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

    /**
     * @return Iterator<array{0: string|null, 1: string|null}>
     */
    public static function dataStrEmptyToNull(): iterable
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

    #[DataProvider('dataStrEmptyToNull')]
    public function testStrEmptyToNull(?string $string, ?string $expected): void
    {
        $actual = str_empty_to_null($string);

        self::assertSame($expected, $actual);
    }

    /**
     * The string, the prefix, the result without collapsing and the result with collapsing.
     *
     * @return Iterator<array{0: string, 1: string, 2: string, 3: string}>
     */
    public static function dataStrEnsureStartsWith(): iterable
    {
        // The prefix is missing
        yield ['foo', '/', '/foo', '/foo'];
        yield ['', '/', '/', '/'];

        // The prefix is present once
        yield ['/foo', '/', '/foo', '/foo'];
        yield ['/', '/', '/', '/'];

        // The prefix is repeated
        yield ['//foo', '/', '//foo', '/foo'];
        yield ['///foo', '/', '///foo', '/foo'];
        yield ['///', '/', '///', '/'];

        // Repeats that are not next to each other are not collapsed
        yield ['/ /foo', '/', '/ /foo', '/ /foo'];
        yield ['/foo/', '/', '/foo/', '/foo/'];

        // A prefix of several characters is a whole substring, not a set of characters
        yield ['baz', 'ab', 'abbaz', 'abbaz'];
        yield ['abbaz', 'ab', 'abbaz', 'abbaz'];
        yield ['ababbaz', 'ab', 'ababbaz', 'abbaz'];
        yield ['http://x', 'https://', 'https://http://x', 'https://http://x'];
        yield ['https://x', 'https://', 'https://x', 'https://x'];
        yield ['https://https://x', 'https://', 'https://https://x', 'https://x'];

        // A part of the prefix at the start is not the prefix
        yield ['a', 'ab', 'aba', 'aba'];
        yield ['aab', 'ab', 'abaab', 'abaab'];
        yield ['ab', 'abc', 'abcab', 'abcab'];

        // Repeats that overlap
        yield ['aaa', 'aa', 'aaa', 'aaa'];
        yield ['aaaa', 'aa', 'aaaa', 'aa'];
        yield ['aaaaa', 'aa', 'aaaaa', 'aaa'];

        // The comparison is case-sensitive
        yield ['Foo', 'foo', 'fooFoo', 'fooFoo'];
        yield ['ABab', 'ab', 'abABab', 'abABab'];

        // An empty prefix changes nothing
        yield ['foo', '', 'foo', 'foo'];
        yield ['', '', '', ''];

        // Falsy-looking values are ordinary strings
        yield ['0', '0', '0', '0'];
        yield ['00', '0', '00', '0'];
        yield ['5', '0', '05', '05'];

        // Whitespace is not trimmed
        yield [' foo', ' ', ' foo', ' foo'];
        yield ["\nfoo", "\n", "\nfoo", "\nfoo"];
        yield ["\n\nfoo", "\n", "\n\nfoo", "\nfoo"];
        yield ["\r\nfoo", "\n", "\n\r\nfoo", "\n\r\nfoo"];

        // Multibyte text: "ё" and "я" share the first byte
        yield ['ёлка', 'я', 'яёлка', 'яёлка'];
        yield ['яблоко', 'я', 'яблоко', 'яблоко'];
        yield ['яяблоко', 'я', 'яяблоко', 'яблоко'];
        yield ['яблоко', 'яб', 'яблоко', 'яблоко'];
        yield ['😀😀a', '😀', '😀😀a', '😀a'];
        yield ['a', '😀', '😀a', '😀a'];

        // Invalid UTF-8: the first byte of "я" is not the prefix "я"
        yield ["\xD1", 'я', "я\xD1", "я\xD1"];
        yield ["\xFE\xFE\xFF", "\xFE", "\xFE\xFE\xFF", "\xFE\xFF"];
    }

    #[DataProvider('dataStrEnsureStartsWith')]
    public function testStrEnsureStartsWith(
        string $string,
        string $prefix,
        string $expected,
        string $expectedCollapsed,
    ): void {
        self::assertSame($expected, str_ensure_starts_with($string, $prefix));
        self::assertSame($expected, str_ensure_starts_with($string, $prefix, false));
        self::assertSame($expected, str_ensure_starts_with($string, $prefix, collapse: false));

        self::assertSame($expectedCollapsed, str_ensure_starts_with($string, $prefix, true));
        self::assertSame($expectedCollapsed, str_ensure_starts_with($string, $prefix, collapse: true));
    }

    #[DataProvider('dataStrEnsureStartsWith')]
    public function testStrEnsureStartsWithIsIdempotent(
        string $string,
        string $prefix,
        string $expected,
        string $expectedCollapsed,
    ): void {
        self::assertSame($expected, str_ensure_starts_with($expected, $prefix));
        self::assertSame($expectedCollapsed, str_ensure_starts_with($expectedCollapsed, $prefix, true));
    }

    public function testStrEnsureStartsWithCollapseManyRepeats(): void
    {
        self::assertSame('abc', str_ensure_starts_with(str_repeat('ab', 10000) . 'c', 'ab', true));
        self::assertSame('ab', str_ensure_starts_with(str_repeat('ab', 10000), 'ab', true));
    }

    /**
     * The null string and the prefix or the suffix.
     *
     * @return Iterator<array{0: null, 1: string}>
     */
    public static function dataStrEnsureNull(): iterable
    {
        yield [null, '/'];
        yield [null, 'ab'];
        yield [null, ' '];
        yield [null, 'я'];
        yield [null, '😀'];

        // An empty prefix or suffix
        yield [null, ''];
    }

    #[DataProvider('dataStrEnsureNull')]
    public function testStrEnsureStartsWithNull(?string $string, string $prefix): void
    {
        self::assertNull(str_ensure_starts_with($string, $prefix));
        self::assertNull(str_ensure_starts_with($string, $prefix, false));
        self::assertNull(str_ensure_starts_with($string, $prefix, true));
        self::assertNull(str_ensure_starts_with(string: $string, prefix: $prefix, collapse: true));
    }

    /**
     * The string, the suffix, the result without collapsing and the result with collapsing.
     *
     * @return Iterator<array{0: string, 1: string, 2: string, 3: string}>
     */
    public static function dataStrEnsureEndsWith(): iterable
    {
        // The suffix is missing
        yield ['foo', '/', 'foo/', 'foo/'];
        yield ['', '/', '/', '/'];

        // The suffix is present once
        yield ['foo/', '/', 'foo/', 'foo/'];
        yield ['/', '/', '/', '/'];

        // The suffix is repeated
        yield ['foo//', '/', 'foo//', 'foo/'];
        yield ['foo///', '/', 'foo///', 'foo/'];
        yield ['///', '/', '///', '/'];

        // Repeats that are not next to each other are not collapsed
        yield ['foo/ /', '/', 'foo/ /', 'foo/ /'];
        yield ['/foo/', '/', '/foo/', '/foo/'];

        // A suffix of several characters is a whole substring, not a set of characters
        yield ['baz', 'ab', 'bazab', 'bazab'];
        yield ['bazab', 'ab', 'bazab', 'bazab'];
        yield ['bazabab', 'ab', 'bazabab', 'bazab'];
        yield ['image.jpg', '.png', 'image.jpg.png', 'image.jpg.png'];
        yield ['image.png', '.png', 'image.png', 'image.png'];
        yield ['quiz', '.gz', 'quiz.gz', 'quiz.gz'];
        yield ['file.gz', '.gz', 'file.gz', 'file.gz'];
        yield ['file.gz.gz', '.gz', 'file.gz.gz', 'file.gz'];

        // A part of the suffix at the end is not the suffix
        yield ['b', 'ab', 'bab', 'bab'];
        yield ['abb', 'ab', 'abbab', 'abbab'];
        yield ['ab', 'abc', 'ababc', 'ababc'];

        // Repeats that overlap
        yield ['aaa', 'aa', 'aaa', 'aaa'];
        yield ['aaaa', 'aa', 'aaaa', 'aa'];
        yield ['aaaaa', 'aa', 'aaaaa', 'aaa'];

        // The comparison is case-sensitive
        yield ['Foo', 'foo', 'Foofoo', 'Foofoo'];
        yield ['abAB', 'ab', 'abABab', 'abABab'];

        // An empty suffix changes nothing
        yield ['foo', '', 'foo', 'foo'];
        yield ['', '', '', ''];

        // Falsy-looking values are ordinary strings
        yield ['0', '0', '0', '0'];
        yield ['00', '0', '00', '0'];
        yield ['5', '0', '50', '50'];

        // Whitespace is not trimmed
        yield ['foo ', ' ', 'foo ', 'foo '];
        yield ["foo\n", "\n", "foo\n", "foo\n"];
        yield ["foo\n\n", "\n", "foo\n\n", "foo\n"];
        yield ["foo\r", "\n", "foo\r\n", "foo\r\n"];

        // Multibyte text
        yield ['мама', 'ы', 'мамаы', 'мамаы'];
        yield ['яблоко', 'я', 'яблокоя', 'яблокоя'];
        yield ['яблокояя', 'я', 'яблокояя', 'яблокоя'];
        yield ['яблоко', 'ко', 'яблоко', 'яблоко'];
        yield ['a😀😀', '😀', 'a😀😀', 'a😀'];
        yield ['a', '😀', 'a😀', 'a😀'];

        // Invalid UTF-8: the last byte of "я" is not the suffix "я"
        yield ["\x8F", 'я', "\x8Fя", "\x8Fя"];
        yield ["\xFF\xFE\xFE", "\xFE", "\xFF\xFE\xFE", "\xFF\xFE"];
    }

    #[DataProvider('dataStrEnsureEndsWith')]
    public function testStrEnsureEndsWith(
        string $string,
        string $suffix,
        string $expected,
        string $expectedCollapsed,
    ): void {
        self::assertSame($expected, str_ensure_ends_with($string, $suffix));
        self::assertSame($expected, str_ensure_ends_with($string, $suffix, false));
        self::assertSame($expected, str_ensure_ends_with($string, $suffix, collapse: false));

        self::assertSame($expectedCollapsed, str_ensure_ends_with($string, $suffix, true));
        self::assertSame($expectedCollapsed, str_ensure_ends_with($string, $suffix, collapse: true));
    }

    #[DataProvider('dataStrEnsureEndsWith')]
    public function testStrEnsureEndsWithIsIdempotent(
        string $string,
        string $suffix,
        string $expected,
        string $expectedCollapsed,
    ): void {
        self::assertSame($expected, str_ensure_ends_with($expected, $suffix));
        self::assertSame($expectedCollapsed, str_ensure_ends_with($expectedCollapsed, $suffix, true));
    }

    public function testStrEnsureEndsWithCollapseManyRepeats(): void
    {
        self::assertSame('cab', str_ensure_ends_with('c' . str_repeat('ab', 10000), 'ab', true));
        self::assertSame('ab', str_ensure_ends_with(str_repeat('ab', 10000), 'ab', true));
    }

    #[DataProvider('dataStrEnsureNull')]
    public function testStrEnsureEndsWithNull(?string $string, string $suffix): void
    {
        self::assertNull(str_ensure_ends_with($string, $suffix));
        self::assertNull(str_ensure_ends_with($string, $suffix, false));
        self::assertNull(str_ensure_ends_with($string, $suffix, true));
        self::assertNull(str_ensure_ends_with(string: $string, suffix: $suffix, collapse: true));
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
