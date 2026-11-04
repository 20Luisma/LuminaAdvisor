<?php

declare(strict_types=1);

namespace LuminaAdvisor\Domain\Entities;

/**
 * Value Object — resultado inmutable del cálculo de iluminación.
 * Contiene todas las recomendaciones derivadas del análisis de Room.
 */
final class LightRecommendation
{
    // Tipos de luminaria recomendados por temperatura de color
    private const LUMINAIRE_BY_KELVIN = [
        'calida'   => 'LED integrado E27 (2700K–3000K) — Cálida / Relax',
        'neutra'   => 'LED integrado GU10 (3500K–4000K) — Neutra / Funcional',
        'fria'     => 'LED panel/downlight (4000K–5000K) — Fría / Concentración',
    ];

    public function __construct(
        private readonly int    $totalLumens,         // Lúmenes totales necesarios
        private readonly int    $colorTemperatureK,   // Temperatura de color recomendada (K)
        private readonly int    $lightPoints,         // Número de puntos de luz sugeridos
        private readonly string $luminaireType,       // Tipo de luminaria descriptivo
        private readonly float  $estimatedWatts,      // Consumo estimado en Watts
        private readonly float  $monthlyKwh,          // kWh/mes estimado
        private readonly float  $monthlyCostEur,      // €/mes estimado
        private readonly string $colorCategory,       // calida | neutra | fria
    ) {}

    // ─── Getters ───────────────────────────────────────────────────────────────

    public function getTotalLumens(): int
    {
        return $this->totalLumens;
    }

    public function getColorTemperatureK(): int
    {
        return $this->colorTemperatureK;
    }

    public function getLightPoints(): int
    {
        return $this->lightPoints;
    }

    public function getLuminaireType(): string
    {
        return $this->luminaireType;
    }

    public function getEstimatedWatts(): float
    {
        return $this->estimatedWatts;
    }

    public function getMonthlyKwh(): float
    {
        return $this->monthlyKwh;
    }

    public function getMonthlyCostEur(): float
    {
        return $this->monthlyCostEur;
    }

    public function getColorCategory(): string
    {
        return $this->colorCategory;
    }

    /**
     * Serializa para la vista (array plano, sin lógica de presentación aquí).
     */
    public function toArray(): array
    {
        return [
            'total_lumens'        => $this->totalLumens,
            'color_temperature_k' => $this->colorTemperatureK,
            'light_points'        => $this->lightPoints,
            'luminaire_type'      => $this->luminaireType,
            'estimated_watts'     => round($this->estimatedWatts, 1),
            'monthly_kwh'         => round($this->monthlyKwh, 2),
            'monthly_cost_eur'    => round($this->monthlyCostEur, 2),
            'color_category'      => $this->colorCategory,
        ];
    }
}
