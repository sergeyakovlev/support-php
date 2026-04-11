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

final readonly class FluentArr
{
    /**
     * @param array<integer|string, mixed> $array The underlying array data
     */
    public function __construct(
        private array $array,
    ) {}

    /**
     * Returns an element of the data array specified by a dot-notation path.
     *
     * If the retrieved value is an array, it will be wrapped in a new FluentArr instance.
     *
     * @param DotNotationPath|string $path A dot-notation path
     * @param mixed $default Default value returned if the path is not found
     * @return mixed The value at the specified path, wrapped in FluentArr if it’s an array
     * @throws ArrException If the path is invalid
     */
    public function get(DotNotationPath|string $path, mixed $default = null): mixed
    {
        $result = Arr::get($this->array, $path, $default);

        return is_array($result) ? new self($result) : $result;
    }

    /**
     * Returns a new instance with the value set at the specified by a dot-notation path.
     *
     * @param DotNotationPath|string $path A dot-notation path
     * @param mixed $value The value to set
     * @return self A new FluentArr instance with the updated value
     * @throws ArrException If the path is invalid or any of the array segments is not an array
     */
    public function set(DotNotationPath|string $path, mixed $value): self
    {
        $dotNotationPath = DotNotationPath::of($path);

        if (Arr::hasPath($this->array, $dotNotationPath) && Arr::get($this->array, $dotNotationPath) === $value) {
            return $this;
        }

        return new self(Arr::set($this->array, $dotNotationPath, $value));
    }

    /**
     * Returns a new instance with a value pushed to the array at the specified by a dot-notation path.
     *
     * If the required element is missing from the source array, it is created.
     *
     * @param DotNotationPath|string $path A dot-notation path
     * @param mixed $value The value to push
     * @return self A new FluentArr instance with the pushed value
     * @throws ArrException If the path is invalid or any of the array segments is not an array
     */
    public function push(DotNotationPath|string $path, mixed $value): self
    {
        return new self(Arr::push($this->array, $path, $value));
    }

    /**
     * Returns a new instance without the element at the specified by a dot-notation path.
     *
     * @param DotNotationPath|string $path A dot-notation path
     * @return self A new FluentArr instance without the specified element
     * @throws ArrException If the path is invalid
     */
    public function forget(DotNotationPath|string $path): self
    {
        $dotNotationPath = DotNotationPath::of($path);

        if ($this->array === [] || !Arr::hasPath($this->array, $dotNotationPath)) {
            return $this;
        }

        return new self(Arr::forget($this->array, $dotNotationPath));
    }

    /**
     * Checks if the array is empty.
     *
     * @return bool True if the array is empty, false otherwise
     */
    public function isEmpty(): bool
    {
        return $this->array === [];
    }

    /**
     * Checks if the array is not empty.
     *
     * @return bool True if the array is not empty, false otherwise
     */
    public function isNotEmpty(): bool
    {
        return $this->array !== [];
    }

    /**
     * Returns the first element of the array.
     *
     * If the first element is an array, it will be wrapped in a new FluentArr instance.
     *
     * @param mixed $default Default value returned if the array is empty
     * @return mixed The first element, wrapped in FluentArr if it’s an array, or the default value
     */
    public function first(mixed $default = null): mixed
    {
        $result = $this->array !== [] ? array_first($this->array) : $default;

        return is_array($result) ? new self($result) : $result;
    }

    /**
     * Returns the last element of the array.
     *
     * If the last element is an array, it will be wrapped in a new FluentArr instance.
     *
     * @param mixed $default Default value returned if the array is empty
     * @return mixed The last element, wrapped in FluentArr if it’s an array, or the default value
     */
    public function last(mixed $default = null): mixed
    {
        $result = $this->array !== [] ? array_last($this->array) : $default;

        return is_array($result) ? new self($result) : $result;
    }

    /**
     * Checks if the array contains a specific value.
     *
     * @param mixed $value The value to search for
     * @return bool True if the value exists in the array, false otherwise
     */
    public function has(mixed $value): bool
    {
        return array_any($this->array, static fn(mixed $item): bool => $item === $value);
    }

    /**
     * Checks if the array has a specific key.
     *
     * @param int|string $key The key to check for
     * @return bool True if the key exists, false otherwise
     */
    public function hasKey(int|string $key): bool
    {
        return array_key_exists($key, $this->array);
    }

    /**
     * Checks if the array has a value at the specified dot-notation path.
     *
     * @param DotNotationPath|string $path A dot-notation path
     * @return bool True if the path exists, false otherwise
     * @throws ArrException If the path is invalid
     */
    public function hasPath(DotNotationPath|string $path): bool
    {
        return Arr::hasPath($this->array, $path);
    }

    /**
     * Joins array elements with a separator string.
     *
     * @param string $separator The separator string
     * @return string A string containing the array elements joined by the separator
     */
    public function join(string $separator): string
    {
        return implode($separator, $this->array);
    }

    /**
     * Filters array elements using a callback function.
     *
     * @param callable|null $callback The callback function to use for filtering
     * @param int $mode Flag determining what arguments are sent to callback
     * @return self A new FluentArr instance with the filtered array
     */
    public function filter(?callable $callback = null, int $mode = 0): self
    {
        return new self(array_filter($this->array, $callback, $mode));
    }

    /**
     * Applies a callback to all elements of the array.
     *
     * @param callable|null $callback The callback function to apply
     * @return self A new FluentArr instance containing the results of applying the callback
     */
    public function map(?callable $callback = null): self
    {
        return new self(array_map($callback, $this->array));
    }

    /**
     * Checks if all elements of the array satisfy the callback condition.
     *
     * @param callable $callback The callback function that returns true or false
     * @return bool True if all elements satisfy the condition, false otherwise
     */
    public function all(callable $callback): bool
    {
        return array_all($this->array, $callback);
    }

    /**
     * Checks if any element of the array satisfies the callback condition.
     *
     * @param callable $callback The callback function that returns true or false
     * @return bool True if any element satisfies the condition, false otherwise
     */
    public function any(callable $callback): bool
    {
        return array_any($this->array, $callback);
    }

    /**
     * Returns a new instance with only the specified key/value pairs.
     *
     * @param integer[]|string[] $keys The keys to include in the result
     * @return self A new FluentArr instance containing only the specified key/value pairs
     */
    public function only(array $keys): self
    {
        $result = array_intersect_key($this->array, array_flip($keys));

        return $result === $this->array ? $this : new self($result);
    }

    /**
     * Returns a new instance with all key/value pairs except the specified keys.
     *
     * @param integer[]|string[] $keys The keys to exclude from the result
     * @return self A new FluentArr instance containing all key/value pairs except the specified keys
     */
    public function except(array $keys): self
    {
        $result = array_diff_key($this->array, array_flip($keys));

        return $result === $this->array ? $this : new self($result);
    }

    /**
     * Retrieves all the values for a given key from the array.
     *
     * @param DotNotationPath|string $path A dot-notation path
     * @return self A new FluentArr instance containing the plucked values
     * @throws ArrException If the path is invalid
     */
    public function pluck(DotNotationPath|string $path): self
    {
        return new self(Arr::pluck($this->array, $path));
    }

    /**
     * Converts the FluentArr instance to a plain array.
     *
     * @return array<integer|string, mixed> The underlying array
     */
    public function toArray(): array
    {
        return $this->array;
    }
}
