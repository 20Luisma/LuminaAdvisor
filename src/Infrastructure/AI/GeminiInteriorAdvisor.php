<?php

declare(strict_types=1);

namespace LuminaAdvisor\Infrastructure\AI;

use LuminaAdvisor\Domain\Entities\LightRecommendation;
use LuminaAdvisor\Domain\Entities\Room;

/**
 * Servicio de Asesoría de Interiorismo por IA (Google Gemini 3.8 Flash).
 * Agrega valor cualitativo y de diseño sobre el cálculo matemático.
 * Blindado con fallback silencioso: si falla la API, la app nunca se rompe.
 */
final class GeminiInteriorAdvisor
{
    private string $apiKey;
    private string $model;

    public function __construct(?string $apiKey = null, string $model = 'gemini-3.8-flash')
    {
        $this->apiKey = $apiKey ?? (string) ($_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY') ?: '');
        $this->model  = $model;
    }

    /**
     * Genera 3 consejos de interiorismo y distribución en formato array estructurado.
     * Retorna null si no hay API key o si ocurre cualquier error de red/cuota.
     */
    public function getAdvice(Room $room, LightRecommendation $recommendation): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $prompt = $this->buildPrompt($room, $recommendation);

        try {
            $response = $this->callGemini($prompt);
            return $this->parseResponse($response);
        } catch (\Throwable $e) {
            // Silencioso por diseño: la física matemática es prioritaria
            error_log("GeminiInteriorAdvisor Error: " . $e->getMessage());
            return null;
        }
    }

    private function buildPrompt(Room $room, LightRecommendation $rec): string
    {
        return <<<PROMPT
Actúa como un diseñador de interiores y arquitecto lumínico de alto nivel.
Se han calculado los requerimientos de iluminación basados en la norma UNE-EN 12464-1 para esta habitación:

- Habitación: {$room->getType()}
- Superficie: {$room->getSquareMeters()} m²
- Altura del techo: {$room->getCeilingHeight()} m
- Color de paredes: {$room->getWallColor()}
- Luz natural: {$room->getWindows()}
- Lúmenes totales calculados: {$rec->getTotalLumens()} lm
- Puntos de luz sugeridos: {$rec->getLightPoints()}
- Temperatura sugerida: {$rec->getColorTemperatureK()}K ({$rec->getColorCategory()})

Tu misión: Da exactamente 3 recomendaciones concretas y de buen gusto sobre diseño, ubicación física de las luminarias y ambiente.
Responde un JSON con exactamente estas 3 claves:
- "distribucion": Dónde ubicar los puntos en el espacio (focos empotrados, lámpara suspendida, perimetral, distancias).
- "estilo_ambiente": Cómo crear capas de luz (luz indirecta, tiras LED ocultas, lámparas de apoyo, regulación dimmer).
- "consejo_pro": Un consejo profesional o detalle de confort visual (CRI > 90, evitar deslumbramientos, efecto del color de pared).
PROMPT;
    }

    private function callGemini(string $prompt): string
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'temperature'      => 0.3,
                'maxOutputTokens'  => 600,
                'thinkingConfig'   => [
                    'thinkingBudget' => 0
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error !== '' || $httpCode !== 200 || !$result) {
            throw new \RuntimeException("Error HTTP {$httpCode} al consultar Gemini: {$error}");
        }

        return (string) $result;
    }

    private function parseResponse(string $json): ?array
    {
        $data = json_decode($json, true);
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$text) {
            return null;
        }

        $parsed = json_decode($text, true);
        if (!is_array($parsed) || !isset($parsed['distribucion'])) {
            return null;
        }

        return [
            'distribucion'    => htmlspecialchars((string) ($parsed['distribucion'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'estilo_ambiente' => htmlspecialchars((string) ($parsed['estilo_ambiente'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'consejo_pro'     => htmlspecialchars((string) ($parsed['consejo_pro'] ?? ''), ENT_QUOTES, 'UTF-8'),
        ];
    }
}
