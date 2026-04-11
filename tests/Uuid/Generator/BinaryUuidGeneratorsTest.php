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

namespace SergeYakovlev\Support\Tests\Uuid\Generator;

use PHPUnit\Framework\TestCase;
use Random\Engine\Mt19937;
use Random\Randomizer;
use SergeYakovlev\Support\Uuid\BinaryUuid;
use SergeYakovlev\Support\Uuid\Generator\BinaryUuidGeneratorInterface;
use SergeYakovlev\Support\Uuid\Generator\BinaryUuidMaxGenerator;
use SergeYakovlev\Support\Uuid\Generator\BinaryUuidNilGenerator;
use SergeYakovlev\Support\Uuid\Generator\BinaryUuidV4Generator;

final class BinaryUuidGeneratorsTest extends TestCase
{
    public function testNilGenerator(): void
    {
        $generator = new BinaryUuidNilGenerator();

        self::assertInstanceOf(BinaryUuidGeneratorInterface::class, $generator);
        self::assertSame('00000000-0000-0000-0000-000000000000', $generator->generate()->toString());
    }

    public function testMaxGenerator(): void
    {
        $generator = new BinaryUuidMaxGenerator();

        self::assertInstanceOf(BinaryUuidGeneratorInterface::class, $generator);
        self::assertSame('ffffffff-ffff-ffff-ffff-ffffffffffff', $generator->generate()->toString());
        self::assertTrue(BinaryUuid::isMax($generator->generate()->toString()));
    }

    public function testV4Generator(): void
    {
        $generator = new BinaryUuidV4Generator(new Randomizer(new Mt19937(1)));

        self::assertInstanceOf(BinaryUuidGeneratorInterface::class, $generator);

        for ($i = 0; $i < 100; $i++) {
            $uuid = $generator->generate();

            self::assertSame(4, $uuid->getVersion());
            self::assertSame(1, $uuid->getVariant());
            self::assertTrue(BinaryUuid::isStrictValid($uuid->toString()));
        }
    }

    public function testV4GeneratorWithTheSameSeedGivesTheSameUuids(): void
    {
        $first = new BinaryUuidV4Generator(new Randomizer(new Mt19937(42)));
        $second = new BinaryUuidV4Generator(new Randomizer(new Mt19937(42)));
        $other = new BinaryUuidV4Generator(new Randomizer(new Mt19937(43)));

        $uuid = $first->generate()->toString();

        self::assertSame($uuid, $second->generate()->toString());
        self::assertNotSame($uuid, $other->generate()->toString());
        self::assertNotSame($uuid, $first->generate()->toString());
    }
}
