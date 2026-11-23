<?php

declare(strict_types=1);

namespace LuminaAdvisor\Presentation\Controllers;

use LuminaAdvisor\Infrastructure\AI\GeminiInteriorAdvisor;

final class ChatController
{
    public function __construct(
        private readonly ?GeminiInteriorAdvisor $advisor = null,
    ) {}

    public function handle(): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }

        $method   = (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $inputRaw = (string) file_get_contents('php://input');

        [$statusCode, $responseBody] = $this->processRequest($method, $inputRaw);

        http_response_code($statusCode);
        echo json_encode($responseBody, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Procesa una petición de chat y devuelve [statusCode, payload] (testeable sin HTTP real).
     *
     * @return array{0: int, 1: array<string, mixed>}
     */
    public function processRequest(string $method, string $inputRaw): array
    {
        if ($method !== 'POST') {
            return [405, ['ok' => false, 'error' => 'Método no permitido']];
        }

        $payload = json_decode($inputRaw, true);
        if (!is_array($payload)) {
            return [400, ['ok' => false, 'error' => 'JSON inválido']];
        }

        $message = trim((string) ($payload['message'] ?? ''));
        $context = is_array($payload['context'] ?? null) ? $payload['context'] : [];
        $history = is_array($payload['history'] ?? null) ? $payload['history'] : [];

        if ($message === '') {
            return [422, ['ok' => false, 'error' => 'El mensaje no puede estar vacío']];
        }

        $cleanContext = [
            'type'                => htmlspecialchars((string) ($context['type'] ?? 'habitación'), ENT_QUOTES, 'UTF-8'),
            'square_meters'       => (float) ($context['square_meters'] ?? 10.0),
            'ceiling_height'      => (float) ($context['ceiling_height'] ?? 2.5),
            'wall_color'          => htmlspecialchars((string) ($context['wall_color'] ?? 'blanco'), ENT_QUOTES, 'UTF-8'),
            'windows'             => htmlspecialchars((string) ($context['windows'] ?? 'pequeña'), ENT_QUOTES, 'UTF-8'),
            'total_lumens'        => (int) ($context['total_lumens'] ?? 2000),
            'light_points'        => (int) ($context['light_points'] ?? 2),
            'color_temperature_k' => (int) ($context['color_temperature_k'] ?? 3000),
            'color_category'      => htmlspecialchars((string) ($context['color_category'] ?? 'cálida'), ENT_QUOTES, 'UTF-8'),
        ];

        $advisor = $this->advisor ?? new GeminiInteriorAdvisor();
        $reply   = $advisor->chat($message, $cleanContext, $history);

        return [200, ['ok' => true, 'reply' => $reply]];
    }
}
