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

namespace SergeYakovlev\Support\Str;

use Stringable;

final readonly class FluentStr implements Stringable
{
    /**
     * @param string $value The underlying string value
     */
    public function __construct(
        public string $value,
    ) {}

    /**
     * Magic method to convert the object to a string.
     *
     * @return string The underlying string value
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Returns the underlying string value.
     *
     * @return string The string value
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Checks if the string contains the given needle(s).
     *
     * @param string|string[] $needle The substring(s) to search for
     * @return bool True if the needle is found, false otherwise
     */
    public function contains(string|array $needle): bool
    {
        return is_string($needle)
            ? str_contains($this->value, $needle)
            : array_any($needle, fn(string $v): bool => str_contains($this->value, $v));
    }

    /**
     * Checks if the string does not contain the given needle(s).
     *
     * @param string|string[] $needle The substring(s) to search for
     * @return bool True if the needle is not found, false otherwise
     */
    public function doesntContain(string|array $needle): bool
    {
        return is_string($needle)
            ? !str_contains($this->value, $needle)
            : !array_any($needle, fn(string $v): bool => str_contains($this->value, $v));
    }

    /**
     * Returns the length of the string.
     *
     * @return int The length of the string in characters
     */
    public function length(): int
    {
        return mb_strlen($this->value);
    }

    /**
     * Checks if the string starts with the given needle(s).
     *
     * @param string|string[] $needle The substring(s) to check at the beginning
     * @return bool True if the string starts with the needle, false otherwise
     */
    public function startsWith(string|array $needle): bool
    {
        return is_string($needle)
            ? str_starts_with($this->value, $needle)
            : array_any($needle, fn(string $v): bool => str_starts_with($this->value, $v));
    }

    /**
     * Checks if the string does not start with the given needle(s).
     *
     * @param string|string[] $needle The substring(s) to check at the beginning
     * @return bool True if the string does not start with the needle, false otherwise
     */
    public function doesntStartWith(string|array $needle): bool
    {
        return is_string($needle)
            ? !str_starts_with($this->value, $needle)
            : !array_any($needle, fn(string $v): bool => str_starts_with($this->value, $v));
    }

    /**
     * Checks if the string ends with the given needle(s).
     *
     * @param string|string[] $needle The substring(s) to check at the end
     * @return bool True if the string ends with the needle, false otherwise
     */
    public function endsWith(string|array $needle): bool
    {
        return is_string($needle)
            ? str_ends_with($this->value, $needle)
            : array_any($needle, fn(string $v): bool => str_ends_with($this->value, $v));
    }

    /**
     * Checks if the string does not end with the given needle(s).
     *
     * @param string|string[] $needle The substring(s) to check at the end
     * @return bool True if the string does not end with the needle, false otherwise
     */
    public function doesntEndWith(string|array $needle): bool
    {
        return is_string($needle)
            ? !str_ends_with($this->value, $needle)
            : !array_any($needle, fn(string $v): bool => str_ends_with($this->value, $v));
    }

    /**
     * Returns a new instance with the string prepended to the beginning.
     *
     * @param string $prependString The string to prepend
     * @return self A new FluentStr instance with the prepended value, or the same instance if no change
     */
    public function prepend(string $prependString): self
    {
        return match (true) {
            $prependString === '' => $this,
            $this->value === '' => new self($prependString),
            default => new self($prependString . $this->value),
        };
    }

    /**
     * Returns a new instance with the string appended to the end.
     *
     * @param string $appendString The string to append
     * @return self A new FluentStr instance with the appended value, or the same instance if no change
     */
    public function append(string $appendString): self
    {
        return match (true) {
            $appendString === '' => $this,
            $this->value === '' => new self($appendString),
            default => new self($this->value . $appendString),
        };
    }

    /**
     * Returns a new instance with the string added to the beginning if it does not already start with it.
     *
     * @param string $startString The string to add at the beginning
     * @return self A new FluentStr instance that starts with the given string, or the same instance if already starts with it
     */
    public function start(string $startString): self
    {
        return str_starts_with($this->value, $startString)
            ? $this
            : new self($startString . $this->value);
    }

    /**
     * Returns a new instance with the string added to the end if it does not already end with it.
     *
     * @param string $endString The string to add at the end
     * @return self A new FluentStr instance that ends with the given string, or the same instance if already ends with it
     */
    public function end(string $endString): self
    {
        return str_ends_with($this->value, $endString)
            ? $this
            : new self($this->value . $endString);
    }

    /**
     * Converts the string to uppercase.
     *
     * @return self A new FluentStr instance in uppercase, or the same instance if already uppercase
     */
    public function upper(): self
    {
        $newValue = mb_strtoupper($this->value);

        return $newValue === $this->value
            ? $this
            : new self($newValue);
    }

    /**
     * Converts the string to lowercase.
     *
     * @return self A new FluentStr instance in lowercase, or the same instance if already lowercase
     */
    public function lower(): self
    {
        $newValue = mb_strtolower($this->value);

        return $newValue === $this->value
            ? $this
            : new self($newValue);
    }
}
