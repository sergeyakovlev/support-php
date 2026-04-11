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
use PHPUnit\Framework\TestCase;
use SergeYakovlev\Support\Arr\ArrException;
use SergeYakovlev\Support\Arr\DotNotationPath;

final class DotNotationPathTest extends TestCase
{
    #[DataProvider('dataValidateOrThrowExceptionFailed')]
    public function testConstructorFailed(string $path): void
    {
        $this->expectException(ArrException::class);

        new DotNotationPath($path);
    }

    /**
     * @return Iterator<array{path: string, allSegments: string[], parentSegments: string[], lastSegment: string}>
     */
    public static function dataConstructorSuccess(): iterable
    {
        yield [
            'path' => 'first',
            'allSegments' => [
                'first',
            ],
            'parentSegments' => [],
            'lastSegment' => 'first',
        ];

        yield [
            'path' => 'first.second',
            'allSegments' => [
                'first',
                'second',
            ],
            'parentSegments' => [
                'first',
            ],
            'lastSegment' => 'second',
        ];

        yield [
            'path' => 'first.second.third',
            'allSegments' => [
                'first',
                'second',
                'third',
            ],
            'parentSegments' => [
                'first',
                'second',
            ],
            'lastSegment' => 'third',
        ];
    }

    /**
     * @param string[] $allSegments
     * @param string[] $parentSegments
     */
    #[DataProvider('dataConstructorSuccess')]
    public function testConstructorSuccess(
        string $path,
        array $allSegments,
        array $parentSegments,
        string $lastSegment,
    ): void {
        $dotNotationPath = new DotNotationPath($path);

        self::assertSame($path, $dotNotationPath->path, "Failed test path: {$path}");
        self::assertArraysAreIdentical($allSegments, $dotNotationPath->allSegments, "Failed test path: {$path}");
        self::assertArraysAreIdentical($parentSegments, $dotNotationPath->parentSegments, "Failed test path: {$path}");
        self::assertSame($lastSegment, $dotNotationPath->lastSegment, "Failed test path: {$path}");
    }

    /**
     * @return Iterator<array{path: string}>
     */
    public static function dataValidateOrThrowExceptionFailed(): iterable
    {
        yield [
            'path' => '',
        ];

        yield [
            'path' => '.',
        ];

        yield [
            'path' => '..',
        ];

        yield [
            'path' => '.translate.ru',
        ];

        yield [
            'path' => 'translate..ru',
        ];

        yield [
            'path' => 'translate.ru.',
        ];
    }

    #[DataProvider('dataValidateOrThrowExceptionFailed')]
    public function testValidateOrThrowExceptionFailed(string $path): void
    {
        $this->expectException(ArrException::class);

        DotNotationPath::validateOrThrowException($path);
    }

    /**
     * @return Iterator<array{path: string}>
     */
    public static function dataValidateOrThrowExceptionSuccess(): iterable
    {
        yield [
            'path' => 'first',
        ];

        yield [
            'path' => 'first.second',
        ];

        yield [
            'path' => 'first.second.third',
        ];
    }

    #[DataProvider('dataValidateOrThrowExceptionSuccess')]
    public function testValidateOrThrowExceptionSuccess(string $path): void
    {
        $this->expectNotToPerformAssertions();

        DotNotationPath::validateOrThrowException($path);
    }

    #[DataProvider('dataValidateOrThrowExceptionFailed')]
    public function testOfFailed(string $path): void
    {
        $this->expectException(ArrException::class);

        DotNotationPath::of($path);
    }

    /**
     * @param string[] $allSegments
     * @param string[] $parentSegments
     */
    #[DataProvider('dataConstructorSuccess')]
    public function testOfSuccess(string $path, array $allSegments, array $parentSegments, string $lastSegment): void
    {
        $dotNotationPath = DotNotationPath::of($path);

        self::assertSame($path, $dotNotationPath->path, "Failed test path: {$path}");
        self::assertArraysAreIdentical($allSegments, $dotNotationPath->allSegments, "Failed test path: {$path}");
        self::assertArraysAreIdentical($parentSegments, $dotNotationPath->parentSegments, "Failed test path: {$path}");
        self::assertSame($lastSegment, $dotNotationPath->lastSegment, "Failed test path: {$path}");
    }
}
