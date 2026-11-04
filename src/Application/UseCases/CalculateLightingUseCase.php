<?php

declare(strict_types=1);

namespace LuminaAdvisor\Application\UseCases;

use LuminaAdvisor\Domain\Entities\LightRecommendation;
use LuminaAdvisor\Domain\Entities\Room;

/**
 * Caso de uso: Calcular recomendación de iluminación para una habitación.
 *
 * Estándar aplicado: UNE-EN 12464-1 (Iluminación de lugares de trabajo interiores)
 *
 * Fórmula base:
 *   Lúmenes = (Lux × m²) / Factor_Reflexión_Paredes / Factor_Luz_Natural
 *
 * Supuestos:
 *   - Eficiencia LED estándar: 100 lm/W
 *   - Uso diario: 5 horas/día · 30 días = 150 horas/mes
 *   - Precio energía España (2024): 0.18 €/kWh (configurable)
 *   - Lúmenes por punto de luz estándar: 800 lm (bombilla LED E27 equivalente)
 */
final class CalculateLightingUseCase
{
    // Eficiencia media LED en lm/W
    private const LED_EFFICACY_LM_W = 100.0;

    // Horas de uso estimadas por mes (5h/día × 30 días)
    private const MONTHLY_HOURS = 150.0;

    // Precio kWh en España (€) — ajustable por entorno
    private const KWH_PRICE_EUR = 0.18;

    // Lúmenes medios por punto de luz LED estándar
    private const LUMENS_PER_LIGHT_POINT = 800;

    // Temperatura de color por tipo de habitación (Kelvin)
    private const COLOR_TEMPERATURE_BY_TYPE = [
        'dormitorio' => ['k' => 2700, 'category' => 'calida'],
        'salon'      => ['k' => 3000, 'category' => 'calida'],
        'cocina'     => ['k' => 4000, 'category' => 'neutra'],
        'bano'       => ['k' => 3500, 'category' => 'neutra'],
        'despacho'   => ['k' => 4500, 'category' => 'fria'],
        'pasillo'    => ['k' => 3000, 'category' => 'calida'],
        'garaje'     => ['k' => 4000, 'category' => 'neutra'],
    ];

    // Descripción de luminaria por categoría de temperatura
    private const LUMINAIRE_BY_CATEGORY = [
        'calida' => 'LED integrado E27 (2700K–3000K) — Cálida / Relax',
        'neutra' => 'LED integrado GU10 (3500K–4000K) — Neutra / Funcional',
        'fria'   => 'LED panel / downlight (4000K–5000K) — Fría / Concentración',
    ];

    public function execute(Room $room): LightRecommendation
    {
        // 1. Lúmenes totales necesarios
        $totalLumens = $this->calculateTotalLumens($room);

        // 2. Número de puntos de luz
        $lightPoints = $this->calculateLightPoints($totalLumens);

        // 3. Temperatura de color y tipo de luminaria
        $colorData     = self::COLOR_TEMPERATURE_BY_TYPE[$room->getType()];
        $colorTempK    = $colorData['k'];
        $colorCategory = $colorData['category'];
        $luminaireType = self::LUMINAIRE_BY_CATEGORY[$colorCategory];

        // 4. Consumo estimado
        $estimatedWatts = $this->calculateWatts($totalLumens);
        $monthlyKwh     = $this->calculateMonthlyKwh($estimatedWatts);
        $monthlyCostEur = $this->calculateMonthlyCost($monthlyKwh);

        return new LightRecommendation(
            totalLumens:        $totalLumens,
            colorTemperatureK:  $colorTempK,
            lightPoints:        $lightPoints,
            luminaireType:      $luminaireType,
            estimatedWatts:     $estimatedWatts,
            monthlyKwh:         $monthlyKwh,
            monthlyCostEur:     $monthlyCostEur,
            colorCategory:      $colorCategory,
        );
    }

    // ─── Cálculos internos ─────────────────────────────────────────────────────

    /**
     * Lúmenes = (Lux × m²) / reflexión_paredes / factor_luz_natural
     * Redondeamos al entero más cercano.
     */
    private function calculateTotalLumens(Room $room): int
    {
        $rawLumens = ($room->getRequiredLux() * $room->getSquareMeters())
            / $room->getWallReflectionFactor()
            / $room->getNaturalLightFactor();

        return (int) round($rawLumens);
    }

    /**
     * Puntos de luz = ceil(lúmenes_totales / lúmenes_por_punto)
     * Mínimo siempre 1 punto.
     */
    private function calculateLightPoints(int $totalLumens): int
    {
        return max(1, (int) ceil($totalLumens / self::LUMENS_PER_LIGHT_POINT));
    }

    /**
     * Watts = lúmenes_totales / eficiencia_LED
     */
    private function calculateWatts(int $totalLumens): float
    {
        return $totalLumens / self::LED_EFFICACY_LM_W;
    }

    /**
     * kWh/mes = (Watts / 1000) × horas_mes
     */
    private function calculateMonthlyKwh(float $watts): float
    {
        return ($watts / 1000) * self::MONTHLY_HOURS;
    }

    /**
     * €/mes = kWh/mes × precio_kWh
     */
    private function calculateMonthlyCost(float $monthlyKwh): float
    {
        return $monthlyKwh * self::KWH_PRICE_EUR;
    }
}

