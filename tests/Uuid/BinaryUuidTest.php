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
use SergeYakovlev\Support\Uuid\BinaryUuid;
use SergeYakovlev\Support\Uuid\UuidException;

final class BinaryUuidTest extends TestCase
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
        $actual = (string) new BinaryUuid($value);

        self::assertSame($value, $actual);
    }

    /**
     * @return Iterator<array{value: string, expected: int}>
     */
    public static function dataGetVariant(): iterable
    {
        yield [
            'value' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
            'expected' => 3,
        ];

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
        $actual = new BinaryUuid($value)->getVariant();

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{value: string, expected: int}>
     */
    public static function dataGetVersion(): iterable
    {
        yield [
            'value' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
            'expected' => 15,
        ];

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
        $actual = new BinaryUuid($value)->getVersion();

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
        $actual = BinaryUuid::isNil($value);

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
        $actual = BinaryUuid::isBinaryStrictValid($value);

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
        $actual = BinaryUuid::isStrictValid($value);

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{value: string, expected: bool}>
     */
    public static function dataIsValid(): iterable
    {
        // Whatever the constructor accepts is valid, and the other way round
        yield [
            'value' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
            'expected' => true,
        ];

        yield [
            'value' => 'ffffffffffffffffffffffffffffffff',
            'expected' => true,
        ];

        yield [
            'value' => "\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF",
            'expected' => true,
        ];

        yield [
            'value' => 'FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF',
            'expected' => true,
        ];

        yield [
            'value' => 'ffffffff-ffff-ffff-ffff-fffffffffffe',
            'expected' => false,
        ];

        yield [
            'value' => 'ffffffff-ffff-ffff-ffff-ffffffffffff-',
            'expected' => false,
        ];

        yield [
            'value' => '550e8400-e29b-91d4-a716-446655440000',
            'expected' => false,
        ];

        yield [
            'value' => '550e8400e29b01d4a716446655440000',
            'expected' => false,
        ];

        yield [
            'value' => '550e8400-e29b-41d4-0716-446655440000',
            'expected' => false,
        ];

        yield [
            'value' => '550e8400e29b41d4a716446655440000aaaa',
            'expected' => false,
        ];

        yield [
            'value' => '0123456789ab4def8123456789abcdef----',
            'expected' => false,
        ];

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

        // 16 bytes: only the nil UUID and the values with the version 1-8 and the variant 1-3 are UUIDs
        yield [
            'value' => hex2bin('550e8400e29b41d4a716446655440000'),
            'expected' => true,
        ];

        yield [
            'value' => 'abcdefghijklmnop',
            'expected' => false,
        ];

        yield [
            'value' => str_repeat("\xFF", 15) . "\xFE",
            'expected' => false,
        ];

        yield [
            'value' => hex2bin('550e8400e29b01d4a716446655440000'),
            'expected' => false,
        ];

        yield [
            'value' => hex2bin('550e8400e29b91d4a716446655440000'),
            'expected' => false,
        ];

        yield [
            'value' => hex2bin('550e8400e29b41d40716446655440000'),
            'expected' => false,
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
        $actual = BinaryUuid::isValid($value);

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
        $actual = new BinaryUuid($value)->toBinaryString();

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
        $actual = new BinaryUuid($value)->toCompactString();

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
        $actual = new BinaryUuid($value)->toString();

        self::assertSame($value, $actual);
    }

    /**
     * @return Iterator<array{value: string, expected: string}>
     */
    public static function dataConstructorSuccess(): iterable
    {
        $standard = '550e8400-e29b-41d4-a716-446655440000';
        $compact = '550e8400e29b41d4a716446655440000';

        yield 'standard' => [
            'value' => $standard,
            'expected' => $standard,
        ];
        yield 'compact' => [
            'value' => $compact,
            'expected' => $standard,
        ];
        yield 'binary' => [
            'value' => hex2bin($compact),
            'expected' => $standard,
        ];
        yield 'standard in uppercase' => [
            'value' => strtoupper($standard),
            'expected' => $standard,
        ];
        yield 'compact in uppercase' => [
            'value' => strtoupper($compact),
            'expected' => $standard,
        ];
        yield 'standard in mixed case' => [
            'value' => '550E8400-e29b-41D4-a716-446655440000',
            'expected' => $standard,
        ];

        yield 'max, standard' => [
            'value' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
            'expected' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
        ];
        yield 'max, compact' => [
            'value' => 'ffffffffffffffffffffffffffffffff',
            'expected' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
        ];
        yield 'max, binary' => [
            'value' => "\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF",
            'expected' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
        ];
        yield 'max, standard in uppercase' => [
            'value' => 'FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF',
            'expected' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
        ];
        yield 'max, compact in uppercase' => [
            'value' => 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF',
            'expected' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
        ];

        yield 'nil, standard' => [
            'value' => '00000000-0000-0000-0000-000000000000',
            'expected' => '00000000-0000-0000-0000-000000000000',
        ];
        yield 'nil, compact' => [
            'value' => '00000000000000000000000000000000',
            'expected' => '00000000-0000-0000-0000-000000000000',
        ];
        yield 'nil, binary' => [
            'value' => "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00",
            'expected' => '00000000-0000-0000-0000-000000000000',
        ];
    }

    #[DataProvider('dataConstructorSuccess')]
    public function testConstructorSuccess(string $value, string $expected): void
    {
        $actual = new BinaryUuid($value)->toString();

        self::assertSame($expected, $actual);
    }

    /**
     * @return Iterator<array{value: string, message: string}>
     */
    public static function dataConstructorFailed(): iterable
    {
        $length = 'Invalid UUID length: expected 16, 32, or 36 characters.';
        $hexadecimal = 'Invalid hexadecimal string: unable to convert to binary representation.';
        $format = 'Invalid UUID format: expected 8-4-4-4-12 hexadecimal characters separated by hyphens.';
        $versionAndVariant = 'Invalid UUID format: the value does not match UUID version 1-8 variant 1-3 specification.';

        // The length
        yield 'empty' => [
            'value' => '',
            'message' => $length,
        ];
        yield 'short' => [
            'value' => 'abc',
            'message' => $length,
        ];
        yield '15 bytes' => [
            'value' => str_repeat('a', 15),
            'message' => $length,
        ];
        yield '17 bytes' => [
            'value' => str_repeat('a', 17),
            'message' => $length,
        ];
        yield '31 chars' => [
            'value' => str_repeat('a', 31),
            'message' => $length,
        ];
        yield '33 chars' => [
            'value' => str_repeat('a', 33),
            'message' => $length,
        ];
        yield '35 chars' => [
            'value' => '550e8400-e29b-41d4-a716-44665544000',
            'message' => $length,
        ];
        yield '37 chars' => [
            'value' => '550e8400-e29b-41d4-a716-4466554400000',
            'message' => $length,
        ];

        // Not hexadecimal, the compact format
        yield 'compact, only z' => [
            'value' => str_repeat('z', 32),
            'message' => $hexadecimal,
        ];
        yield 'compact, z at the end' => [
            'value' => '550e8400e29b41d4a716446655440zzz',
            'message' => $hexadecimal,
        ];
        yield 'compact, a space' => [
            'value' => '550e8400e29b41d4a716 46655440000',
            'message' => $hexadecimal,
        ];
        yield 'compact, a hyphen' => [
            'value' => '550e8400e29b41d4a716-46655440000',
            'message' => $hexadecimal,
        ];

        // Not hexadecimal, the standard format
        yield 'standard, only z' => [
            'value' => 'zzzzzzzz-zzzz-zzzz-zzzz-zzzzzzzzzzzz',
            'message' => $format,
        ];
        yield 'standard, g at the end' => [
            'value' => '550e8400-e29b-41d4-a716-44665544000g',
            'message' => $format,
        ];

        // Hyphens in wrong places or other characters instead of them
        yield 'hyphens at the end' => [
            'value' => '550e8400e29b41d4a716446655440000----',
            'message' => $format,
        ];
        yield 'hyphens at the start' => [
            'value' => '----550e8400e29b41d4a716446655440000',
            'message' => $format,
        ];
        yield 'hyphens moved' => [
            'value' => '550e84-00e29b-41d4-a716-446655440000',
            'message' => $format,
        ];
        yield 'first hyphen shifted' => [
            'value' => '550e8400e-29b-41d4-a716-446655440000',
            'message' => $format,
        ];
        yield 'last group has a hyphen' => [
            'value' => '550e8400-e29b-41d4-a716-44665544000-',
            'message' => $format,
        ];
        yield 'hyphens instead of digits' => [
            'value' => '550e8400-e29b-41d4-a716-4466554400--',
            'message' => $format,
        ];
        yield 'five hyphens' => [
            'value' => '550e8400-e29b-41d4-a716-4466-5544000',
            'message' => $format,
        ];
        yield 'underscores' => [
            'value' => '550e8400_e29b_41d4_a716_446655440000',
            'message' => $format,
        ];
        yield 'spaces' => [
            'value' => '550e8400 e29b 41d4 a716 446655440000',
            'message' => $format,
        ];
        yield 'colons' => [
            'value' => '550e8400:e29b:41d4:a716:446655440000',
            'message' => $format,
        ];
        yield 'no hyphens, 36 digits' => [
            'value' => '550e8400e29b41d4a716446655440000aaaa',
            'message' => $format,
        ];

        // The version and the variant, in each of the formats
        foreach (
            [
                'version 0' => '550e8400-e29b-01d4-a716-446655440000',
                'version 9' => '550e8400-e29b-91d4-a716-446655440000',
                'version 15, not the max UUID' => 'ffffffff-ffff-ffff-ffff-fffffffffffe',
                'version 15, variant 7' => 'ffffffff-ffff-ffff-7fff-ffffffffffff',
                'version 14, the other bits are set' => 'ffffffff-ffff-efff-ffff-ffffffffffff',
                'variant 0 (NCS)' => '550e8400-e29b-41d4-0716-446655440000',
                'variant 7 (NCS)' => '550e8400-e29b-41d4-7716-446655440000',
            ] as $name => $standard
        ) {
            yield "{$name}, standard" => [
                'value' => $standard,
                'message' => $versionAndVariant,
            ];
            yield "{$name}, compact" => [
                'value' => str_replace('-', '', $standard),
                'message' => $versionAndVariant,
            ];
            yield "{$name}, binary" => [
                'value' => hex2bin(str_replace('-', '', $standard)),
                'message' => $versionAndVariant,
            ];
        }
    }

    #[DataProvider('dataConstructorFailed')]
    public function testConstructorFailed(string $value, string $message): void
    {
        $this->expectException(UuidException::class);
        $this->expectExceptionMessage($message);

        new BinaryUuid($value);
    }

    public function testConstructorRaisesNoWarnings(): void
    {
        $warnings = [];

        set_error_handler(static function (int $errorNumber, string $errorMessage) use (&$warnings): bool {
            $warnings[] = $errorMessage;

            return true;
        });

        try {
            foreach (self::dataConstructorFailed() as ['value' => $value]) {
                try {
                    new BinaryUuid($value);
                } catch (UuidException) {
                    // It is expected, only the warnings are checked here
                }
            }
        } finally {
            restore_error_handler();
        }

        self::assertSame([], $warnings);
    }

    #[DataProvider('dataConstructorSuccess')]
    public function testIsValidOfValueThatConstructorAccepts(string $value, string $expected): void
    {
        self::assertTrue(BinaryUuid::isValid($value));
    }

    #[DataProvider('dataConstructorFailed')]
    public function testIsValidOfValueThatConstructorRejects(string $value, string $message): void
    {
        self::assertFalse(BinaryUuid::isValid($value));
    }

    /**
     * isValid() says the same as the constructor for every combination of the version and the variant byte,
     * in each of the three formats.
     */
    public function testIsValidAndConstructorAgreeOnAllFormats(): void
    {
        for ($version = 0; $version <= 0xF; $version++) {
            for ($variant = 0; $variant <= 0xFF; $variant++) {
                $binary = "\x55\x0E\x84\x00\xE2\x9B" . chr($version << 4 | 0x1) . "\xD4" . chr($variant)
                    . "\x16\x44\x66\x55\x44\x00\x00";
                $compact = bin2hex($binary);
                $standard = substr($compact, 0, 8) . '-' . substr($compact, 8, 4) . '-' . substr($compact, 12, 4)
                    . '-' . substr($compact, 16, 4) . '-' . substr($compact, 20);

                foreach ([
                    'binary' => $binary,
                    'compact' => $compact,
                    'standard' => $standard,
                ] as $format => $value) {
                    try {
                        new BinaryUuid($value);
                        $accepted = true;
                    } catch (UuidException) {
                        $accepted = false;
                    }

                    self::assertSame(
                        $accepted,
                        BinaryUuid::isValid($value),
                        sprintf('Failed test format: %s, version: %d, variant byte: 0x%02X', $format, $version, $variant),
                    );
                }
            }
        }
    }

    /**
     * @return Iterator<array{value: string, expected: bool}>
     */
    public static function dataIsMax(): iterable
    {
        yield 'max, standard' => [
            'value' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
            'expected' => true,
        ];
        yield 'max, compact' => [
            'value' => 'ffffffffffffffffffffffffffffffff',
            'expected' => true,
        ];
        yield 'max, binary' => [
            'value' => "\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF",
            'expected' => true,
        ];
        yield 'max, standard in uppercase' => [
            'value' => 'FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF',
            'expected' => true,
        ];
        yield 'max, compact in uppercase' => [
            'value' => 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF',
            'expected' => true,
        ];

        yield 'empty' => [
            'value' => '',
            'expected' => false,
        ];
        yield 'nil, standard' => [
            'value' => '00000000-0000-0000-0000-000000000000',
            'expected' => false,
        ];
        yield 'nil, compact' => [
            'value' => '00000000000000000000000000000000',
            'expected' => false,
        ];
        yield 'nil, binary' => [
            'value' => "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00",
            'expected' => false,
        ];
        yield 'a usual UUID' => [
            'value' => '550e8400-e29b-41d4-a716-446655440000',
            'expected' => false,
        ];
        yield 'the last digit differs, standard' => [
            'value' => 'ffffffff-ffff-ffff-ffff-fffffffffffe',
            'expected' => false,
        ];
        yield 'the last digit differs, compact' => [
            'value' => 'fffffffffffffffffffffffffffffffe',
            'expected' => false,
        ];
        yield 'the last byte differs, binary' => [
            'value' => str_repeat("\xFF", 15) . "\xFE",
            'expected' => false,
        ];
        yield 'too long' => [
            'value' => 'ffffffff-ffff-ffff-ffff-fffffffffffff',
            'expected' => false,
        ];
        yield 'too short' => [
            'value' => 'fffffffffffffffffffffffffffffff',
            'expected' => false,
        ];
        yield 'hyphens in wrong places' => [
            'value' => 'ffffffffffffffffffffffffffffffff----',
            'expected' => false,
        ];
    }

    #[DataProvider('dataIsMax')]
    public function testIsMax(string $value, bool $expected): void
    {
        self::assertSame($expected, BinaryUuid::isMax($value));
    }

    public function testMaxConstants(): void
    {
        self::assertSame('ffffffff-ffff-ffff-ffff-ffffffffffff', BinaryUuid::MAX);
        self::assertSame('ffffffffffffffffffffffffffffffff', BinaryUuid::COMPACT_MAX);
        self::assertSame("\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF", BinaryUuid::BINARY_MAX);
    }

    public function testNilConstants(): void
    {
        self::assertSame('00000000-0000-0000-0000-000000000000', BinaryUuid::NIL);
        self::assertSame('00000000000000000000000000000000', BinaryUuid::COMPACT_NIL);
        self::assertSame("\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", BinaryUuid::BINARY_NIL);
    }

    public function testMaxIsNeitherStrictValidNorNil(): void
    {
        self::assertFalse(BinaryUuid::isStrictValid('ffffffff-ffff-ffff-ffff-ffffffffffff'));
        self::assertFalse(BinaryUuid::isBinaryStrictValid("\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"));
        self::assertFalse(BinaryUuid::isNil('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    public function testMaxInAllFormats(): void
    {
        $uuid = new BinaryUuid('ffffffffffffffffffffffffffffffff');

        self::assertSame('ffffffff-ffff-ffff-ffff-ffffffffffff', $uuid->toString());
        self::assertSame('ffffffffffffffffffffffffffffffff', $uuid->toCompactString());
        self::assertSame("\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF", $uuid->toBinaryString());
        self::assertSame(15, $uuid->getVersion());
        self::assertSame(3, $uuid->getVariant());
        self::assertEquals(new BinaryUuid('ffffffff-ffff-ffff-ffff-ffffffffffff'), $uuid);
        self::assertEquals(new BinaryUuid("\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"), $uuid);
    }

    public function testIsValidOfBinaryStringsAcceptsAllVersionsAndVariantsOfTheConstructor(): void
    {
        foreach ([1, 2, 3, 4, 5, 6, 7, 8] as $version) {
            foreach ([0x80, 0x9F, 0xA0, 0xBF, 0xC0, 0xDF, 0xE0, 0xFF] as $variant) {
                $value = "\x55\x0E\x84\x00\xE2\x9B" . chr($version << 4) . "\xD4" . chr($variant)
                    . "\x16\x44\x66\x55\x44\x00\x00";

                self::assertTrue(BinaryUuid::isValid($value), sprintf('Failed test: %d, 0x%02X', $version, $variant));
            }
        }
    }

    public function testV4(): void
    {
        self::assertTrue(
            BinaryUuid::isStrictValid(
                BinaryUuid::v4()->toString(),
            ),
        );
    }
}
