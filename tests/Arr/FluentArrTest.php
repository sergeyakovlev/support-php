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

namespace SergeYakovlev\Support\Tests\Arr;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use SergeYakovlev\Support\Arr\ArrException;
use SergeYakovlev\Support\Arr\FluentArr;

final class FluentArrTest extends TestCase
{
    #[DataProviderExternal(DotNotationPathTest::class, 'dataValidateOrThrowExceptionFailed')]
    public function testGetFailed(string $path): void
    {
        $this->expectException(ArrException::class);

        new FluentArr(ArrTest::TEST_ARRAY)->get($path);
    }

    #[DataProviderExternal(ArrTest::class, 'dataGetSuccess')]
    public function testGetSuccess(string $path, mixed $default, mixed $expected): void
    {
        $actual = new FluentArr(ArrTest::TEST_ARRAY)->get($path, $default);

        is_array($expected)
            ? self::assertEquals(new FluentArr($expected), $actual, "Failed test path: {$path}")
            : self::assertSame($expected, $actual, "Failed test path: {$path}");
    }

    /**
     * @param array<string, mixed> $array
     */
    #[DataProviderExternal(ArrTest::class, 'dataSetFailed')]
    public function testSetFailed(array $array, string $path, mixed $value): void
    {
        $this->expectException(ArrException::class);

        new FluentArr($array)->set($path, $value);
    }

    /**
     * @return Iterator<array{
     *     reference: array<string, mixed>,
     *     original: array<string, mixed>,
     *     path: string,
     *     value: int|string,
     *     expected: array<string, mixed>,
     *     isReturnsSameObject: boolean,
     * }>
     */
    public static function dataSetSuccess(): iterable
    {
        yield [
            'reference' => [],
            'original' => [],
            'path' => 'id',
            'value' => 123,
            'expected' => [
                'id' => 123,
            ],
            'isReturnsSameObject' => false,
        ];

        yield [
            'reference' => [],
            'original' => [],
            'path' => 'translate.ru.placeholder',
            'value' => 'Плейсхолдер',
            'expected' => [
                'translate' => [
                    'ru' => [
                        'placeholder' => 'Плейсхолдер',
                    ],
                ],
            ],
            'isReturnsSameObject' => false,
        ];

        yield [
            'reference' => [
                'id' => 123,
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                ],
            ],
            'original' => [
                'id' => 123,
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                ],
            ],
            'path' => 'translate.ru.placeholder',
            'value' => 'Плейсхолдер',
            'expected' => [
                'id' => 123,
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                    'ru' => [
                        'placeholder' => 'Плейсхолдер',
                    ],
                ],
            ],
            'isReturnsSameObject' => false,
        ];

        yield [
            'reference' => [
                'id' => 123,
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                    'ru' => [
                        'placeholder' => 'Placeholder',
                    ],
                ],
            ],
            'original' => [
                'id' => 123,
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                    'ru' => [
                        'placeholder' => 'Placeholder',
                    ],
                ],
            ],
            'path' => 'translate.ru.placeholder',
            'value' => 'Плейсхолдер',
            'expected' => [
                'id' => 123,
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                    'ru' => [
                        'placeholder' => 'Плейсхолдер',
                    ],
                ],
            ],
            'isReturnsSameObject' => false,
        ];

        yield [
            'reference' => [
                'id' => 123,
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                    'ru' => [
                        'placeholder' => 'Плейсхолдер',
                    ],
                ],
            ],
            'original' => [
                'id' => 123,
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                    'ru' => [
                        'placeholder' => 'Плейсхолдер',
                    ],
                ],
            ],
            'path' => 'translate.ru.placeholder',
            'value' => 'Плейсхолдер',
            'expected' => [
                'id' => 123,
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                    'ru' => [
                        'placeholder' => 'Плейсхолдер',
                    ],
                ],
            ],
            'isReturnsSameObject' => true,
        ];
    }

    /**
     * @param array<string, mixed> $reference
     * @param array<string, mixed> $original
     * @param array<string, mixed> $expected
     */
    #[DataProvider('dataSetSuccess')]
    public function testSetSuccess(
        array $reference,
        array $original,
        string $path,
        mixed $value,
        array $expected,
        bool $isReturnsSameObject,
    ): void {
        $referenceObject = new FluentArr($reference);
        $originalObject = new FluentArr($original);
        $expectedObject = new FluentArr($expected);

        $actualObject = $originalObject->set($path, $value);

        self::assertArraysAreIdentical($reference, $original, "Failed test path: {$path}");
        self::assertEquals($referenceObject, $originalObject, "Failed test path: {$path}");
        self::assertEquals($expectedObject, $actualObject, "Failed test path: {$path}");
        $isReturnsSameObject
            ? self::assertSame($originalObject, $actualObject, "Failed test path: {$path}")
            : self::assertNotSame($originalObject, $actualObject, "Failed test path: {$path}");
    }

    /**
     * @param array<string, mixed> $array
     */
    #[DataProviderExternal(ArrTest::class, 'dataPushFailed')]
    public function testPushFailed(array $array, string $path, mixed $value): void
    {
        $this->expectException(ArrException::class);

        new FluentArr($array)->push($path, $value);
    }

    /**
     * @param array<string, mixed> $reference
     * @param array<string, mixed> $original
     * @param array<string, mixed> $expected
     */
    #[DataProviderExternal(ArrTest::class, 'dataPushSuccess')]
    public function testPushSuccess(
        array $reference,
        array $original,
        string $path,
        mixed $value,
        array $expected,
    ): void {
        $referenceObject = new FluentArr($reference);
        $originalObject = new FluentArr($original);
        $expectedObject = new FluentArr($expected);

        $actualObject = $originalObject->push($path, $value);

        self::assertArraysAreIdentical($reference, $original, "Failed test path: {$path}");
        self::assertEquals($referenceObject, $originalObject, "Failed test path: {$path}");
        self::assertEquals($expectedObject, $actualObject, "Failed test path: {$path}");
    }

    /**
     * @param array<string, mixed> $reference
     * @param array<string, mixed> $original
     * @param array<string, mixed> $expected
     */
    #[DataProviderExternal(ArrTest::class, 'dataForget')]
    public function testForgetArraysIdentical(array $reference, array $original, string $path, array $expected): void
    {
        $originalObject = new FluentArr($original);

        $actualObject = $originalObject->forget($path);

        self::assertArraysAreIdentical($reference, $original, "Failed test path: {$path}");
        self::assertArraysAreIdentical($reference, $originalObject->toArray(), "Failed test path: {$path}");
        self::assertArraysAreIdentical($expected, $actualObject->toArray(), "Failed test path: {$path}");
    }

    /**
     * @return Iterator<array{reference: array<string, mixed>, original: array<string, mixed>, path: string}>
     */
    public static function dataForgetObjectsSame(): iterable
    {
        yield [
            'reference' => [],
            'original' => [],
            'path' => 'id',
        ];

        yield [
            'reference' => [
                'id' => 123,
            ],
            'original' => [
                'id' => 123,
            ],
            'path' => 'missing',
        ];
    }

    /**
     * @param array<string, mixed> $reference
     * @param array<string, mixed> $original
     */
    #[DataProvider('dataForgetObjectsSame')]
    public function testForgetObjectsSame(array $reference, array $original, string $path): void
    {
        $originalObject = new FluentArr($original);

        $actualObject = $originalObject->forget($path);

        self::assertArraysAreIdentical($reference, $original, "Failed test path: {$path}");
        self::assertArraysAreIdentical($reference, $originalObject->toArray(), "Failed test path: {$path}");
        self::assertSame($originalObject, $actualObject, "Failed test path: {$path}");
    }

    /**
     * @param array<string, mixed> $array
     */
    #[DataProviderExternal(ArrTest::class, 'dataIsEmpty')]
    public function testIsEmpty(array $array, bool $expected): void
    {
        $actual = new FluentArr($array)->isEmpty();

        self::assertSame($expected, $actual);
    }

    /**
     * @param array<string, mixed> $array
     */
    #[DataProviderExternal(ArrTest::class, 'dataIsEmpty')]
    public function testIsNotEmpty(array $array, bool $expected): void
    {
        $actual = new FluentArr($array)->isNotEmpty();

        self::assertSame(!$expected, $actual);
    }

    /**
     * @param array<string, mixed> $array
     */
    #[DataProviderExternal(ArrTest::class, 'dataFirst')]
    public function testFirst(array $array, mixed $default, mixed $expected): void
    {
        $actual = new FluentArr($array)->first($default);

        is_array($expected)
            ? self::assertEquals(new FluentArr($expected), $actual)
            : self::assertSame($expected, new FluentArr($array)->first($default));
    }

    /**
     * @param array<string, mixed> $array
     */
    #[DataProviderExternal(ArrTest::class, 'dataLast')]
    public function testLast(array $array, mixed $default, mixed $expected): void
    {
        $actual = new FluentArr($array)->last($default);

        is_array($expected)
            ? self::assertEquals(new FluentArr($expected), $actual)
            : self::assertSame($expected, new FluentArr($array)->last($default));
    }

    #[DataProviderExternal(ArrTest::class, 'dataHas')]
    public function testHas(mixed $value, bool $expected): void
    {
        $actual = new FluentArr(ArrTest::TEST_ARRAY)->has($value);

        self::assertSame($expected, $actual, "Failed test value: {$value}");
    }

    #[DataProviderExternal(ArrTest::class, 'dataHasKey')]
    public function testHasKey(mixed $key, bool $expected): void
    {
        $actual = new FluentArr(ArrTest::TEST_ARRAY)->hasKey($key);

        self::assertSame($expected, $actual, "Failed test key: {$key}");
    }

    #[DataProviderExternal(DotNotationPathTest::class, 'dataValidateOrThrowExceptionFailed')]
    public function testHasPathFailed(string $path): void
    {
        $this->expectException(ArrException::class);

        new FluentArr(ArrTest::TEST_ARRAY)->hasPath($path);
    }

    #[DataProviderExternal(ArrTest::class, 'dataHasPathSuccess')]
    public function testHasPathSuccess(string $path, bool $expected): void
    {
        $actual = new FluentArr(ArrTest::TEST_ARRAY)->hasPath($path);

        self::assertSame($expected, $actual, "Failed test path: {$path}");
    }

    /**
     * @param string[] $array
     */
    #[DataProviderExternal(ArrTest::class, 'dataJoin')]
    public function testJoin(array $array, string $separator, string $expected): void
    {
        $actual = new FluentArr($array)->join($separator);

        self::assertSame($expected, $actual);
    }

    /**
     * @param array<string, mixed> $array
     * @param array<string, mixed> $expected
     */
    #[DataProviderExternal(ArrTest::class, 'dataFilter')]
    public function testFilter(array $array, ?callable $callback, int $mode, array $expected): void
    {
        $actual = new FluentArr($array)->filter($callback, $mode);

        self::assertEquals(new FluentArr($expected), $actual);
    }

    /**
     * @param int[] $array
     * @param int[] $expected
     */
    #[DataProviderExternal(ArrTest::class, 'dataMap')]
    public function testMap(array $array, ?callable $callback, array $expected): void
    {
        $actual = new FluentArr($array)->map($callback);

        self::assertEquals(new FluentArr($expected), $actual);
    }

    /**
     * @param int[] $array
     */
    #[DataProviderExternal(ArrTest::class, 'dataAll')]
    public function testAll(array $array, callable $callback, bool $expected): void
    {
        $actual = new FluentArr($array)->all($callback);

        self::assertSame($expected, $actual);
    }

    /**
     * @param int[] $array
     */
    #[DataProviderExternal(ArrTest::class, 'dataAny')]
    public function testAny(array $array, callable $callback, bool $expected): void
    {
        $actual = new FluentArr($array)->any($callback);

        self::assertSame($expected, $actual);
    }

    /**
     * @param string[] $keys
     * @param array<string, mixed> $expected
     */
    #[DataProviderExternal(ArrTest::class, 'dataOnly')]
    public function testOnly(array $keys, array $expected): void
    {
        $actual = new FluentArr(ArrTest::TEST_ARRAY)->only($keys);

        self::assertEquals(new FluentArr($expected), $actual);
    }

    /**
     * @param string[] $keys
     * @param array<string, mixed> $expected
     */
    #[DataProviderExternal(ArrTest::class, 'dataExcept')]
    public function testExcept(array $keys, array $expected): void
    {
        $actual = new FluentArr(ArrTest::TEST_ARRAY)->except($keys);

        self::assertEquals(new FluentArr($expected), $actual);
    }

    /**
     * @param array<string, mixed> $array
     * @param string[] $expected
     */
    #[DataProviderExternal(ArrTest::class, 'dataPluck')]
    public function testPluck(array $array, string $path, array $expected): void
    {
        $actual = new FluentArr($array)->pluck($path);

        self::assertEquals(new FluentArr($expected), $actual);
    }

    public function testToArray(): void
    {
        $actual = new FluentArr(ArrTest::TEST_ARRAY)->toArray();

        self::assertArraysAreIdentical(ArrTest::TEST_ARRAY, $actual);
    }
}
