<?php

/*
 * This file is part of the Support package.
 *
 * (c) Serge Yakovlev <serge.yakovlev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SergeYakovlev\Support\Uuid;

use Random\Engine\Secure;
use Random\Randomizer;

final readonly class Uuid implements UuidInterface
{
    public const string BINARY_NIL = "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";

    public const string COMPACT_NIL = '00000000000000000000000000000000';

    public const string NIL = '00000000-0000-0000-0000-000000000000';

    /** @var non-empty-string $binaryValue Binary UUID value */
    private string $binaryValue;

    /**
     * Creates a new UUID instance.
     *
     * @param string $value The UUID value in binary (16 bytes), compact (32 hex chars),
     *                      or standard (36 chars with hyphens) format
     *
     * @throws UuidException If the provided value is not a valid UUID in any supported format
     */
    public function __construct(string $value)
    {
        $binaryValue = match (strlen($value)) {
            16 => $value,
            32 => hex2bin($value),
            36 => hex2bin(str_replace('-', '', $value)),
            default => throw new UuidException('Invalid UUID length: expected 16, 32, or 36 characters.'),
        };

        if ($binaryValue === false) {
            throw new UuidException('Invalid hexadecimal string: unable to convert to binary representation.');
        }

        if (!self::isBinaryStrictValid($binaryValue) && !self::isNil($value)) {
            throw new UuidException('Invalid UUID format: the value does not match UUID version 1-8 variant 1-2 specification.');
        }

        $this->binaryValue = $binaryValue;
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Gets the variant of the UUID according to RFC 4122.
     *
     * The variant field indicates the layout of the UUID. This method extracts
     * the variant from the 9th byte (index 8) of the binary representation.
     *
     * The variant is determined by the high 3 bits of the byte at index 8:
     * - 0b0xx (0-3): Reserved for NCS backward compatibility (variant 0)
     * - 0b10x (4-5): RFC 4122 variant (variant 1) - currently used variant
     * - 0b110 (6):   Reserved for Microsoft Corporation backward compatibility (variant 2)
     * - 0b111 (7):   Reserved for future definition (variant 3)
     *
     * Return values:
     * - 0: NCS (Network Computing System) backward compatibility
     * - 1: RFC 4122 variant (standard)
     * - 2: Microsoft Corporation backward compatibility
     * - 3: Reserved for future definition
     *
     * @return int<0, 3> The variant number (0, 1, 2, or 3)
     */
    public function getVariant(): int
    {
        return [0, 0, 0, 0, 1, 1, 2, 3][(ord($this->binaryValue[8]) >> 5) & 0x7];
    }

    /**
     * Gets the version of the UUID according to RFC 4122.
     *
     * The version field indicates the algorithm used to generate the UUID.
     * This method extracts the version from the 7th byte (index 6) of the
     * binary representation, specifically the high nibble (4 bits).
     *
     * Version values defined in RFC 4122:
     * - 1: Time-based (UUIDv1)
     * - 2: DCE security (UUIDv2)
     * - 3: Name-based (MD5, UUIDv3)
     * - 4: Random (UUIDv4)
     * - 5: Name-based (SHA-1, UUIDv5)
     * - 6: Reordered time-based (UUIDv6) - draft
     * - 7: Unix time-based (UUIDv7) - draft
     * - 8: Custom (UUIDv8) - draft
     *
     * @return int<0, 15> The version number (0-15, though typically 1-8 for standard UUIDs)
     */
    public function getVersion(): int
    {
        return (ord($this->binaryValue[6]) >> 4) & 0xf;
    }

    /**
     * Checks if the given value represents a nil UUID.
     *
     * @param string $value The UUID value to check
     * @return bool True if the value is a nil UUID in any supported format, false otherwise
     */
    public static function isNil(string $value): bool
    {
        return in_array($value, [self::NIL, self::COMPACT_NIL, self::BINARY_NIL], true);
    }

    /**
     * Validates a binary UUID string strictly according to RFC 4122 version/variant rules.
     *
     * This method validates that:
     * - The string is exactly 16 bytes long
     * - The version field (byte at index 6, high nibble) contains a valid version number (1-8)
     * - The variant field (byte at index 8, high nibble) contains a valid variant (8, 9, a, b, c, d, e, or f)
     *
     * @param string $value The binary UUID string to validate (must be exactly 16 bytes)
     * @return bool True if the binary UUID is strictly valid according to RFC 4122, false otherwise
     */
    public static function isBinaryStrictValid(string $value): bool
    {
        return strlen($value) === 16
            && in_array(ord($value[6]) & 0xf0, [0x10, 0x20, 0x30, 0x40, 0x50, 0x60, 0x70, 0x80], true)
            && in_array(ord($value[8]) & 0xe0, [0x80, 0x90, 0xa0, 0xb0, 0xc0, 0xd0, 0xe0, 0xf0], true);
    }

    /**
     * Validates a UUID string strictly according to RFC 4122 version/variant rules.
     *
     * This method validates that:
     * - The version field (13th character) contains 1-8
     * - The variant field (17th character) contains 8, 9, a, b, c, d, e, or f
     *
     * @param string $value The UUID string to validate (standard format with hyphens)
     * @return bool True if the UUID is strictly valid, false otherwise
     */
    public static function isStrictValid(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        return (bool) preg_match(
            pattern: '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89a-f][0-9a-f]{3}-[0-9a-f]{12}$/i',
            subject: $value,
        );
    }

    /**
     * Validates a UUID string format loosely.
     *
     * Checks the structural format without validating version/variant bits:
     * - Binary string: any 16-byte string
     * - Compact: 32 hexadecimal characters
     * - Standard: 36 characters with hyphens in correct positions
     *
     * @param string $value The UUID value to validate in any supported format
     * @return bool True if the value has a valid UUID structure, false otherwise
     */
    public static function isValid(string $value): bool
    {
        return match (strlen($value)) {
            16 => true,
            32 => (bool) preg_match('/^[0-9a-f]{32}$/i', $value),
            36 => (bool) preg_match('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/i', $value),
            default => false,
        };
    }

    /**
     * Returns the binary representation of the UUID.
     *
     * @return non-empty-string The 16-byte binary string
     */
    public function toBinaryString(): string
    {
        return $this->binaryValue;
    }

    /**
     * Returns the compact hexadecimal representation without hyphens.
     *
     * @return non-empty-string The 32-character hexadecimal string
     */
    public function toCompactString(): string
    {
        return bin2hex($this->binaryValue);
    }

    /**
     * Returns the standard string representation with hyphens.
     *
     * @return string The 36-character UUID string in the format "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
     */
    public function toString(): string
    {
        return $this->binaryValue
                |> bin2hex(...)
                |> (
                    static fn(string $v): string => substr($v, 0, 8)
                        . '-'
                        . substr($v, 8, 4)
                        . '-'
                        . substr($v, 12, 4)
                        . '-'
                        . substr($v, 16, 4)
                        . '-'
                        . substr($v, 20, 12)
                );
    }

    /**
     * Generates a random (version 4) UUID according to RFC 4122.
     *
     * This method creates a cryptographically secure random UUID using a non-blocking
     * random number generator. The generated UUID follows the RFC 4122 specification:
     * - Version 4 (random) is set in the 13th character (byte at index 6, high nibble = 0b0100)
     * - Variant 1 (RFC 4122) is set in the 17th character (byte at index 8, high 2 bits = 0b10)
     *
     * @return self A new UUID instance with a randomly generated version 4 UUID
     * @throws UuidException If the generated random bytes produce an invalid UUID
     *                       (should never happen with proper random generation)
     */
    public static function v4(): self
    {
        $value = new Randomizer(new Secure())->getBytes(16);

        $value[6] = $value[6]
            |> ord(...)
            |> (static fn(int $v): int => $v & 0x0f | 0x40)
            |> chr(...);

        $value[8] = $value[8]
            |> ord(...)
            |> (static fn(int $v): int => $v & 0x3f | 0x80)
            |> chr(...);

        return new self($value);
    }
}
