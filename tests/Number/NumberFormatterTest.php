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
use SergeYakovlev\Support\Number\NumberFormatter;

final class NumberFormatterTest extends TestCase
{
    /**
     * @return Iterator<array{
     *     params: array<string, float|string>,
     *     expected: string,
     * }>
     */
    public static function dataFormatFloat(): iterable
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
            'expected' => '&minus;3,14',
        ];

        yield [
            'params' => [
                'value' => -3.1415,
                'decimals' => 3,
            ],
            'expected' => '&minus;3,142',
        ];

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
                'value' => 1_234.0,
                'decimals' => 2,
            ],
            'expected' => '1&nbsp;234,00',
        ];

        yield [
            'params' => [
                'value' => 1_234_567.0,
                'decimals' => 2,
            ],
            'expected' => '1&nbsp;234&nbsp;567,00',
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
                'value' => -1.0,
                'decimals' => 2,
            ],
            'expected' => '&minus;1,00',
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
    #[DataProvider('dataFormatFloat')]
    public function testFormatFloat(array $params, string $expected): void
    {
        $actual = new NumberFormatter()->formatFloat(...$params);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{
     *     params: array<string, int|string>,
     *     expected: string,
     * }>
     */
    public static function dataFormatInt(): iterable
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
            'expected' => '1&nbsp;234',
        ];

        yield [
            'params' => [
                'value' => 1_234_567,
            ],
            'expected' => '1&nbsp;234&nbsp;567',
        ];

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
            ],
            'expected' => '&minus;1',
        ];

        yield [
            'params' => [
                'value' => -1,
                'signMinus' => '-',
            ],
            'expected' => '-1',
        ];
    }

    /**
     * @param array<string, int|string> $params
     */
    #[DataProvider('dataFormatInt')]
    public function testFormatInt(array $params, string $expected): void
    {
        $actual = new NumberFormatter()->formatInt(...$params);

        self::assertSame($expected, $actual);
    }

    public function testRuPluralThingsFailed(): void
    {
        $this->expectException(NumberException::class);

        new NumberFormatter()->ruPluralThings(-1, ['штук', 'штука', 'штуки']);
    }

    /**
     * @return Iterator<array{0: integer, 1: string}>
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

    #[DataProvider('dataRuPluralThingsSuccess')]
    public function testRuPluralThingsSuccess(int $value, string $expected): void
    {
        $actual = new NumberFormatter()->ruPluralThings($value, ['штук', 'штука', 'штуки']);

        self::assertSame($expected, $actual);
    }
}
