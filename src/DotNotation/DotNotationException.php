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

use Exception;

final class DotNotationException extends Exception
{
    public static function invalidPathBecauseIsEmpty(): self
    {
        return new self('Invalid path (is empty).');
    }

    public static function invalidPathBecauseSegmentIsEmpty(string $path): self
    {
        return new self("Invalid path \"{$path}\" (one of the path segments is empty).");
    }

    public static function valueOfSegmentIsNotAnArray(DotNotationPath $dotNotationPath, string $segment): self
    {
        return new self("Value of the segment \"{$segment}\" of path \"{$dotNotationPath->path}\" is not an array.");
    }
}
