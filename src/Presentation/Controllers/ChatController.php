<?php

declare(strict_types=1);

namespace LuminaAdvisor\Presentation\Controllers;

use LuminaAdvisor\Infrastructure\AI\GeminiInteriorAdvisor;

final class ChatController
{
    public function handle(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
            return;
        }

        $inputRaw = file_get_contents('php://input');
        $payload  = json_decode((string) $inputRaw, true);

        if (!is_array($payload)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'JSON inválido']);
            return;
        }

        $message = trim((string) ($payload['message'] ?? ''));
        $context = (array) ($payload['context'] ?? []);
        $history = (array) ($payload['history'] ?? []);

        if ($message === '') {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'El mensaje no puede estar vacío']);
            return;
        }

        // Sanitización básica de inputs
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

        $advisor = new GeminiInteriorAdvisor();
        $reply   = $advisor->chat($message, $cleanContext, $history);

        echo json_encode([
            'ok'    => true,
            'reply' => $reply,
        ], JSON_UNESCAPED_UNICODE);
    }
}
