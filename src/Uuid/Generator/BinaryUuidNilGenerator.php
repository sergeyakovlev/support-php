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

use SergeYakovlev\Support\Uuid\BinaryUuid;
use SergeYakovlev\Support\Uuid\UuidInterface;

final readonly class BinaryUuidNilGenerator implements BinaryUuidGeneratorInterface
{
    public function generate(): UuidInterface
    {
        return new BinaryUuid(BinaryUuid::BINARY_NIL);
    }
}
