<?php

declare(strict_types=1);

namespace LuminaAdvisor\Tests\Unit;

use InvalidArgumentException;
use LuminaAdvisor\Domain\Entities\Room;
use PHPUnit\Framework\TestCase;

final class RoomTest extends TestCase
{
    public function testValidRoomExposesAllPropertiesAndFactors(): void
    {
        $room = new Room('cocina', 15.0, 2.5, 'blanco', 'grande');

        self::assertSame('cocina', $room->getType());
        self::assertSame(15.0, $room->getSquareMeters());
        self::assertSame(2.5, $room->getCeilingHeight());
        self::assertSame('blanco', $room->getWallColor());
        self::assertSame('grande', $room->getWindows());
        self::assertSame(400, $room->getRequiredLux());
        self::assertSame(0.80, $room->getWallReflectionFactor());
        self::assertSame(0.65, $room->getNaturalLightFactor());
    }

    public function testRejectsInvalidRoomType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Room('terraza', 12.0, 2.5, 'blanco', 'grande');
    }

    public function testRejectsZeroOrNegativeSquareMeters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Room('salon', 0.0, 2.5, 'blanco', 'grande');
    }

    public function testRejectsExcessiveSquareMeters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Room('salon', 1500.0, 2.5, 'blanco', 'grande');
    }

    public function testRejectsOutOfRangeCeilingHeight(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Room('dormitorio', 12.0, 1.5, 'blanco', 'pequena');
    }

    public function testRejectsInvalidWallColor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Room('bano', 6.0, 2.5, 'violeta', 'ninguna');
    }

    public function testRejectsInvalidWindowsOption(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Room('despacho', 10.0, 2.6, 'gris', 'gigantes');
    }
}
