<?php

declare(strict_types=1);

namespace LuminaAdvisor\Presentation\Controllers;

use LuminaAdvisor\Application\UseCases\CalculateLightingUseCase;
use LuminaAdvisor\Domain\Entities\Room;

final class LightingController
{
    public function handle(): void
    {
        $data   = [];
        $errors = [];
        $result = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$data, $errors, $result] = $this->processPost();
        }

        require __DIR__ . '/../../../templates/layout.php';
    }

    private function processPost(): array
    {
        $data = [
            'type'           => trim((string) ($_POST['type']          ?? '')),
            'square_meters'  => trim((string) ($_POST['square_meters'] ?? '')),
            'ceiling_height' => trim((string) ($_POST['ceiling_height'] ?? '')),
            'wall_color'     => trim((string) ($_POST['wall_color']    ?? '')),
            'windows'        => trim((string) ($_POST['windows']       ?? '')),
        ];

        $errors = $this->validate($data);

        if (!empty($errors)) {
            return [$data, $errors, null];
        }

        try {
            $room = new Room(
                type:          $data['type'],
                squareMeters:  (float) $data['square_meters'],
                ceilingHeight: (float) $data['ceiling_height'],
                wallColor:     $data['wall_color'],
                windows:       $data['windows'],
            );

            $result = (new CalculateLightingUseCase())->execute($room);
        } catch (\InvalidArgumentException $e) {
            $errors['general'] = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }

        return [$data, $errors, $result];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty($data['type'])) {
            $errors['type'] = 'Seleccioná el tipo de habitación.';
        }

        $m2 = $data['square_meters'];
        if (!is_numeric($m2) || (float)$m2 <= 0 || (float)$m2 > 1000) {
            $errors['square_meters'] = 'Los m² deben ser un número entre 1 y 1000.';
        }

        $h = $data['ceiling_height'];
        if (!is_numeric($h) || (float)$h < 2.0 || (float)$h > 10.0) {
            $errors['ceiling_height'] = 'La altura del techo debe estar entre 2.0 y 10.0 metros.';
        }

        if (empty($data['wall_color'])) {
            $errors['wall_color'] = 'Seleccioná el color de las paredes.';
        }

        if (empty($data['windows'])) {
            $errors['windows'] = 'Seleccioná el tipo de ventanas.';
        }

        return $errors;
    }
}
