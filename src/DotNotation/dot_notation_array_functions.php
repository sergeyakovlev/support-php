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

/**
 * Returns an element of the data array specified by a dot-notation path.
 *
 * @param array<array-key, mixed> $array The data array
 * @param DotNotationPath|string $path A dot-notation path
 * @param mixed $default Default value
 * @return mixed The value at the specified path or the default value if not found
 * @throws DotNotationException If the path is invalid or any parent path segment of the array is not an array
 */
function dot_notation_array_get(array $array, DotNotationPath|string $path, mixed $default = null): mixed
{
    $dotNotationPath = DotNotationPath::from($path);

    $item = $array;
    $itemSegment = '';
    foreach ($dotNotationPath->allSegments as $pathSegment) {
        if (!is_array($item)) {
            throw DotNotationException::valueOfSegmentIsNotAnArray($dotNotationPath, $itemSegment);
        }

        if (!array_key_exists($pathSegment, $item)) {
            return $default;
        }

        $item = $item[$pathSegment];
        $itemSegment = $pathSegment;
    }

    return $item;
}

/**
 * Returns a new data array with the value set at the specified by a dot-notation path.
 *
 * If the required element is missing from the source array, it is created.
 *
 * @param array<array-key, mixed> $array The data array
 * @param DotNotationPath|string $path A dot-notation path
 * @param mixed $value The value being set
 * @return array<array-key, mixed> A new array with the value set at the specified path
 * @throws DotNotationException If the path is invalid or any parent path segment of the array is not an array
 */
function dot_notation_array_set(array $array, DotNotationPath|string $path, mixed $value): array
{
    $dotNotationPath = DotNotationPath::from($path);

    $parentItem = &$array;
    foreach ($dotNotationPath->parentSegments as $pathSegment) {
        if (!array_key_exists($pathSegment, $parentItem)) {
            $parentItem[$pathSegment] = [];
        }

        $parentItem = &$parentItem[$pathSegment];

        if (!is_array($parentItem)) {
            throw DotNotationException::valueOfSegmentIsNotAnArray($dotNotationPath, $pathSegment);
        }
    }

    $parentItem[$dotNotationPath->lastSegment] = $value;

    return $array;
}

/**
 * Returns a new data array with a value pushed to the array at the specified by a dot-notation path.
 *
 * If the required element is missing from the source array, it is created.
 *
 * @param array<array-key, mixed> $array The data array
 * @param DotNotationPath|string $path A dot-notation path
 * @param mixed $value Value to push
 * @return array<array-key, mixed> A new array with the value pushed to the specified path
 * @throws DotNotationException If the path is invalid or any path segment of the array is not an array
 */
function dot_notation_array_push(array $array, DotNotationPath|string $path, mixed $value): array
{
    $dotNotationPath = DotNotationPath::from($path);

    $item = &$array;
    foreach ($dotNotationPath->allSegments as $pathSegment) {
        if (!array_key_exists($pathSegment, $item)) {
            $item[$pathSegment] = [];
        }

        $item = &$item[$pathSegment];

        if (!is_array($item)) {
            throw DotNotationException::valueOfSegmentIsNotAnArray($dotNotationPath, $pathSegment);
        }
    }

    $item[] = $value;

    return $array;
}

/**
 * Returns a new data array without the element at the specified by a dot-notation path.
 *
 * @param array<array-key, mixed> $array The data array
 * @param DotNotationPath|string $path A dot-notation path
 * @return array<array-key, mixed> A new array with the element removed from the specified path
 * @throws DotNotationException If the path is invalid or any parent path segment of the array is not an array
 */
function dot_notation_array_forget(array $array, DotNotationPath|string $path): array
{
    $dotNotationPath = DotNotationPath::from($path);

    $parentOfItem = &$array;
    foreach ($dotNotationPath->parentSegments as $pathSegment) {
        if (!array_key_exists($pathSegment, $parentOfItem)) {
            return $array;
        }

        $parentOfItem = &$parentOfItem[$pathSegment];

        if (!is_array($parentOfItem)) {
            throw DotNotationException::valueOfSegmentIsNotAnArray($dotNotationPath, $pathSegment);
        }
    }

    unset($parentOfItem[$dotNotationPath->lastSegment]);

    return $array;
}

/**
 * Checks if the array has a value at the specified dot-notation path.
 *
 * @param array<array-key, mixed> $array The data array
 * @param DotNotationPath|string $path A dot-notation path
 * @return bool True if the path exists, false otherwise
 * @throws DotNotationException If the path is invalid or any parent path segment of the array is not an array
 */
function dot_notation_array_has_path(array $array, DotNotationPath|string $path): bool
{
    $dotNotationPath = DotNotationPath::from($path);

    $parentItem = $array;
    $parentSegment = '';
    foreach ($dotNotationPath->allSegments as $pathSegment) {
        if (!is_array($parentItem)) {
            throw DotNotationException::valueOfSegmentIsNotAnArray($dotNotationPath, $parentSegment);
        }

        if (!array_key_exists($pathSegment, $parentItem)) {
            return false;
        }

        $parentItem = $parentItem[$pathSegment];
        $parentSegment = $pathSegment;
    }

    return true;
}
