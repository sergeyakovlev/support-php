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

namespace SergeYakovlev\Support\Tests\DotNotation;

use PHPUnit\Framework\TestCase;
use SergeYakovlev\Support\DotNotation\DotNotationException;
use SergeYakovlev\Support\DotNotation\DotNotationPath;

final class DotNotationExceptionTest extends TestCase
{
    public function testInvalidPathBecauseIsEmpty(): void
    {
        $this->expectExceptionObject(
            new DotNotationException('Invalid path (is empty).'),
        );

        throw DotNotationException::invalidPathBecauseIsEmpty();
    }

    public function testInvalidPathBecauseSegmentIsEmpty(): void
    {
        $this->expectExceptionObject(
            new DotNotationException('Invalid path "a..c" (one of the path segments is empty).'),
        );

        throw DotNotationException::invalidPathBecauseSegmentIsEmpty('a..c');
    }

    public function testValueOfSegmentIsNotAnArray(): void
    {
        $this->expectExceptionObject(
            new DotNotationException('Value of the segment "b" of path "a.b.c" is not an array.'),
        );

        throw DotNotationException::valueOfSegmentIsNotAnArray(
            DotNotationPath::from('a.b.c'),
            'b',
        );
    }
}
