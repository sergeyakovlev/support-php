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

namespace SergeYakovlev\Support\DotNotation;

final class DotNotationPath
{
    /**
     * @var list<string> All path segments split by dot
     */
    private(set) array $allSegments {
        get => $this->allSegments ??= explode('.', $this->path);
    }

    /**
     * @var list<string> All path segments except the last one
     */
    private(set) array $parentSegments {
        get => $this->parentSegments ??= array_slice($this->allSegments, 0, -1);
    }

    /**
     * @var string The last segment of the path
     */
    private(set) string $lastSegment {
        get => $this->lastSegment ??= array_last($this->allSegments);
    }

    /**
     * Creates a new DotNotationPath instance.
     *
     * @param string $path The dot-notation path string
     * @throws DotNotationException If the path is invalid
     */
    public function __construct(
        public readonly string $path,
    ) {
        self::validateOrThrowException($path);
    }

    /**
     * Validates a dot-notation path and throws an exception if invalid.
     *
     * @param string $path The path to validate
     * @throws DotNotationException If the path is empty, starts/ends with a dot, or contains empty segments
     */
    public static function validateOrThrowException(string $path): void
    {
        if ($path === '') {
            throw DotNotationException::invalidPathBecauseIsEmpty();
        }

        if (str_starts_with($path, '.') || str_ends_with($path, '.') || str_contains($path, '..')) {
            throw DotNotationException::invalidPathBecauseSegmentIsEmpty($path);
        }
    }

    /**
     * Creates a DotNotationPath instance from either a string or an existing instance.
     *
     * @param self|string $path The path as string or existing DotNotationPath instance
     * @return self The DotNotationPath instance
     * @throws DotNotationException If the path is invalid (when string is provided)
     */
    public static function from(self|string $path): self
    {
        return is_string($path) ? new self($path) : $path;
    }
}
