<?php

declare(strict_types=1);

namespace LuminaAdvisor\Tests\Unit;

use LuminaAdvisor\Application\UseCases\CalculateLightingUseCase;
use LuminaAdvisor\Domain\Entities\Room;
use PHPUnit\Framework\TestCase;

final class CalculateLightingUseCaseTest extends TestCase
{
    public function testCalculatesKitchenWithLargeWindowsAccurately(): void
    {
        $room = new Room('cocina', 15.0, 2.5, 'blanco', 'grande');
        $rec  = (new CalculateLightingUseCase())->execute($room);

        // (400 * 15) / 0.80 / 0.65 = 11538.46 -> 11538 lm
        self::assertSame(11538, $rec->getTotalLumens());
        self::assertSame(4000, $rec->getColorTemperatureK());
        self::assertSame('neutra', $rec->getColorCategory());
        self::assertSame(15, $rec->getLightPoints());
        self::assertStringContainsString('GU10', $rec->getLuminaireType());
        self::assertEqualsWithDelta(115.38, $rec->getEstimatedWatts(), 0.01);
        self::assertEqualsWithDelta(17.307, $rec->getMonthlyKwh(), 0.01);
        self::assertEqualsWithDelta(3.115, $rec->getMonthlyCostEur(), 0.01);

        $array = $rec->toArray();
        self::assertSame(11538, $array['total_lumens']);
        self::assertSame(15, $array['light_points']);
        self::assertSame(115.4, $array['estimated_watts']);
        self::assertSame(17.31, $array['monthly_kwh']);
        self::assertSame(3.12, $array['monthly_cost_eur']);
    }

    public function testCalculatesColdLightingForHomeOffice(): void
    {
        $room = new Room('despacho', 10.0, 2.6, 'gris', 'pequena');
        $rec  = (new CalculateLightingUseCase())->execute($room);

        self::assertSame(4500, $rec->getColorTemperatureK());
        self::assertSame('fria', $rec->getColorCategory());
        self::assertGreaterThan(10000, $rec->getTotalLumens());
    }

    public function testCalculatesWarmLightingAndMinimumOneLightPoint(): void
    {
        $room = new Room('garaje', 2.0, 2.4, 'blanco', 'muchas');
        $rec  = (new CalculateLightingUseCase())->execute($room);

        self::assertSame(1, $rec->getLightPoints());
        self::assertSame(4000, $rec->getColorTemperatureK());

        $bedroom = new Room('dormitorio', 12.0, 2.5, 'beige', 'pequena');
        $bedRec  = (new CalculateLightingUseCase())->execute($bedroom);
        self::assertSame(2700, $bedRec->getColorTemperatureK());
        self::assertSame('calida', $bedRec->getColorCategory());
    }
}
