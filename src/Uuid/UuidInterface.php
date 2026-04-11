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

use Stringable;

interface UuidInterface extends Stringable
{
    /**
     * @return non-empty-string
     */
    public function __toString(): string;

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
    public function getVariant(): int;

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
    public function getVersion(): int;

    /**
     * Checks if the given value represents a nil UUID.
     *
     * @param string $value The UUID value to check
     * @return bool True if the value is a nil UUID in any supported format, false otherwise
     */
    public static function isNil(string $value): bool;

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
    public static function isBinaryStrictValid(string $value): bool;

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
    public static function isStrictValid(string $value): bool;

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
    public static function isValid(string $value): bool;

    /**
     * Returns the binary representation of the UUID.
     *
     * @return non-empty-string The 16-byte binary string
     */
    public function toBinaryString(): string;

    /**
     * Returns the compact hexadecimal representation without hyphens.
     *
     * @return string The 32-character hexadecimal string
     */
    public function toCompactString(): string;

    /**
     * Returns the standard string representation with hyphens.
     *
     * @return non-empty-string The 36-character UUID string in the format "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
     */
    public function toString(): string;

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
    public static function v4(): self;
}
