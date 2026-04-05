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

namespace SergeYakovlev\Support\Tests\DotNotation;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use SergeYakovlev\Support\DotNotation\DotNotationException;
use SergeYakovlev\Support\DotNotation\DotNotationPath;

use function SergeYakovlev\Support\DotNotation\dot_notation_array_forget;
use function SergeYakovlev\Support\DotNotation\dot_notation_array_get;
use function SergeYakovlev\Support\DotNotation\dot_notation_array_has_path;
use function SergeYakovlev\Support\DotNotation\dot_notation_array_push;
use function SergeYakovlev\Support\DotNotation\dot_notation_array_set;

final class DotNotationArrayFunctionsTest extends TestCase
{
    private const array TEST_ARRAY = [
        'id' => 123,
        'is_activated' => true,
        'name' => 'Username',
        'null' => null,
        'zero' => 0,
        'false' => false,
        'empty_string' => '',
        'empty_array' => [],
        'rate' => 12.34,
        'list' => ['first', 'second', 'third'],
        'translate' => [
            'en' => [
                'placeholder' => 'Placeholder',
            ],
            'ru' => [
                'placeholder' => 'Плейсхолдер',
            ],
        ],
    ];

    // dot_notation_array_get()

    #[DataProviderExternal(DotNotationPathTest::class, 'dataValidateOrThrowExceptionFailed')]
    public function testGetFailedBecausePathIsInvalid(string $path): void
    {
        $this->expectException(DotNotationException::class);

        dot_notation_array_get(self::TEST_ARRAY, $path);
    }

    /**
     * @return Iterator<array{path: string, default: mixed, expected: mixed}>
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
            'path' => 'translate.ru',
            'default' => null,
            'expected' => [
                'placeholder' => 'Плейсхолдер',
            ],
        ];

        yield [
            'path' => 'translate.ru.placeholder',
            'default' => null,
            'expected' => 'Плейсхолдер',
        ];

        yield [
            'path' => 'list.1',
            'default' => null,
            'expected' => 'second',
        ];

        // The default is returned only if the path does not exist
        yield [
            'path' => 'missing',
            'default' => null,
            'expected' => null,
        ];

        yield [
            'path' => 'missing',
            'default' => 'DEFAULT VALUE',
            'expected' => 'DEFAULT VALUE',
        ];

        yield [
            'path' => 'translate.ru.missing',
            'default' => 'DEFAULT VALUE',
            'expected' => 'DEFAULT VALUE',
        ];

        yield [
            'path' => 'translate.fr.placeholder',
            'default' => 'DEFAULT VALUE',
            'expected' => 'DEFAULT VALUE',
        ];

        yield [
            'path' => 'missing',
            'default' => ['DEFAULT VALUE'],
            'expected' => ['DEFAULT VALUE'],
        ];

        // Existing falsy values are returned as is, the default does not replace them
        yield [
            'path' => 'null',
            'default' => 'DEFAULT VALUE',
            'expected' => null,
        ];

        yield [
            'path' => 'zero',
            'default' => 'DEFAULT VALUE',
            'expected' => 0,
        ];

        yield [
            'path' => 'false',
            'default' => 'DEFAULT VALUE',
            'expected' => false,
        ];

        yield [
            'path' => 'empty_string',
            'default' => 'DEFAULT VALUE',
            'expected' => '',
        ];

        yield [
            'path' => 'empty_array',
            'default' => 'DEFAULT VALUE',
            'expected' => [],
        ];
    }

    #[DataProvider('dataGetSuccess')]
    public function testGetSuccess(string $path, mixed $default, mixed $expected): void
    {
        $actual = dot_notation_array_get(self::TEST_ARRAY, $path, $default);

        is_array($expected)
            ? self::assertArraysAreIdentical($expected, $actual, "Failed test path: {$path}")
            : self::assertSame($expected, $actual, "Failed test path: {$path}");
    }

    public function testGetSuccessWithDotNotationPath(): void
    {
        $actual = dot_notation_array_get(self::TEST_ARRAY, new DotNotationPath('translate.ru.placeholder'));

        self::assertSame('Плейсхолдер', $actual);
    }

    public function testGetSuccessFromEmptyArray(): void
    {
        $actual = dot_notation_array_get([], 'translate.ru.placeholder', 'DEFAULT VALUE');

        self::assertSame('DEFAULT VALUE', $actual);
    }

    /**
     * A parent segment that is not an array (a string, a number, null, etc.) cannot contain the requested element,
     * so the path does not exist.
     *
     * @return Iterator<array{path: string}>
     */
    public static function dataParentIsNotArray(): iterable
    {
        yield [
            'path' => 'id.missing',
        ];

        yield [
            'path' => 'is_activated.missing',
        ];

        yield [
            'path' => 'null.missing',
        ];

        yield [
            'path' => 'list.0.missing',
        ];

        yield [
            'path' => 'translate.ru.placeholder.missing',
        ];

        yield [
            'path' => 'translate.ru.placeholder.missing.another_missing',
        ];
    }

    #[DataProvider('dataParentIsNotArray')]
    public function testGetReturnsDefaultIfParentIsNotArray(string $path): void
    {
        $actual = dot_notation_array_get(self::TEST_ARRAY, $path, 'DEFAULT VALUE');

        self::assertSame('DEFAULT VALUE', $actual, "Failed test path: {$path}");
    }

    // dot_notation_array_set()

    #[DataProviderExternal(DotNotationPathTest::class, 'dataValidateOrThrowExceptionFailed')]
    public function testSetFailedBecausePathIsInvalid(string $path): void
    {
        $this->expectException(DotNotationException::class);

        dot_notation_array_set(self::TEST_ARRAY, $path, 'NEW VALUE');
    }

    /**
     * @return Iterator<array{array: array<string, mixed>, path: string}>
     */
    public static function dataSetFailedBecauseParentIsNotArray(): iterable
    {
        yield [
            'array' => [
                'some' => 'VALUE',
            ],
            'path' => 'some.path.to.var',
        ];

        yield [
            'array' => [
                'some' => [
                    'path' => 'VALUE',
                ],
            ],
            'path' => 'some.path.to.var',
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
        ];
    }

    /**
     * @param array<string, mixed> $array
     */
    #[DataProvider('dataSetFailedBecauseParentIsNotArray')]
    public function testSetFailedBecauseParentIsNotArray(array $array, string $path): void
    {
        $this->expectException(DotNotationException::class);

        dot_notation_array_set($array, $path, 'NEW VALUE');
    }

    /**
     * @return Iterator<array{array: array<string, mixed>, path: string, value: mixed, expected: array<string, mixed>}>
     */
    public static function dataSetSuccess(): iterable
    {
        yield [
            'array' => [],
            'path' => 'id',
            'value' => 123,
            'expected' => [
                'id' => 123,
            ],
        ];

        yield [
            'array' => [],
            'path' => 'translate.ru.placeholder',
            'value' => 'Плейсхолдер',
            'expected' => [
                'translate' => [
                    'ru' => [
                        'placeholder' => 'Плейсхолдер',
                    ],
                ],
            ],
        ];

        // Creating a missing branch next to an existing one
        yield [
            'array' => [
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
        ];

        // Replacing an existing value
        yield [
            'array' => [
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
        ];

        // Setting the same value changes nothing
        yield [
            'array' => [
                'id' => 123,
                'translate' => [
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
                    'ru' => [
                        'placeholder' => 'Плейсхолдер',
                    ],
                ],
            ],
        ];

        // The last segment may replace a whole branch, and any value (including null and an array) can be set
        yield [
            'array' => [
                'translate' => [
                    'ru' => [
                        'placeholder' => 'Плейсхолдер',
                    ],
                ],
            ],
            'path' => 'translate.ru',
            'value' => 'RU',
            'expected' => [
                'translate' => [
                    'ru' => 'RU',
                ],
            ],
        ];

        yield [
            'array' => [
                'translate' => 'TRANSLATE',
            ],
            'path' => 'translate',
            'value' => [
                'ru' => 'RU',
            ],
            'expected' => [
                'translate' => [
                    'ru' => 'RU',
                ],
            ],
        ];

        yield [
            'array' => [],
            'path' => 'some.path',
            'value' => null,
            'expected' => [
                'some' => [
                    'path' => null,
                ],
            ],
        ];

        // Numeric segments are array keys
        yield [
            'array' => [],
            'path' => 'list.0',
            'value' => 'first',
            'expected' => [
                'list' => [
                    0 => 'first',
                ],
            ],
        ];

        yield [
            'array' => [
                'list' => ['first', 'second'],
            ],
            'path' => 'list.1',
            'value' => 'SECOND',
            'expected' => [
                'list' => ['first', 'SECOND'],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $array
     * @param array<string, mixed> $expected
     */
    #[DataProvider('dataSetSuccess')]
    public function testSetSuccess(array $array, string $path, mixed $value, array $expected): void
    {
        $reference = $array;

        $actual = dot_notation_array_set($array, $path, $value);

        self::assertArraysAreIdentical($reference, $array, "The original array was changed, test path: {$path}");
        self::assertArraysAreIdentical($expected, $actual, "Failed test path: {$path}");
    }

    public function testSetSuccessWithDotNotationPath(): void
    {
        $actual = dot_notation_array_set([], new DotNotationPath('some.path'), 'VALUE');

        self::assertArraysAreIdentical([
            'some' => [
                'path' => 'VALUE',
            ],
        ], $actual);
    }

    // dot_notation_array_push()

    #[DataProviderExternal(DotNotationPathTest::class, 'dataValidateOrThrowExceptionFailed')]
    public function testPushFailedBecausePathIsInvalid(string $path): void
    {
        $this->expectException(DotNotationException::class);

        dot_notation_array_push(self::TEST_ARRAY, $path, 'NEW VALUE');
    }

    /**
     * @return Iterator<array{array: array<string, mixed>, path: string}>
     */
    public static function dataPushFailedBecauseTargetOrParentIsNotArray(): iterable
    {
        yield [
            'array' => [
                'some' => 'VALUE',
            ],
            'path' => 'some.path.to.var',
        ];

        yield [
            'array' => [
                'some' => [
                    'path' => 'VALUE',
                ],
            ],
            'path' => 'some.path.to.var',
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
        ];

        // The target of the push itself is not an array
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
        ];

        yield [
            'array' => [
                'null' => null,
            ],
            'path' => 'null',
        ];
    }

    /**
     * @param array<string, mixed> $array
     */
    #[DataProvider('dataPushFailedBecauseTargetOrParentIsNotArray')]
    public function testPushFailedBecauseTargetOrParentIsNotArray(array $array, string $path): void
    {
        $this->expectException(DotNotationException::class);

        dot_notation_array_push($array, $path, 'NEW VALUE');
    }

    /**
     * @return Iterator<array{array: array<string, mixed>, path: string, value: mixed, expected: array<string, mixed>}>
     */
    public static function dataPushSuccess(): iterable
    {
        // The missing branch and the target array are created
        yield [
            'array' => [],
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
            'array' => [
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

        yield [
            'array' => [
                'list' => ['first', 'second'],
            ],
            'path' => 'list',
            'value' => 'third',
            'expected' => [
                'list' => ['first', 'second', 'third'],
            ],
        ];

        yield [
            'array' => [
                'list' => [],
            ],
            'path' => 'list',
            'value' => 'first',
            'expected' => [
                'list' => ['first'],
            ],
        ];

        // An array value is pushed as a single element, it is not merged
        yield [
            'array' => [
                'list' => ['first'],
            ],
            'path' => 'list',
            'value' => ['second', 'third'],
            'expected' => [
                'list' => ['first', ['second', 'third']],
            ],
        ];

        yield [
            'array' => [],
            'path' => 'list',
            'value' => null,
            'expected' => [
                'list' => [null],
            ],
        ];

        // The next key is used if the target array has string keys
        yield [
            'array' => [
                'map' => [
                    'a' => 'A',
                ],
            ],
            'path' => 'map',
            'value' => 'B',
            'expected' => [
                'map' => [
                    'a' => 'A',
                    0 => 'B',
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $array
     * @param array<string, mixed> $expected
     */
    #[DataProvider('dataPushSuccess')]
    public function testPushSuccess(array $array, string $path, mixed $value, array $expected): void
    {
        $reference = $array;

        $actual = dot_notation_array_push($array, $path, $value);

        self::assertArraysAreIdentical($reference, $array, "The original array was changed, test path: {$path}");
        self::assertArraysAreIdentical($expected, $actual, "Failed test path: {$path}");
    }

    public function testPushSuccessWithDotNotationPath(): void
    {
        $actual = dot_notation_array_push([], new DotNotationPath('some.list'), 'VALUE');

        self::assertArraysAreIdentical([
            'some' => [
                'list' => ['VALUE'],
            ],
        ], $actual);
    }

    // dot_notation_array_forget()

    #[DataProviderExternal(DotNotationPathTest::class, 'dataValidateOrThrowExceptionFailed')]
    public function testForgetFailedBecausePathIsInvalid(string $path): void
    {
        $this->expectException(DotNotationException::class);

        dot_notation_array_forget(self::TEST_ARRAY, $path);
    }

    /**
     * @return Iterator<array{array: array<string, mixed>, path: string, expected: array<string, mixed>}>
     */
    public static function dataForget(): iterable
    {
        yield [
            'array' => [],
            'path' => 'id',
            'expected' => [],
        ];

        yield [
            'array' => [
                'id' => 123,
            ],
            'path' => 'missing',
            'expected' => [
                'id' => 123,
            ],
        ];

        yield [
            'array' => [
                'id' => 123,
            ],
            'path' => 'id',
            'expected' => [],
        ];

        yield [
            'array' => [
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
            'array' => [
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
            'array' => [
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

        // Only the last segment is removed, the empty parent stays
        yield [
            'array' => [
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

        // A missing parent means there is nothing to remove
        yield [
            'array' => [
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                ],
            ],
            'path' => 'translate.ru.placeholder',
            'expected' => [
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                ],
            ],
        ];

        yield [
            'array' => [
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                ],
            ],
            'path' => 'missing.ru.placeholder',
            'expected' => [
                'translate' => [
                    'en' => [
                        'placeholder' => 'Placeholder',
                    ],
                ],
            ],
        ];

        // An element with the null value exists and is removed
        yield [
            'array' => [
                'id' => 123,
                'null' => null,
            ],
            'path' => 'null',
            'expected' => [
                'id' => 123,
            ],
        ];

        // Keys of a list are not renumbered
        yield [
            'array' => [
                'list' => ['first', 'second', 'third'],
            ],
            'path' => 'list.1',
            'expected' => [
                'list' => [
                    0 => 'first',
                    2 => 'third',
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $array
     * @param array<string, mixed> $expected
     */
    #[DataProvider('dataForget')]
    public function testForget(array $array, string $path, array $expected): void
    {
        $reference = $array;

        $actual = dot_notation_array_forget($array, $path);

        self::assertArraysAreIdentical($reference, $array, "The original array was changed, test path: {$path}");
        self::assertArraysAreIdentical($expected, $actual, "Failed test path: {$path}");
    }

    #[DataProvider('dataParentIsNotArray')]
    public function testForgetReturnsArrayUnchangedIfParentIsNotArray(string $path): void
    {
        $actual = dot_notation_array_forget(self::TEST_ARRAY, $path);

        self::assertArraysAreIdentical(self::TEST_ARRAY, $actual, "Failed test path: {$path}");
    }

    public function testForgetWithDotNotationPath(): void
    {
        $actual = dot_notation_array_forget(
            [
                'some' => [
                    'path' => 'VALUE',
                    'other' => 'OTHER',
                ],
            ],
            new DotNotationPath('some.path'),
        );

        self::assertArraysAreIdentical([
            'some' => [
                'other' => 'OTHER',
            ],
        ], $actual);
    }

    // dot_notation_array_has_path()

    #[DataProviderExternal(DotNotationPathTest::class, 'dataValidateOrThrowExceptionFailed')]
    public function testHasPathFailedBecausePathIsInvalid(string $path): void
    {
        $this->expectException(DotNotationException::class);

        dot_notation_array_has_path(self::TEST_ARRAY, $path);
    }

    /**
     * @return Iterator<array{path: string, expected: bool}>
     */
    public static function dataHasPath(): iterable
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
            'path' => 'list.2',
            'expected' => true,
        ];

        yield [
            'path' => 'missing',
            'expected' => false,
        ];

        yield [
            'path' => 'translate.fr',
            'expected' => false,
        ];

        yield [
            'path' => 'translate.ru.missing',
            'expected' => false,
        ];

        yield [
            'path' => 'list.3',
            'expected' => false,
        ];

        // An existing element is found regardless of its value
        yield [
            'path' => 'null',
            'expected' => true,
        ];

        yield [
            'path' => 'zero',
            'expected' => true,
        ];

        yield [
            'path' => 'false',
            'expected' => true,
        ];

        yield [
            'path' => 'empty_string',
            'expected' => true,
        ];

        yield [
            'path' => 'empty_array',
            'expected' => true,
        ];
    }

    #[DataProvider('dataHasPath')]
    public function testHasPath(string $path, bool $expected): void
    {
        $actual = dot_notation_array_has_path(self::TEST_ARRAY, $path);

        self::assertSame($expected, $actual, "Failed test path: {$path}");
    }

    #[DataProvider('dataParentIsNotArray')]
    public function testHasPathReturnsFalseIfParentIsNotArray(string $path): void
    {
        $actual = dot_notation_array_has_path(self::TEST_ARRAY, $path);

        self::assertFalse($actual, "Failed test path: {$path}");
    }

    public function testHasPathWithDotNotationPath(): void
    {
        $actual = dot_notation_array_has_path(self::TEST_ARRAY, new DotNotationPath('translate.ru.placeholder'));

        self::assertTrue($actual);
    }

    public function testHasPathInEmptyArray(): void
    {
        $actual = dot_notation_array_has_path([], 'translate');

        self::assertFalse($actual);
    }
}
