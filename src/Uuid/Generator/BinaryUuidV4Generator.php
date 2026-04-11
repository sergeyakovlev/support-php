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

namespace SergeYakovlev\Support\Uuid\Generator;

use Random\Randomizer;
use SergeYakovlev\Support\Uuid\BinaryUuid;

final readonly class BinaryUuidV4Generator implements BinaryUuidGeneratorInterface
{
    public function __construct(
        private Randomizer $randomizer,
    ) {}

    public function generate(): BinaryUuid
    {
        $value = $this->randomizer->getBytes(16);

        $value[6] = $value[6]
            |> ord(...)
            |> (static fn(int $v): int => $v & 0x0f | 0x40)
            |> chr(...);

        $value[8] = $value[8]
            |> ord(...)
            |> (static fn(int $v): int => $v & 0x3f | 0x80)
            |> chr(...);

        return new BinaryUuid($value);
    }
}
