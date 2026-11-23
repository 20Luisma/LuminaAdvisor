<?php

declare(strict_types=1);

namespace LuminaAdvisor\Presentation\View;

/**
 * ViewModel para el formulario Wizard.
 * Centraliza catálogos y helpers de presentación para mantener los templates
 * libres de lógica condicional compleja.
 */
final class WizardViewData
{
    /**
     * @param array<string, string> $data
     * @param array<string, string> $errors
     */
    public function __construct(
        private readonly array $data = [],
        private readonly array $errors = [],
    ) {}

    /**
     * @return array<string, array{icon: string, label: string}>
     */
    public static function roomTypes(): array
    {
        return [
            'dormitorio' => ['icon' => '🛏️', 'label' => 'Dormitorio'],
            'salon'      => ['icon' => '🛋️', 'label' => 'Salón'],
            'cocina'     => ['icon' => '🍳', 'label' => 'Cocina'],
            'bano'       => ['icon' => '🚿', 'label' => 'Baño'],
            'despacho'   => ['icon' => '💼', 'label' => 'Despacho'],
            'pasillo'    => ['icon' => '🚪', 'label' => 'Pasillo'],
            'garaje'     => ['icon' => '🚗', 'label' => 'Garaje'],
        ];
    }

    /**
     * @return array<string, array{class: string, label: string}>
     */
    public static function wallColors(): array
    {
        return [
            'blanco' => ['class' => 'swatch-blanco', 'label' => 'Blanco'],
            'beige'  => ['class' => 'swatch-beige',  'label' => 'Beige'],
            'gris'   => ['class' => 'swatch-gris',   'label' => 'Gris'],
            'azul'   => ['class' => 'swatch-azul',   'label' => 'Azul'],
            'rojo'   => ['class' => 'swatch-rojo',   'label' => 'Rojo'],
            'negro'  => ['class' => 'swatch-negro',  'label' => 'Oscuro'],
        ];
    }

    /**
     * @return array<string, array{icon: string, label: string, hint: string}>
     */
    public static function windowTypes(): array
    {
        return [
            'ninguna' => ['icon' => '🪟', 'label' => 'Sin ventanas',    'hint' => '100% iluminación artificial'],
            'pequena' => ['icon' => '🔲', 'label' => 'Ventana pequeña', 'hint' => 'Poca luz natural'],
            'grande'  => ['icon' => '🔳', 'label' => 'Ventana grande',  'hint' => 'Buena luz natural'],
            'muchas'  => ['icon' => '☀️', 'label' => 'Muchas ventanas', 'hint' => 'Máxima luz natural'],
        ];
    }

    public function value(string $key): string
    {
        return htmlspecialchars($this->data[$key] ?? '', ENT_QUOTES, 'UTF-8');
    }

    public function checkedAttr(string $field, string $value): string
    {
        return ($this->data[$field] ?? '') === $value ? 'checked' : '';
    }

    public function errorText(string $key): string
    {
        $message = $this->errors[$key] ?? '';
        return $message !== '' ? '⚠ ' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') : '';
    }

    public function hasError(string $key): bool
    {
        return ($this->errors[$key] ?? '') !== '';
    }

    public function errorDisplayStyle(string $key): string
    {
        return $this->hasError($key) ? 'display:flex;' : 'display:none;';
    }
}
