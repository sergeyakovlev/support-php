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

namespace SergeYakovlev\Support\Tests\Uuid;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SergeYakovlev\Support\Uuid\Uuid;

final class UuidTest extends TestCase
{
    /**
     * @return Iterator<array{value: string, variant: int, version: int}>
     */
    public static function dataStrictUuid(): iterable
    {
        yield [
            'value' => '00000000-0000-1000-8000-000000000000',
            'variant' => 1,
            'version' => 1,
        ];

        yield [
            'value' => '00000000-0000-1000-9000-000000000000',
            'variant' => 1,
            'version' => 1,
        ];

        yield [
            'value' => '00000000-0000-1000-a000-000000000000',
            'variant' => 1,
            'version' => 1,
        ];

        yield [
            'value' => '00000000-0000-1000-b000-000000000000',
            'variant' => 1,
            'version' => 1,
        ];

        yield [
            'value' => '00000000-0000-2000-8000-000000000000',
            'variant' => 1,
            'version' => 2,
        ];

        yield [
            'value' => '00000000-0000-2000-9000-000000000000',
            'variant' => 1,
            'version' => 2,
        ];

        yield [
            'value' => '00000000-0000-2000-a000-000000000000',
            'variant' => 1,
            'version' => 2,
        ];

        yield [
            'value' => '00000000-0000-2000-b000-000000000000',
            'variant' => 1,
            'version' => 2,
        ];

        yield [
            'value' => '00000000-0000-3000-8000-000000000000',
            'variant' => 1,
            'version' => 3,
        ];

        yield [
            'value' => '00000000-0000-3000-9000-000000000000',
            'variant' => 1,
            'version' => 3,
        ];

        yield [
            'value' => '00000000-0000-3000-a000-000000000000',
            'variant' => 1,
            'version' => 3,
        ];

        yield [
            'value' => '00000000-0000-3000-b000-000000000000',
            'variant' => 1,
            'version' => 3,
        ];

        yield [
            'value' => '00000000-0000-4000-8000-000000000000',
            'variant' => 1,
            'version' => 4,
        ];

        yield [
            'value' => '00000000-0000-4000-9000-000000000000',
            'variant' => 1,
            'version' => 4,
        ];

        yield [
            'value' => '00000000-0000-4000-a000-000000000000',
            'variant' => 1,
            'version' => 4,
        ];

        yield [
            'value' => '00000000-0000-4000-b000-000000000000',
            'variant' => 1,
            'version' => 4,
        ];

        yield [
            'value' => '00000000-0000-5000-8000-000000000000',
            'variant' => 1,
            'version' => 5,
        ];

        yield [
            'value' => '00000000-0000-5000-9000-000000000000',
            'variant' => 1,
            'version' => 5,
        ];

        yield [
            'value' => '00000000-0000-5000-a000-000000000000',
            'variant' => 1,
            'version' => 5,
        ];

        yield [
            'value' => '00000000-0000-5000-b000-000000000000',
            'variant' => 1,
            'version' => 5,
        ];

        yield [
            'value' => '00000000-0000-6000-8000-000000000000',
            'variant' => 1,
            'version' => 6,
        ];
        yield [
            'value' => '00000000-0000-6000-9000-000000000000',
            'variant' => 1,
            'version' => 6,
        ];
        yield [
            'value' => '00000000-0000-6000-a000-000000000000',
            'variant' => 1,
            'version' => 6,
        ];

        yield [
            'value' => '00000000-0000-6000-b000-000000000000',
            'variant' => 1,
            'version' => 6,
        ];

        yield [
            'value' => '00000000-0000-7000-8000-000000000000',
            'variant' => 1,
            'version' => 7,
        ];

        yield [
            'value' => '00000000-0000-7000-9000-000000000000',
            'variant' => 1,
            'version' => 7,
        ];

        yield [
            'value' => '00000000-0000-7000-a000-000000000000',
            'variant' => 1,
            'version' => 7,
        ];

        yield [
            'value' => '00000000-0000-7000-b000-000000000000',
            'variant' => 1,
            'version' => 7,
        ];

        yield [
            'value' => '00000000-0000-8000-8000-000000000000',
            'variant' => 1,
            'version' => 8,
        ];

        yield [
            'value' => '00000000-0000-8000-9000-000000000000',
            'variant' => 1,
            'version' => 8,
        ];

        yield [
            'value' => '00000000-0000-8000-a000-000000000000',
            'variant' => 1,
            'version' => 8,
        ];

        yield [
            'value' => '00000000-0000-8000-b000-000000000000',
            'variant' => 1,
            'version' => 8,
        ];
    }

    #[DataProvider('dataToString')]
    public function test__ToString(string $value): void
    {
        $actual = (string) new Uuid($value);

        self::assertSame($value, $actual);
    }

    /**
     * @return Iterator<array{value: string, expected: int}>
     */
    public static function dataGetVariant(): iterable
    {
        yield [
            'value' => '00000000-0000-0000-0000-000000000000',
            'expected' => 0,
        ];

        foreach (self::dataStrictUuid() as $item) {
            yield [
                'value' => $item['value'],
                'expected' => $item['variant'],
            ];
        }
    }

    #[DataProvider('dataGetVariant')]
    public function testGetVariant(string $value, int $expected): void
    {
        $actual = new Uuid($value)->getVariant();

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{value: string, expected: int}>
     */
    public static function dataGetVersion(): iterable
    {
        yield [
            'value' => '00000000-0000-0000-0000-000000000000',
            'expected' => 0,
        ];

        foreach (self::dataStrictUuid() as $item) {
            yield [
                'value' => $item['value'],
                'expected' => $item['version'],
            ];
        }
    }

    #[DataProvider('dataGetVersion')]
    public function testGetVersion(string $value, int $expected): void
    {
        $actual = new Uuid($value)->getVersion();

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{value: string, expected: bool}>
     */
    public static function dataIsNil(): iterable
    {
        yield [
            'value' => '',
            'expected' => false,
        ];

        yield [
            'value' => 'abcd',
            'expected' => false,
        ];

        yield [
            'value' => '17d20f74d946415b9e6da1675bfd29a2',
            'expected' => false,
        ];

        yield [
            'value' => '17d20f74-d946-415b-9e6d-a1675bfd29a2',
            'expected' => false,
        ];

        yield [
            'value' => "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00",
            'expected' => true,
        ];

        yield [
            'value' => '00000000000000000000000000000000',
            'expected' => true,
        ];

        yield [
            'value' => '00000000-0000-0000-0000-000000000000',
            'expected' => true,
        ];
    }

    #[DataProvider('dataIsNil')]
    public function testIsNil(string $value, bool $expected): void
    {
        $actual = Uuid::isNil($value);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{value: string, expected: bool}>
     */
    public static function dataIsBinaryStrictValid(): iterable
    {
        yield [
            'value' => '3ae4a4c1-5e6e-4bdf-bc8f-e3205bafe08e',
            'expected' => false,
        ];

        yield [
            'value' => 'a9adafa1cebd4c5da38f10f598182fae',
            'expected' => false,
        ];

        yield [
            'value' => "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00",
            'expected' => false,
        ];

        foreach (self::dataStrictUuid() as $item) {
            yield [
                'value' => hex2bin(str_replace('-', '', $item['value'])),
                'expected' => true,
            ];
        }
    }

    #[DataProvider('dataIsBinaryStrictValid')]
    public function testIsBinaryStrictValid(string $value, bool $expected): void
    {
        $actual = Uuid::isBinaryStrictValid($value);

        self::assertSame($expected, $actual, 'Value: ' . bin2hex($value));
    }

    /**
     * @return Iterator<array{value: string, expected: bool}>
     */
    public static function dataIsStrictValid(): iterable
    {
        yield [
            'value' => "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00",
            'expected' => false,
        ];

        yield [
            'value' => '00000000000000000000000000000000',
            'expected' => false,
        ];

        yield [
            'value' => '00000000-0000-0000-0000-000000000000',
            'expected' => false,
        ];

        foreach (self::dataStrictUuid() as $item) {
            yield [
                'value' => $item['value'],
                'expected' => true,
            ];
        }
    }

    #[DataProvider('dataIsStrictValid')]
    public function testIsStrictValid(string $value, bool $expected): void
    {
        $actual = Uuid::isStrictValid($value);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{value: string, expected: bool}>
     */
    public static function dataIsValid(): iterable
    {
        yield [
            'value' => '',
            'expected' => false,
        ];

        yield [
            'value' => 'abcd',
            'expected' => false,
        ];

        yield [
            'value' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'expected' => false,
        ];

        yield [
            'value' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
            'expected' => false,
        ];

        yield [
            'value' => "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00",
            'expected' => true,
        ];

        yield [
            'value' => "\xBE\xCF\xA5\x32\x4D\x62\x4A\x02\x82\x01\xE1\x45\xE3\x68\xE5\x1C",
            'expected' => true,
        ];

        yield [
            'value' => '00000000000000000000000000000000',
            'expected' => true,
        ];

        yield [
            'value' => 'becfa5324d624a028201e145e368e51c',
            'expected' => true,
        ];

        yield [
            'value' => 'BECFA5324D624A028201E145E368E51C',
            'expected' => true,
        ];

        yield [
            'value' => '00000000-0000-0000-0000-000000000000',
            'expected' => true,
        ];

        yield [
            'value' => 'becfa532-4d62-4a02-8201-e145e368e51c',
            'expected' => true,
        ];

        yield [
            'value' => 'BECFA532-4D62-4A02-8201-E145E368E51C',
            'expected' => true,
        ];
    }

    #[DataProvider('dataIsValid')]
    public function testIsValid(string $value, bool $expected): void
    {
        $actual = Uuid::isValid($value);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{value: string, expected: string}>
     */
    public static function dataToBinaryString(): iterable
    {
        yield [
            'value' => '00000000-0000-0000-0000-000000000000',
            'expected' => "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00",
        ];

        yield [
            'value' => '829a04f6-2ec9-4f97-9e8e-34df5280d951',
            'expected' => "\x82\x9a\x04\xf6\x2e\xc9\x4f\x97\x9e\x8e\x34\xdf\x52\x80\xd9\x51",
        ];
    }

    #[DataProvider('dataToBinaryString')]
    public function testToBinaryString(string $value, string $expected): void
    {
        $actual = new Uuid($value)->toBinaryString();

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{value: string, expected: string}>
     */
    public static function dataToCompactString(): iterable
    {
        yield [
            'value' => '00000000-0000-0000-0000-000000000000',
            'expected' => '00000000000000000000000000000000',
        ];

        yield [
            'value' => '94d48fdf-538e-4b3b-ad24-7fa7a16f4b9e',
            'expected' => '94d48fdf538e4b3bad247fa7a16f4b9e',
        ];
    }

    #[DataProvider('dataToCompactString')]
    public function testToCompactString(string $value, string $expected): void
    {
        $actual = new Uuid($value)->toCompactString();

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{value: string}>
     */
    public static function dataToString(): iterable
    {
        yield [
            'value' => '00000000-0000-0000-0000-000000000000',
        ];

        yield [
            'value' => 'd17d62ea-672c-4a3f-b569-517d4f0c1d12',
        ];
    }

    #[DataProvider('dataToString')]
    public function testToString(string $value): void
    {
        $actual = new Uuid($value)->toString();

        self::assertSame($value, $actual);
    }

    public function testV4(): void
    {
        self::assertTrue(
            Uuid::isStrictValid(
                Uuid::v4()->toString(),
            ),
        );
    }
}
