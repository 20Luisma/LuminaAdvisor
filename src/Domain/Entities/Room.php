<?php

declare(strict_types=1);

namespace LuminaAdvisor\Domain\Entities;

/**
 * Entidad Room — representa los parámetros de una habitación a calcular.
 * Basada en la norma UNE-EN 12464-1 (iluminancia por tipo de espacio).
 */
final class Room
{
    // Tipos de habitación soportados
    public const TYPES = [
        'dormitorio',
        'salon',
        'cocina',
        'bano',
        'despacho',
        'pasillo',
        'garaje',
    ];

    // Lux requeridos por tipo (valor medio de rango UNE-EN 12464-1)
    public const LUX_BY_TYPE = [
        'dormitorio' => 150,
        'salon'      => 250,
        'cocina'     => 400,
        'bano'       => 350,
        'despacho'   => 625,
        'pasillo'    => 125,
        'garaje'     => 88,
    ];

    // Factor de reflexión de paredes
    public const WALL_REFLECTION = [
        'blanco'  => 0.80,
        'beige'   => 0.60,
        'gris'    => 0.40,
        'azul'    => 0.30,
        'rojo'    => 0.20,
        'negro'   => 0.10,
    ];

    // Factor de luz natural según ventanas
    public const NATURAL_LIGHT_FACTOR = [
        'ninguna'  => 1.00,
        'pequena'  => 0.85,
        'grande'   => 0.65,
        'muchas'   => 0.50,
    ];

    public function __construct(
        private readonly string $type,          // Tipo de habitación
        private readonly float  $squareMeters,  // m²
        private readonly float  $ceilingHeight, // Altura del techo en metros
        private readonly string $wallColor,     // Color de paredes
        private readonly string $windows,       // Tipo de ventanas / luz natural
    ) {
        $this->validate();
    }

    // ─── Getters ───────────────────────────────────────────────────────────────

    public function getType(): string
    {
        return $this->type;
    }

    public function getSquareMeters(): float
    {
        return $this->squareMeters;
    }

    public function getCeilingHeight(): float
    {
        return $this->ceilingHeight;
    }

    public function getWallColor(): string
    {
        return $this->wallColor;
    }

    public function getWindows(): string
    {
        return $this->windows;
    }

    public function getRequiredLux(): int
    {
        return self::LUX_BY_TYPE[$this->type];
    }

    public function getWallReflectionFactor(): float
    {
        return self::WALL_REFLECTION[$this->wallColor];
    }

    public function getNaturalLightFactor(): float
    {
        return self::NATURAL_LIGHT_FACTOR[$this->windows];
    }

    // ─── Validación ────────────────────────────────────────────────────────────

    private function validate(): void
    {
        if (!array_key_exists($this->type, self::LUX_BY_TYPE)) {
            throw new \InvalidArgumentException(
                "Tipo de habitación inválido: '{$this->type}'. "
                . "Valores aceptados: " . implode(', ', array_keys(self::LUX_BY_TYPE))
            );
        }

        if ($this->squareMeters <= 0 || $this->squareMeters > 1000) {
            throw new \InvalidArgumentException(
                "Los metros cuadrados deben estar entre 0.1 y 1000. Recibido: {$this->squareMeters}"
            );
        }

        if ($this->ceilingHeight < 2.0 || $this->ceilingHeight > 10.0) {
            throw new \InvalidArgumentException(
                "La altura del techo debe estar entre 2.0 y 10.0 metros. Recibido: {$this->ceilingHeight}"
            );
        }

        if (!array_key_exists($this->wallColor, self::WALL_REFLECTION)) {
            throw new \InvalidArgumentException(
                "Color de paredes inválido: '{$this->wallColor}'. "
                . "Valores aceptados: " . implode(', ', array_keys(self::WALL_REFLECTION))
            );
        }

        if (!array_key_exists($this->windows, self::NATURAL_LIGHT_FACTOR)) {
            throw new \InvalidArgumentException(
                "Tipo de ventanas inválido: '{$this->windows}'. "
                . "Valores aceptados: " . implode(', ', array_keys(self::NATURAL_LIGHT_FACTOR))
            );
        }
    }
}
