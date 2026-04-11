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
use SergeYakovlev\Support\Arr\Arr;
use SergeYakovlev\Support\Arr\ArrException;
use SergeYakovlev\Support\Arr\FluentArr;

final class ArrTest extends TestCase
{
    public const array TEST_ARRAY = [
        'id' => 123,
        'is_activated' => true,
        'name' => 'Username',
        'null' => null,
        'rate' => 12.34,
        'translate' => [
            'en' => [
                'placeholder' => 'Placeholder',
            ],
            'ru' => [
                'placeholder' => 'Плейсхолдер',
            ],
        ],
    ];

    #[DataProviderExternal(DotNotationPathTest::class, 'dataValidateOrThrowExceptionFailed')]
    public function testGetFailed(string $path): void
    {
        $this->expectException(ArrException::class);

        Arr::get(self::TEST_ARRAY, $path);
    }

    /**
     * @return Iterator<array{path: string, default: string|null, expected: mixed}>
     */
    public static function dataGetSuccess(): iterable
    {
        yield [
            'path' => 'id',
            'default' => null,
            'expected' => 123,
        ];

        yield [
            'path' => 'name',
            'default' => null,
            'expected' => 'Username',
        ];

        yield [
            'path' => 'translate',
            'default' => null,
            'expected' => [
                'en' => [
                    'placeholder' => 'Placeholder',
                ],
                'ru' => [
                    'placeholder' => 'Плейсхолдер',
                ],
            ],
        ];

        yield [
            'path' => 'translate.ru.placeholder',
            'default' => null,
            'expected' => 'Плейсхолдер',
        ];

        yield [
            'path' => 'translate.ru.missing',
            'default' => 'DEFAULT VALUE',
            'expected' => 'DEFAULT VALUE',
        ];

        yield [
            'path' => 'translate.ru.placeholder.missing',
            'default' => 'DEFAULT VALUE',
            'expected' => 'DEFAULT VALUE',
        ];

        yield [
            'path' => 'translate.ru.placeholder.missing.another_missing',
            'default' => 'DEFAULT VALUE',
            'expected' => 'DEFAULT VALUE',
        ];

        yield [
            'path' => 'null',
            'default' => 'DEFAULT VALUE',
            'expected' => null,
        ];
    }

    #[DataProvider('dataGetSuccess')]
    public function testGetSuccess(string $path, mixed $default, mixed $expected): void
    {
        $actual = Arr::get(self::TEST_ARRAY, $path, $default);

        is_array($expected)
            ? self::assertArraysAreIdentical($expected, $actual, "Failed test path: {$path}")
            : self::assertSame($expected, $actual, "Failed test path: {$path}");
    }

    public function testGetArrayFailed(): void
    {
        $this->expectException(ArrException::class);

        Arr::getArray(self::TEST_ARRAY, 'id', []);
    }

    /**
     * @return Iterator<array{path: string, expected: array<string, mixed>}>
     */
    public static function dataGetArraySuccess(): iterable
    {
        yield [
            'path' => 'translate',
            'expected' => self::TEST_ARRAY['translate'],
        ];

        yield [
            'path' => 'missing',
            'expected' => [],
        ];
    }

    /**
     * @param array<string, mixed> $expected
     */
    #[DataProvider('dataGetArraySuccess')]
    public function testGetArraySuccess(string $path, array $expected): void
    {
        $actual = Arr::getArray(self::TEST_ARRAY, $path, []);

        self::assertArraysAreIdentical($expected, $actual, "Failed test path: {$path}");
    }

    public function testGetBooleanFailed(): void
    {
        $this->expectException(ArrException::class);

        Arr::getBoolean(self::TEST_ARRAY, 'id', false);
    }

    /**
     * @return Iterator<array{path: string, expected: boolean}>
     */
    public static function dataGetBooleanSuccess(): iterable
    {
        yield [
            'path' => 'is_activated',
            'expected' => true,
        ];

        yield [
            'path' => 'missing',
            'expected' => false,
        ];
    }

    #[DataProvider('dataGetBooleanSuccess')]
    public function testGetBooleanSuccess(string $path, bool $expected): void
    {
        $actual = Arr::getBoolean(self::TEST_ARRAY, $path, false);

        self::assertSame($expected, $actual, "Failed test path: {$path}");
    }

    public function testGetFloatFailed(): void
    {
        $this->expectException(ArrException::class);

        Arr::getFloat(self::TEST_ARRAY, 'name', 0.0);
    }

    /**
     * @return Iterator<array{path: string, expected: float}>
     */
    public static function dataGetFloatSuccess(): iterable
    {
        yield [
            'path' => 'id',
            'expected' => 123.0,
        ];

        yield [
            'path' => 'rate',
            'expected' => 12.34,
        ];

        yield [
            'path' => 'missing',
            'expected' => 0.0,
        ];
    }

    #[DataProvider('dataGetFloatSuccess')]
    public function testGetFloatSuccess(string $path, float $expected): void
    {
        $actual = Arr::getFloat(self::TEST_ARRAY, $path, 0.0);

        self::assertSame($expected, $actual, "Failed test path: {$path}");
    }

    public function testGetIntegerFailed(): void
    {
        $this->expectException(ArrException::class);

        Arr::getInteger(self::TEST_ARRAY, 'rate', 0);
    }

    /**
     * @return Iterator<array{path: string, expected: integer}>
     */
    public static function dataGetIntegerSuccess(): iterable
    {
        yield [
            'path' => 'id',
            'expected' => 123,
        ];

        yield [
            'path' => 'missing',
            'expected' => 0,
        ];
    }

    #[DataProvider('dataGetIntegerSuccess')]
    public function testGetIntegerSuccess(string $path, int $expected): void
    {
        $actual = Arr::getInteger(self::TEST_ARRAY, $path, 0);

        self::assertSame($expected, $actual, "Failed test path: {$path}");
    }

    public function testGetStringFailed(): void
    {
        $this->expectException(ArrException::class);

        Arr::getString(self::TEST_ARRAY, 'id', '');
    }

    /**
     * @return Iterator<array{path: string, expected: string}>
     */
    public static function dataGetStringSuccess(): iterable
    {
        yield [
            'path' => 'name',
            'expected' => 'Username',
        ];

        yield [
            'path' => 'missing',
            'expected' => '',
        ];
    }

    #[DataProvider('dataGetStringSuccess')]
    public function testGetStringSuccess(string $path, string $expected): void
    {
        $actual = Arr::getString(self::TEST_ARRAY, $path, '');

        self::assertSame($expected, $actual, "Failed test path: {$path}");
    }

    /**
     * @return Iterator<array{array: array<string, mixed>, path: string, value: string}>
     */
    public static function dataSetFailed(): iterable
    {
        yield [
            'array' => [
                'some' => 'VALUE',
            ],
            'path' => 'some.path.to.var',
            'value' => 'NEW VALUE',
        ];

        yield [
            'array' => [
                'some' => [
                    'path' => 'VALUE',
                ],
            ],
            'path' => 'some.path.to.var',
            'value' => 'NEW VALUE',
        ];

        yield [
            'array' => [
                'some' => [
                    'path' => [
                        'to' => 'VALUE',
                    ],
                ],
            ],
            'path' => 'some.path.to.var',
            'value' => 'NEW VALUE',
        ];
    }

    /**
     * @param array<string, mixed> $array
     */
    #[DataProvider('dataSetFailed')]
    public function testSetFailed(array $array, string $path, mixed $value): void
    {
        $this->expectException(ArrException::class);

        Arr::set($array, $path, $value);
    }

    /**
     * @param array<string, mixed> $reference
     * @param array<string, mixed> $original
     * @param array<string, mixed> $expected
     */
    #[DataProviderExternal(FluentArrTest::class, 'dataSetSuccess')]
    public function testSetSuccess(
        array $reference,
        array $original,
        string $path,
        mixed $value,
        array $expected,
        bool $isReturnsSameObject,
    ): void {
        $actual = Arr::set($original, $path, $value);

        self::assertArraysAreIdentical($reference, $original, "Failed test path: {$path}");
        self::assertArraysAreIdentical($expected, $actual, "Failed test path: {$path}");
    }

    /**
     * @return Iterator<array{array: array<string, mixed>, path: string, value: string}>
     */
    public static function dataPushFailed(): iterable
    {
        yield [
            'array' => [
                'some' => 'VALUE',
            ],
            'path' => 'some.path.to.var',
            'value' => 'NEW VALUE',
        ];

        yield [
            'array' => [
                'some' => [
                    'path' => 'VALUE',
                ],
            ],
            'path' => 'some.path.to.var',
            'value' => 'NEW VALUE',
        ];

        yield [
            'array' => [
                'some' => [
                    'path' => [
                        'to' => 'VALUE',
                    ],
                ],
            ],
            'path' => 'some.path.to.var',
            'value' => 'NEW VALUE',
        ];

        yield [
            'array' => [
                'some' => [
                    'path' => [
                        'to' => [
                            'var' => 'VALUE',
                        ],
                    ],
                ],
            ],
            'path' => 'some.path.to.var',
            'value' => 'NEW VALUE',
        ];
    }

    /**
     * @param array<string, mixed> $array
     */
    #[DataProvider('dataPushFailed')]
    public function testPushFailed(array $array, string $path, mixed $value): void
    {
        $this->expectException(ArrException::class);

        Arr::push($array, $path, $value);
    }

    /**
     * @return Iterator<array{
     *     reference: array<string, mixed>,
     *     original: array<string, mixed>,
     *     path: string,
     *     value: string,
     *     expected: array<string, mixed>,
     * }>
     */
    public static function dataPushSuccess(): iterable
    {
        yield [
            'reference' => [],
            'original' => [],
            'path' => 'some.path.to.var',
            'value' => 'VALUE',
            'expected' => [
                'some' => [
                    'path' => [
                        'to' => [
                            'var' => [
                                'VALUE',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield [
            'reference' => [
                'some' => [
                    'path' => [
                        'to' => [
                            'var' => [
                                'VALUE 1',
                            ],
                        ],
                    ],
                ],
            ],
            'original' => [
                'some' => [
                    'path' => [
                        'to' => [
                            'var' => [
                                'VALUE 1',
                            ],
                        ],
                    ],
                ],
            ],
            'path' => 'some.path.to.var',
            'value' => 'VALUE 2',
            'expected' => [
                'some' => [
                    'path' => [
                        'to' => [
                            'var' => [
                                'VALUE 1',
                                'VALUE 2',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $reference
     * @param array<string, mixed> $original
     * @param array<string, mixed> $expected
     */
    #[DataProvider('dataPushSuccess')]
    public function testPushSuccess(
        array $reference,
        array $original,
        string $path,
        mixed $value,
        array $expected,
    ): void {
        $actual = Arr::push($original, $path, $value);

        self::assertArraysAreIdentical($reference, $original, "Failed test path: {$path}");
        self::assertArraysAreIdentical($expected, $actual, "Failed test path: {$path}");
    }

    /**
     * @return Iterator<array{
     *     reference: array<string, mixed>,
     *     original: array<string, mixed>,
     *     path: string,
     *     expected: array<string, mixed>,
     * }>
     */
    public static function dataForget(): iterable
    {
        yield [
            'reference' => [],
            'original' => [],
            'path' => 'id',
            'expected' => [],
        ];

        yield [
            'reference' => [
                'id' => 123,
            ],
            'original' => [
                'id' => 123,
            ],
            'path' => 'missing',
            'expected' => [
                'id' => 123,
            ],
        ];

        yield [
            'reference' => [
                'id' => 123,
            ],
            'original' => [
                'id' => 123,
            ],
            'path' => 'id',
            'expected' => [],
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
            'path' => 'id',
            'expected' => [
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                    'ru' => [
                        'placeholder' => 'Плейсхолдер',
                    ],
                ],
            ],
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
            'path' => 'translate',
            'expected' => [
                'id' => 123,
            ],
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
            'path' => 'translate.ru',
            'expected' => [
                'id' => 123,
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                ],
            ],
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
            'expected' => [
                'id' => 123,
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                    'ru' => [],
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $reference
     * @param array<string, mixed> $original
     * @param array<string, mixed> $expected
     */
    #[DataProvider('dataForget')]
    public function testForget(array $reference, array $original, string $path, array $expected): void
    {
        $actual = Arr::forget($original, $path);

        self::assertArraysAreIdentical($reference, $original, "Failed test path: {$path}");
        self::assertArraysAreIdentical($expected, $actual, "Failed test path: {$path}");
    }

    /**
     * @return Iterator<array{array: array<string, mixed>, expected: boolean}>
     */
    public static function dataIsEmpty(): iterable
    {
        yield [
            'array' => [],
            'expected' => true,
        ];

        yield [
            'array' => self::TEST_ARRAY,
            'expected' => false,
        ];
    }

    /**
     * @param array<string, mixed> $array
     */
    #[DataProvider('dataIsEmpty')]
    public function testIsEmpty(array $array, bool $expected): void
    {
        $actual = Arr::isEmpty($array);

        self::assertSame($expected, $actual);
    }

    /**
     * @param array<string, mixed> $array
     */
    #[DataProvider('dataIsEmpty')]
    public function testIsNotEmpty(array $array, bool $expected): void
    {
        $actual = Arr::isNotEmpty($array);

        self::assertSame(!$expected, $actual);
    }

    /**
     * @return Iterator<array{array: array<string, mixed>, default: string, expected: mixed}>
     */
    public static function dataFirst(): iterable
    {
        yield [
            'array' => [],
            'default' => 'DEFAULT VALUE',
            'expected' => 'DEFAULT VALUE',
        ];

        yield [
            'array' => [
                'first_item' => 123,
                'last_item' => 456,
            ],
            'default' => 'DEFAULT VALUE',
            'expected' => 123,
        ];

        yield [
            'array' => [
                'first_item' => [
                    'key1' => [
                        'VALUE1',
                    ],
                    'key2' => [
                        'VALUE2',
                    ],
                ],
                'last_item' => 'LAST ITEM VALUE',
            ],
            'default' => 'DEFAULT VALUE',
            'expected' => [
                'key1' => [
                    'VALUE1',
                ],
                'key2' => [
                    'VALUE2',
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $array
     */
    #[DataProvider('dataFirst')]
    public function testFirst(array $array, mixed $default, mixed $expected): void
    {
        $actual = Arr::first($array, $default);

        is_array($expected)
            ? self::assertArraysAreIdentical($expected, $actual)
            : self::assertEquals($expected, $actual);
    }

    /**
     * @return Iterator<array{array: array<string, mixed>, default: string, expected: mixed}>
     */
    public static function dataLast(): iterable
    {
        yield [
            'array' => [],
            'default' => 'DEFAULT VALUE',
            'expected' => 'DEFAULT VALUE',
        ];

        yield [
            'array' => [
                'first_item' => 123,
                'last_item' => 456,
            ],
            'default' => 'DEFAULT VALUE',
            'expected' => 456,
        ];

        yield [
            'array' => [
                'first_item' => 'FIRST ITEM VALUE',
                'last_item' => [
                    'key1' => [
                        'VALUE1',
                    ],
                    'key2' => [
                        'VALUE2',
                    ],
                ],
            ],
            'default' => 'DEFAULT VALUE',
            'expected' => [
                'key1' => [
                    'VALUE1',
                ],
                'key2' => [
                    'VALUE2',
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $array
     */
    #[DataProvider('dataLast')]
    public function testLast(array $array, mixed $default, mixed $expected): void
    {
        $actual = Arr::last($array, $default);

        is_array($expected)
            ? self::assertArraysAreIdentical($expected, $actual)
            : self::assertEquals($expected, $actual);
    }

    /**
     * @return Iterator<array{value: integer, expected: boolean}>
     */
    public static function dataHas(): iterable
    {
        yield [
            'value' => 123,
            'expected' => true,
        ];

        yield [
            'value' => 456,
            'expected' => false,
        ];
    }

    #[DataProvider('dataHas')]
    public function testHas(int $value, bool $expected): void
    {
        $actual = Arr::has(self::TEST_ARRAY, $value);

        self::assertSame($expected, $actual, "Failed test value: {$value}");
    }

    /**
     * @return Iterator<array{key: string, expected: boolean}>
     */
    public static function dataHasKey(): iterable
    {
        yield [
            'key' => 'id',
            'expected' => true,
        ];

        yield [
            'key' => 'missing',
            'expected' => false,
        ];
    }

    #[DataProvider('dataHasKey')]
    public function testHasKey(string $key, bool $expected): void
    {
        $actual = Arr::hasKey(self::TEST_ARRAY, $key);

        self::assertSame($expected, $actual, "Failed test key: {$key}");
    }

    #[DataProviderExternal(DotNotationPathTest::class, 'dataValidateOrThrowExceptionFailed')]
    public function testHasPathFailed(string $path): void
    {
        $this->expectException(ArrException::class);

        Arr::hasPath(self::TEST_ARRAY, $path);
    }

    /**
     * @return Iterator<array{path: string, expected: boolean}>
     */
    public static function dataHasPathSuccess(): iterable
    {
        yield [
            'path' => 'id',
            'expected' => true,
        ];

        yield [
            'path' => 'translate',
            'expected' => true,
        ];

        yield [
            'path' => 'translate.ru',
            'expected' => true,
        ];

        yield [
            'path' => 'translate.ru.placeholder',
            'expected' => true,
        ];

        yield [
            'path' => 'translate.ru.placeholder.missing',
            'expected' => false,
        ];

        yield [
            'path' => 'translate.ru.placeholder.missing.another_missing',
            'expected' => false,
        ];

        yield [
            'path' => 'missing',
            'expected' => false,
        ];
    }

    #[DataProvider('dataHasPathSuccess')]
    public function testHasPathSuccess(string $path, bool $expected): void
    {
        $actual = Arr::hasPath(self::TEST_ARRAY, $path);

        self::assertSame($expected, $actual, "Failed test path: {$path}");
    }

    /**
     * @return Iterator<array{array: string[], separator: string, expected: string}>
     */
    public static function dataJoin(): iterable
    {
        yield [
            'array' => [
                'some',
                'path',
                'to',
                'file',
            ],
            'separator' => '/',
            'expected' => 'some/path/to/file',
        ];
    }

    /**
     * @param string[] $array
     */
    #[DataProvider('dataJoin')]
    public function testJoin(array $array, string $separator, string $expected): void
    {
        $actual = Arr::join($array, $separator);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{
     *     array: array<string, mixed>,
     *     callback: callable|null,
     *     mode: integer,
     *     expected: array<string, mixed>,
     * }>
     */
    public static function dataFilter(): iterable
    {
        yield [
            'array' => [
                'id' => 0,
                'is_activated' => false,
                'name' => '',
                'rate' => 12.34,
            ],
            'callback' => null,
            'mode' => 0,
            'expected' => [
                'rate' => 12.34,
            ],
        ];

        yield [
            'array' => self::TEST_ARRAY,
            'callback' => is_float(...),
            'mode' => 0,
            'expected' => [
                'rate' => 12.34,
            ],
        ];

        yield [
            'array' => self::TEST_ARRAY,
            'callback' => static fn($k): bool => $k === 'rate',
            'mode' => ARRAY_FILTER_USE_KEY,
            'expected' => [
                'rate' => 12.34,
            ],
        ];

        yield [
            'array' => self::TEST_ARRAY,
            'callback' => static fn($v, $k): bool => $k === 'rate' && $v === 12.34,
            'mode' => ARRAY_FILTER_USE_BOTH,
            'expected' => [
                'rate' => 12.34,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $array
     * @param array<string, mixed> $expected
     */
    #[DataProvider('dataFilter')]
    public function testFilter(array $array, ?callable $callback, int $mode, array $expected): void
    {
        $actual = Arr::filter($array, $callback, $mode);

        self::assertArraysAreIdentical($expected, $actual);
    }

    /**
     * @return Iterator<array{array: integer[], callback: callable, expected: integer[]}>
     */
    public static function dataMap(): iterable
    {
        yield [
            'array' => [1, 2, 3],
            'callback' => static fn($v): int => $v * 2,
            'expected' => [2, 4, 6],
        ];
    }

    /**
     * @param int[] $array
     * @param int[] $expected
     */
    #[DataProvider('dataMap')]
    public function testMap(array $array, ?callable $callback, array $expected): void
    {
        $actual = Arr::map($array, $callback);

        self::assertArraysAreIdentical($expected, $actual);
    }

    /**
     * @return Iterator<array{array: integer[], callback: callable, expected: boolean}>
     */
    public static function dataAll(): iterable
    {
        yield [
            'array' => [1, 2, 3],
            'callback' => static fn($v): bool => $v % 2 === 0,
            'expected' => false,
        ];

        yield [
            'array' => [2, 4, 6],
            'callback' => static fn($v): bool => $v % 2 === 0,
            'expected' => true,
        ];
    }

    /**
     * @param int[] $array
     */
    #[DataProvider('dataAll')]
    public function testAll(array $array, callable $callback, bool $expected): void
    {
        $actual = Arr::all($array, $callback);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{array: integer[], callback: callable, expected: boolean}>
     */
    public static function dataAny(): iterable
    {
        yield [
            'array' => [1, 3],
            'callback' => static fn($v): bool => $v % 2 === 0,
            'expected' => false,
        ];

        yield [
            'array' => [1, 2, 3],
            'callback' => static fn($v): bool => $v % 2 === 0,
            'expected' => true,
        ];
    }

    /**
     * @param int[] $array
     */
    #[DataProvider('dataAny')]
    public function testAny(array $array, callable $callback, bool $expected): void
    {
        $actual = Arr::any($array, $callback);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{keys: string[], expected: array<string, mixed>}>
     */
    public static function dataOnly(): iterable
    {
        yield [
            'keys' => [
                'id',
                'name',
            ],
            'expected' => [
                'id' => 123,
                'name' => 'Username',
            ],
        ];
    }

    /**
     * @param string[] $keys
     * @param array<string, mixed> $expected
     */
    #[DataProvider('dataOnly')]
    public function testOnly(array $keys, array $expected): void
    {
        $actual = Arr::only(self::TEST_ARRAY, $keys);

        self::assertArraysAreIdentical($expected, $actual);
    }

    /**
     * @return Iterator<array{keys: string[], expected: array<string, mixed>}>
     */
    public static function dataExcept(): iterable
    {
        yield [
            'keys' => [
                'null',
                'translate',
            ],
            'expected' => [
                'id' => 123,
                'is_activated' => true,
                'name' => 'Username',
                'rate' => 12.34,
            ],
        ];
    }

    /**
     * @param string[] $keys
     * @param array<string, mixed> $expected
     */
    #[DataProvider('dataExcept')]
    public function testExcept(array $keys, array $expected): void
    {
        $actual = Arr::except(self::TEST_ARRAY, $keys);

        self::assertArraysAreIdentical($expected, $actual);
    }

    /**
     * @return Iterator<array{array: array<string, mixed>[], path: string, expected: string[]}>
     */
    public static function dataPluck(): iterable
    {
        yield [
            'array' => [
                [
                    'developer' => [
                        'id' => 1,
                        'name' => 'Taylor',
                    ],
                ],
                [
                    'developer' => [
                        'id' => 2,
                        'name' => 'Abigail',
                    ],
                ],
            ],
            'path' => 'developer.name',
            'expected' => ['Taylor', 'Abigail'],
        ];
    }

    /**
     * @param array<string, mixed>[] $array
     * @param string[] $expected
     */
    #[DataProvider('dataPluck')]
    public function testPluck(array $array, string $path, array $expected): void
    {
        $actual = Arr::pluck($array, $path);

        self::assertArraysAreIdentical($expected, $actual);
    }

    public function testOf(): void
    {
        $expected = new FluentArr(self::TEST_ARRAY);

        $actual = Arr::of(self::TEST_ARRAY);

        self::assertEquals($expected, $actual);
    }
}
