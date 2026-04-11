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
use SergeYakovlev\Support\Str\FluentStr;
use SergeYakovlev\Support\Str\Str;

final class StrTest extends TestCase
{
    /**
     * @return Iterator<array{0: string, 1: string|string[], 2: boolean}>
     */
    public static function dataContains(): iterable
    {
        yield ['abcdef', 'xxx', false];
        yield ['abcdef', ['xxx', 'yyy', 'zzz'], false];

        yield ['abcdef', '', true];
        yield ['abcdef', ['', 'yyy', 'zzz'], true];
        yield ['abcdef', ['xxx', '', 'zzz'], true];
        yield ['abcdef', ['xxx', 'yyy', ''], true];

        yield ['abcdef', 'ab', true];
        yield ['abcdef', 'cd', true];
        yield ['abcdef', 'ef', true];
        yield ['abcdef', ['ab'], true];
        yield ['abcdef', ['cd'], true];
        yield ['abcdef', ['ef'], true];

        yield ['абвгде', 'аб', true];
        yield ['абвгде', 'вг', true];
        yield ['абвгде', 'де', true];
        yield ['абвгде', ['аб'], true];
        yield ['абвгде', ['вг'], true];
        yield ['абвгде', ['де'], true];
    }

    /**
     * @param string|string[] $needle
     */
    #[DataProvider('dataContains')]
    public function testContains(string $haystack, string|array $needle, bool $expected): void
    {
        $actual = Str::contains($haystack, $needle);

        self::assertSame($expected, $actual);
    }

    /**
     * @param string|string[] $needle
     */
    #[DataProvider('dataContains')]
    public function testDoesntContain(string $haystack, string|array $needle, bool $expected): void
    {
        $actual = Str::doesntContain($haystack, $needle);

        self::assertSame(!$expected, $actual);
    }

    /**
     * @return Iterator<array{0: string, 1: integer}>
     */
    public static function dataLength(): iterable
    {
        yield ['', 0];
        yield ['Hello World!', 12];
        yield ['Привет, Мир!', 12];
    }

    #[DataProvider('dataLength')]
    public function testLength(string $string, int $expected): void
    {
        $actual = Str::length($string);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{0: string, 1: string|string[], 2: boolean}>
     */
    public static function dataStartsWith(): iterable
    {
        yield ['abcdef', 'xxx', false];
        yield ['abcdef', ['xxx', 'yyy', 'zzz'], false];

        yield ['abcdef', '', true];
        yield ['abcdef', ['', 'yyy', 'zzz'], true];
        yield ['abcdef', ['xxx', '', 'zzz'], true];
        yield ['abcdef', ['xxx', 'yyy', ''], true];

        yield ['abcdef', 'ab', true];
        yield ['abcdef', ['ab'], true];

        yield ['абвгде', 'аб', true];
        yield ['абвгде', ['аб'], true];
    }

    /**
     * @param string|string[] $needle
     */
    #[DataProvider('dataStartsWith')]
    public function testStartsWith(string $haystack, string|array $needle, bool $expected): void
    {
        $actual = Str::startsWith($haystack, $needle);

        self::assertSame($expected, $actual);
    }

    /**
     * @param string|string[] $needle
     */
    #[DataProvider('dataStartsWith')]
    public function testDoesntStartWith(string $haystack, string|array $needle, bool $expected): void
    {
        $actual = Str::doesntStartWith($haystack, $needle);

        self::assertSame(!$expected, $actual);
    }

    /**
     * @return Iterator<array{0: string, 1: string|string[], 2: boolean}>
     */
    public static function dataEndsWith(): iterable
    {
        yield ['abcdef', 'xxx', false];
        yield ['abcdef', ['xxx', 'yyy', 'zzz'], false];

        yield ['abcdef', '', true];
        yield ['abcdef', ['', 'yyy', 'zzz'], true];
        yield ['abcdef', ['xxx', '', 'zzz'], true];
        yield ['abcdef', ['xxx', 'yyy', ''], true];

        yield ['abcdef', 'ef', true];
        yield ['abcdef', ['ef'], true];

        yield ['абвгде', 'де', true];
        yield ['абвгде', ['де'], true];
    }

    /**
     * @param string|string[] $needle
     */
    #[DataProvider('dataEndsWith')]
    public function testEndsWith(string $haystack, string|array $needle, bool $expected): void
    {
        $actual = Str::endsWith($haystack, $needle);

        self::assertSame($expected, $actual);
    }

    /**
     * @param string|string[] $needle
     */
    #[DataProvider('dataEndsWith')]
    public function testDoesntEndWith(string $haystack, string|array $needle, bool $expected): void
    {
        $actual = Str::doesntEndWith($haystack, $needle);

        self::assertSame(!$expected, $actual);
    }

    /**
     * @return Iterator<string[]>
     */
    public static function dataPrepend(): iterable
    {
        yield ['', '', ''];
        yield ['', 'abc', 'abc'];
        yield ['abc', '', 'abc'];
        yield ['def', 'abc', 'abcdef'];
    }

    #[DataProvider('dataPrepend')]
    public function testPrepend(string $string, string $prependString, string $expected): void
    {
        $actual = Str::prepend($string, $prependString);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<string[]>
     */
    public static function dataAppend(): iterable
    {
        yield ['', '', ''];
        yield ['', 'abc', 'abc'];
        yield ['abc', '', 'abc'];
        yield ['abc', 'def', 'abcdef'];
    }

    #[DataProvider('dataAppend')]
    public function testAppend(string $string, string $appendString, string $expected): void
    {
        $actual = Str::append($string, $appendString);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<string[]>
     */
    public static function dataStart(): iterable
    {
        yield ['', '', ''];
        yield ['', '/', '/'];

        yield ['some/path', '', 'some/path'];
        yield ['some/path', '/', '/some/path'];
        yield ['/some/path', '/', '/some/path'];

        yield ['Мир!', '', 'Мир!'];
        yield ['Мир!', 'Привет, ', 'Привет, Мир!'];
        yield ['Привет, Мир!', 'Привет, ', 'Привет, Мир!'];
    }

    #[DataProvider('dataStart')]
    public function testStart(string $string, string $startString, string $expected): void
    {
        $actual = Str::start($string, $startString);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<string[]>
     */
    public static function dataEnd(): iterable
    {
        yield ['', '', ''];
        yield ['', '/', '/'];

        yield ['https://example.com', '', 'https://example.com'];
        yield ['https://example.com', '/', 'https://example.com/'];
        yield ['https://example.com/', '/', 'https://example.com/'];

        yield ['Привет', '', 'Привет'];
        yield ['Привет', ', Мир!', 'Привет, Мир!'];
        yield ['Привет, Мир!', ', Мир!', 'Привет, Мир!'];
    }

    #[DataProvider('dataEnd')]
    public function testEnd(string $string, string $endString, string $expected): void
    {
        $actual = Str::end($string, $endString);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<string[]>
     */
    public static function dataUpper(): iterable
    {
        yield ['', ''];
        yield ['hello!', 'HELLO!'];
        yield ['HELLO!', 'HELLO!'];
        yield ['привет!', 'ПРИВЕТ!'];
        yield ['ПРИВЕТ!', 'ПРИВЕТ!'];
    }

    #[DataProvider('dataUpper')]
    public function testUpper(string $string, string $expected): void
    {
        $actual = Str::upper($string);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<string[]>
     */
    public static function dataLower(): iterable
    {
        yield ['', ''];
        yield ['hello!', 'hello!'];
        yield ['HELLO!', 'hello!'];
        yield ['привет!', 'привет!'];
        yield ['ПРИВЕТ!', 'привет!'];
    }

    #[DataProvider('dataLower')]
    public function testLower(string $string, string $expected): void
    {
        $actual = Str::lower($string);

        self::assertSame($expected, $actual);
    }

    public function testOf(): void
    {
        $expected = new FluentStr('abc');

        $actual = Str::of('abc');

        self::assertEquals(
            $expected,
            $actual,
        );
    }
}
