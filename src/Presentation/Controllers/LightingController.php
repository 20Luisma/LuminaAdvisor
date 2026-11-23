<?php

declare(strict_types=1);

namespace LuminaAdvisor\Presentation\Controllers;

use LuminaAdvisor\Application\UseCases\CalculateLightingUseCase;
use LuminaAdvisor\Domain\Entities\LightRecommendation;
use LuminaAdvisor\Domain\Entities\Room;
use LuminaAdvisor\Infrastructure\AI\GeminiInteriorAdvisor;

final class LightingController
{
    public function __construct(
        private readonly CalculateLightingUseCase $useCase = new CalculateLightingUseCase(),
        private readonly ?GeminiInteriorAdvisor $advisor = null,
    ) {}

    public function handle(): void
    {
        $data     = [];
        $errors   = [];
        $result   = null;
        $aiAdvice = null;

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            [$data, $errors, $result, $aiAdvice] = $this->processPost($_POST);
        }

        require __DIR__ . '/../../../templates/layout.php';
    }

    /**
     * Procesa el formulario de cálculo de iluminación (testeable de forma aislada).
     *
     * @param array<string, mixed> $post
     * @return array{
     *   0: array<string, string>,
     *   1: array<string, string>,
     *   2: LightRecommendation|null,
     *   3: array{distribucion: string, estilo_ambiente: string, consejo_pro: string}|null
     * }
     */
    public function processPost(array $post): array
    {
        $data = [
            'type'           => trim((string) ($post['type'] ?? '')),
            'square_meters'  => trim((string) ($post['square_meters'] ?? '')),
            'ceiling_height' => trim((string) ($post['ceiling_height'] ?? '')),
            'wall_color'     => trim((string) ($post['wall_color'] ?? '')),
            'windows'        => trim((string) ($post['windows'] ?? '')),
        ];

        $errors = $this->validate($data);
        if ($errors !== []) {
            return [$data, $errors, null, null];
        }

        $result   = null;
        $aiAdvice = null;

        try {
            $room = new Room(
                type:          $data['type'],
                squareMeters:  (float) $data['square_meters'],
                ceilingHeight: (float) $data['ceiling_height'],
                wallColor:     $data['wall_color'],
                windows:       $data['windows'],
            );

            $result   = $this->useCase->execute($room);
            $advisor  = $this->advisor ?? new GeminiInteriorAdvisor();
            $aiAdvice = $advisor->getAdvice($room, $result);
        } catch (\InvalidArgumentException $e) {
            $errors['general'] = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }

        return [$data, $errors, $result, $aiAdvice];
    }

    /**
     * @param array<string, string> $data
     * @return array<string, string>
     */
    public function validate(array $data): array
    {
        $errors = [];

        if ($data['type'] === '') {
            $errors['type'] = 'Seleccioná el tipo de habitación.';
        }

        $m2 = $data['square_meters'];
        if (!is_numeric($m2) || (float) $m2 <= 0 || (float) $m2 > 1000) {
            $errors['square_meters'] = 'Los m² deben ser un número entre 1 y 1000.';
        }

        $h = $data['ceiling_height'];
        if (!is_numeric($h) || (float) $h < 2.0 || (float) $h > 10.0) {
            $errors['ceiling_height'] = 'La altura del techo debe estar entre 2.0 y 10.0 metros.';
        }

        if ($data['wall_color'] === '') {
            $errors['wall_color'] = 'Seleccioná el color de las paredes.';
        }

        if ($data['windows'] === '') {
            $errors['windows'] = 'Seleccioná el tipo de ventanas.';
        }

        return $errors;
    }
}
