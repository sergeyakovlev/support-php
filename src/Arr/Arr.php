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

namespace SergeYakovlev\Support\Arr;

final class Arr
{
    /**
     * Returns an element of the data array specified by a dot-notation path.
     *
     * @param array<integer|string, mixed> $array The data array
     * @param DotNotationPath|string $path A dot-notation path
     * @param mixed $default Default value
     * @return mixed The value at the specified path or the default value if not found
     * @throws ArrException If the path is invalid
     */
    public static function get(array $array, DotNotationPath|string $path, mixed $default = null): mixed
    {
        $dotNotationPath = DotNotationPath::of($path);

        if ($array === []) {
            return $default;
        }

        $item = $array;
        foreach ($dotNotationPath->allSegments as $pathSegment) {
            if (!is_array($item) || !array_key_exists($pathSegment, $item)) {
                return $default;
            }

            $item = $item[$pathSegment];
        }

        return $item;
    }

    /**
     * Returns an element of the data array specified by a dot-notation path as an array.
     *
     * @param array<integer|string, mixed> $array The data array
     * @param DotNotationPath|string $path A dot-notation path
     * @param mixed[] $default Default value
     * @return mixed[] The value at the specified path as an array
     * @throws ArrException If the path is invalid or a value is not of the array type
     */
    public static function getArray(array $array, DotNotationPath|string $path, array $default): array
    {
        $result = self::get($array, $path, $default);

        if (!is_array($result)) {
            throw new ArrException('The value must be of the array type.');
        }

        return $result;
    }

    /**
     * Returns an element of the data array specified by a dot-notation path as a boolean.
     *
     * @param array<integer|string, mixed> $array The data array
     * @param DotNotationPath|string $path A dot-notation path
     * @param bool $default Default value
     * @return bool The value at the specified path as a boolean
     * @throws ArrException If the path is invalid or a value is not of the boolean type
     */
    public static function getBoolean(array $array, DotNotationPath|string $path, bool $default): bool
    {
        $result = self::get($array, $path, $default);

        if (!is_bool($result)) {
            throw new ArrException('The value must be of the boolean type.');
        }

        return $result;
    }

    /**
     * Returns an element of the data array specified by a dot-notation path as a float.
     *
     * @param array<integer|string, mixed> $array The data array
     * @param DotNotationPath|string $path A dot-notation path
     * @param float $default Default value
     * @return float The value at the specified path as a float
     * @throws ArrException If the path is invalid or a value is not of the float type
     */
    public static function getFloat(array $array, DotNotationPath|string $path, float $default): float
    {
        $result = self::get($array, $path, $default);

        if (!is_float($result) && !is_int($result)) {
            throw new ArrException('The value must be of the float type.');
        }

        return $result;
    }

    /**
     * Returns an element of the data array specified by a dot-notation path as an integer.
     *
     * @param array<integer|string, mixed> $array The data array
     * @param DotNotationPath|string $path A dot-notation path
     * @param int $default Default value
     * @return int The value at the specified path as an integer
     * @throws ArrException If the path is invalid or a value is not of the integer type
     */
    public static function getInteger(array $array, DotNotationPath|string $path, int $default): int
    {
        $result = self::get($array, $path, $default);

        if (!is_int($result)) {
            throw new ArrException('The value must be of the integer type.');
        }

        return $result;
    }

    /**
     * Returns an element of the data array specified by a dot-notation path as a string.
     *
     * @param array<integer|string, mixed> $array The data array
     * @param DotNotationPath|string $path A dot-notation path
     * @param string $default Default value
     * @return string The value at the specified path as a string
     * @throws ArrException If the path is invalid or a value is not of the string type
     */
    public static function getString(array $array, DotNotationPath|string $path, string $default): string
    {
        $result = self::get($array, $path, $default);

        if (!is_string($result)) {
            throw new ArrException('The value must be of the string type.');
        }

        return $result;
    }

    /**
     * Returns a new data array with the value set at the specified by a dot-notation path.
     *
     * If the required element is missing from the source array, it is created.
     *
     * @param array<integer|string, mixed> $array The data array
     * @param DotNotationPath|string $path A dot-notation path
     * @param mixed $value The value being set
     * @return array<integer|string, mixed> A new array with the value set at the specified path
     * @throws ArrException If the path is invalid or any parent path segment of the array is not an array
     */
    public static function set(array $array, DotNotationPath|string $path, mixed $value): array
    {
        $dotNotationPath = DotNotationPath::of($path);

        $parentItem = &$array;
        foreach ($dotNotationPath->parentSegments as $pathSegment) {
            if (!array_key_exists($pathSegment, $parentItem)) {
                $parentItem[$pathSegment] = [];
            }

            $parentItem = &$parentItem[$pathSegment];

            if (!is_array($parentItem)) {
                throw new ArrException('One parent path segment of the array is not an array');
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
     * @param array<integer|string, mixed> $array The data array
     * @param DotNotationPath|string $path A dot-notation path
     * @param mixed $value Value to push
     * @return array<integer|string, mixed> A new array with the value pushed to the specified path
     * @throws ArrException If the path is invalid or any path segment of the array is not an array
     */
    public static function push(array $array, DotNotationPath|string $path, mixed $value): array
    {
        $dotNotationPath = DotNotationPath::of($path);

        $targetItem = &$array;
        foreach ($dotNotationPath->allSegments as $pathSegment) {
            if (!array_key_exists($pathSegment, $targetItem)) {
                $targetItem[$pathSegment] = [];
            }

            $targetItem = &$targetItem[$pathSegment];

            if (!is_array($targetItem)) {
                throw new ArrException('One path segment of the array is not an array');
            }
        }

        $targetItem[] = $value;

        return $array;
    }

    /**
     * Returns a new data array without the element at the specified by a dot-notation path.
     *
     * @param array<integer|string, mixed> $array The data array
     * @param DotNotationPath|string $path A dot-notation path
     * @return array<integer|string, mixed> A new array with the element removed from the specified path
     * @throws ArrException If the path is invalid
     */
    public static function forget(array $array, DotNotationPath|string $path): array
    {
        $dotNotationPath = DotNotationPath::of($path);

        if ($array === [] || !self::hasPath($array, $dotNotationPath)) {
            return $array;
        }

        $parentOfItem = &$array;
        foreach ($dotNotationPath->parentSegments as $pathSegment) {
            $parentOfItem = &$parentOfItem[$pathSegment];
        }

        unset($parentOfItem[$dotNotationPath->lastSegment]);

        return $array;
    }

    /**
     * Checks if the array is empty.
     *
     * @param array<integer|string, mixed> $array The array to check
     * @return bool True if the array is empty, false otherwise
     */
    public static function isEmpty(array $array): bool
    {
        return $array === [];
    }

    /**
     * Checks if the array is not empty.
     *
     * @param array<integer|string, mixed> $array The array to check
     * @return bool True if the array is not empty, false otherwise
     */
    public static function isNotEmpty(array $array): bool
    {
        return $array !== [];
    }

    /**
     * Returns the first element of the array.
     *
     * @param array<integer|string, mixed> $array The array
     * @param mixed $default Default value if the array is empty
     * @return mixed The first element or the default value
     */
    public static function first(array $array, mixed $default = null): mixed
    {
        return $array !== [] ? array_first($array) : $default;
    }

    /**
     * Returns the last element of the array.
     *
     * @param array<integer|string, mixed> $array The array
     * @param mixed $default Default value if the array is empty
     * @return mixed The last element or the default value
     */
    public static function last(array $array, mixed $default = null): mixed
    {
        return $array !== [] ? array_last($array) : $default;
    }

    /**
     * Checks if the array contains a specific value.
     *
     * @param array<integer|string, mixed> $array The array to search in
     * @param mixed $value The value to search for
     * @return bool True if the value exists in the array, false otherwise
     */
    public static function has(array $array, mixed $value): bool
    {
        return array_any($array, static fn(mixed $item): bool => $item === $value);
    }

    /**
     * Checks if the array has a specific key.
     *
     * @param array<integer|string, mixed> $array The array to check
     * @param int|string $key The key to check for
     * @return bool True if the key exists, false otherwise
     */
    public static function hasKey(array $array, int|string $key): bool
    {
        return array_key_exists($key, $array);
    }

    /**
     * Checks if the array has a value at the specified dot-notation path.
     *
     * @param array<integer|string, mixed> $array The data array
     * @param DotNotationPath|string $path A dot-notation path
     * @return bool True if the path exists, false otherwise
     * @throws ArrException If the path is invalid
     */
    public static function hasPath(array $array, DotNotationPath|string $path): bool
    {
        $dotNotationPath = DotNotationPath::of($path);

        if ($array === []) {
            return false;
        }

        $item = $array;
        foreach ($dotNotationPath->allSegments as $pathSegment) {
            if (!is_array($item) || !array_key_exists($pathSegment, $item)) {
                return false;
            }

            $item = $item[$pathSegment];
        }

        return true;
    }

    /**
     * Joins array elements with a separator string.
     *
     * @param array<integer|string, mixed> $array The array to join
     * @param string $separator The separator string
     * @return string A string containing the array elements joined by the separator
     */
    public static function join(array $array, string $separator = ''): string
    {
        return implode($separator, $array);
    }

    /**
     * Filters array elements using a callback function.
     *
     * @param array<integer|string, mixed> $array The array to filter
     * @param callable|null $callback The callback function to use for filtering
     * @param int $mode Flag determining what arguments are sent to callback
     * @return array<integer|string, mixed> The filtered array
     */
    public static function filter(array $array, ?callable $callback = null, int $mode = 0): array
    {
        return array_filter($array, $callback, $mode);
    }

    /**
     * Applies a callback to all elements of the array.
     *
     * @param array<integer|string, mixed> $array The array to map over
     * @param callable|null $callback The callback function to apply
     * @return array<integer|string, mixed> An array containing the results of applying the callback
     */
    public static function map(array $array, ?callable $callback = null): array
    {
        return array_map($callback, $array);
    }

    /**
     * Checks if all elements of the array satisfy the callback condition.
     *
     * @param array<integer|string, mixed> $array The array to check
     * @param callable $callback The callback function that returns true or false
     * @return bool True if all elements satisfy the condition, false otherwise
     */
    public static function all(array $array, callable $callback): bool
    {
        return array_all($array, $callback);
    }

    /**
     * Checks if any element of the array satisfies the callback condition.
     *
     * @param array<integer|string, mixed> $array The array to check
     * @param callable $callback The callback function that returns true or false
     * @return bool True if any element satisfies the condition, false otherwise
     */
    public static function any(array $array, callable $callback): bool
    {
        return array_any($array, $callback);
    }

    /**
     * Returns only the specified key/value pairs.
     *
     * @param array<integer|string, mixed> $array The source array
     * @param integer[]|string[] $keys The keys to include in the result
     * @return array<integer|string, mixed> An array containing only the specified key/value pairs
     */
    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Returns all key/value pairs except the specified keys.
     *
     * @param array<integer|string, mixed> $array The source array
     * @param integer[]|string[] $keys The keys to exclude from the result
     * @return array<integer|string, mixed> An array containing all key/value pairs except the specified keys
     */
    public static function except(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }

    /**
     * Retrieves all the values for a given key from the array.
     *
     * @param array<integer|string, mixed> $array The array to pluck from
     * @param DotNotationPath|string $path A dot-notation path to pluck
     * @return array<integer, mixed> An array containing the values from the specified path
     * @throws ArrException If the path is invalid
     */
    public static function pluck(array $array, DotNotationPath|string $path): array
    {
        $dotNotationPath = DotNotationPath::of($path);

        $result = [];
        foreach ($array as $item) {
            $result[] = self::get($item, $dotNotationPath);
        }

        return $result;
    }

    /**
     * Creates a new FluentArr instance from the given array.
     *
     * @param array<integer|string, mixed> $array The array to wrap in a FluentArr instance
     * @return FluentArr A FluentArr instance for fluent array operations
     */
    public static function of(array $array): FluentArr
    {
        return new FluentArr($array);
    }
}
