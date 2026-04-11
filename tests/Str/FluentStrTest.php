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

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use SergeYakovlev\Support\Str\FluentStr;

final class FluentStrTest extends TestCase
{
    public function testMagicToString(): void
    {
        $actual = (string) new FluentStr('Hello!');

        self::assertSame('Hello!', $actual);
    }

    public function testToString(): void
    {
        $actual = new FluentStr('Hello!')->toString();

        self::assertSame('Hello!', $actual);
    }

    /**
     * @param string|string[] $needle
     */
    #[DataProviderExternal(StrTest::class, 'dataContains')]
    public function testContains(string $haystack, string|array $needle, bool $expected): void
    {
        $actual = new FluentStr($haystack)->contains($needle);

        self::assertSame($expected, $actual);
    }

    /**
     * @param string|string[] $needle
     */
    #[DataProviderExternal(StrTest::class, 'dataContains')]
    public function testDoesntContain(string $haystack, string|array $needle, bool $expected): void
    {
        $actual = new FluentStr($haystack)->doesntContain($needle);

        self::assertSame(!$expected, $actual);
    }

    #[DataProviderExternal(StrTest::class, 'dataLength')]
    public function testLength(string $string, int $expected): void
    {
        $actual = new FluentStr($string)->length();

        self::assertSame($expected, $actual);
    }

    /**
     * @param string|string[] $needle
     */
    #[DataProviderExternal(StrTest::class, 'dataStartsWith')]
    public function testStartsWith(string $haystack, string|array $needle, bool $expected): void
    {
        $actual = new FluentStr($haystack)->startsWith($needle);

        self::assertSame($expected, $actual);
    }

    /**
     * @param string|string[] $needle
     */
    #[DataProviderExternal(StrTest::class, 'dataStartsWith')]
    public function testDoesntStartWith(string $haystack, string|array $needle, bool $expected): void
    {
        $actual = new FluentStr($haystack)->doesntStartWith($needle);

        self::assertSame(!$expected, $actual);
    }

    /**
     * @param string|string[] $needle
     */
    #[DataProviderExternal(StrTest::class, 'dataEndsWith')]
    public function testEndsWith(string $haystack, string|array $needle, bool $expected): void
    {
        $actual = new FluentStr($haystack)->endsWith($needle);

        self::assertSame($expected, $actual);
    }

    /**
     * @param string|string[] $needle
     */
    #[DataProviderExternal(StrTest::class, 'dataEndsWith')]
    public function testDoesntEndWith(string $haystack, string|array $needle, bool $expected): void
    {
        $actual = new FluentStr($haystack)->doesntEndWith($needle);

        self::assertSame(!$expected, $actual);
    }

    #[DataProviderExternal(StrTest::class, 'dataPrepend')]
    public static function testPrepend(string $string, string $prependString, string $expected): void
    {
        $actual = new FluentStr($string)->prepend($prependString);

        self::assertEquals(new FluentStr($expected), $actual);
    }

    #[DataProviderExternal(StrTest::class, 'dataAppend')]
    public static function testAppend(string $string, string $appendString, string $expected): void
    {
        $actual = new FluentStr($string)->append($appendString);

        self::assertEquals(new FluentStr($expected), $actual);
    }

    #[DataProviderExternal(StrTest::class, 'dataStart')]
    public function testStart(string $string, string $startString, string $expected): void
    {
        $actual = new FluentStr($string)->start($startString);

        self::assertEquals(new FluentStr($expected), $actual);
    }

    #[DataProviderExternal(StrTest::class, 'dataEnd')]
    public function testEnd(string $string, string $endString, string $expected): void
    {
        $actual = new FluentStr($string)->end($endString);

        self::assertEquals(new FluentStr($expected), $actual);
    }

    #[DataProviderExternal(StrTest::class, 'dataUpper')]
    public function testUpper(string $string, string $expected): void
    {
        $expectedObject = new FluentStr($expected);
        $originalObject = new FluentStr($string);

        $actualObject = $originalObject->upper();

        $expected === $string
            ? self::assertSame($originalObject, $actualObject)
            : self::assertEquals($expectedObject, $actualObject);
    }

    #[DataProviderExternal(StrTest::class, 'dataLower')]
    public function testLower(string $string, string $expected): void
    {
        $expectedObject = new FluentStr($expected);
        $originalObject = new FluentStr($string);

        $actualObject = $originalObject->lower();

        $expected === $string
            ? self::assertSame($originalObject, $actualObject)
            : self::assertEquals($expectedObject, $actualObject);
    }
}
