<?php

declare(strict_types=1);

namespace LuminaAdvisor\Infrastructure\AI;

use LuminaAdvisor\Domain\Entities\LightRecommendation;
use LuminaAdvisor\Domain\Entities\Room;
use LuminaAdvisor\Infrastructure\Observability\ErrorLogger;

/**
 * Servicio de Asesoría de Interiorismo por IA (Google Gemini 3.8 Flash).
 * Enfoque: Lenguaje 100% terrenal, humano, práctico y cero jerga técnica.
 */
final class GeminiInteriorAdvisor
{
    private string $apiKey;
    private string $model;
    /** @var callable(array<string, mixed>): string|null */
    private $httpTransport;
    private ErrorLogger $logger;

    /**
     * @param callable(array<string, mixed>): string|null $httpTransport Transporte inyectable para tests aislados sin red.
     */
    public function __construct(
        ?string $apiKey = null,
        string $model = 'gemini-3.8-flash',
        ?callable $httpTransport = null,
        ?ErrorLogger $logger = null,
    ) {
        $this->apiKey        = $apiKey ?? (string) ($_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY') ?: '');
        $this->model         = $model;
        $this->httpTransport = $httpTransport;
        $this->logger        = $logger ?? new ErrorLogger();
    }

    /**
     * Genera 3 consejos de diseño en lenguaje claro y accesible.
     *
     * @return array{distribucion: string, estilo_ambiente: string, consejo_pro: string}|null
     */
    public function getAdvice(Room $room, LightRecommendation $recommendation): ?array
    {
        if ($this->apiKey === '') {
            return null;
        }

        $prompt = $this->buildAdvicePrompt($room, $recommendation);

        try {
            $response = $this->callGemini($prompt);
            return $this->parseAdviceResponse($response);
        } catch (\Throwable $e) {
            $this->logger->log('warning', 'GeminiInteriorAdvisor fallback activado', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Responde preguntas del usuario en el chat conversacional.
     *
     * @param array<string, mixed>                   $roomContext
     * @param array<int, array<string, mixed>>       $history
     */
    public function chat(string $userMessage, array $roomContext, array $history = []): string
    {
        if ($this->apiKey === '') {
            return 'Lo siento, la conexión con el asesor de IA no está configurada.';
        }

        $type     = (string) ($roomContext['type'] ?? 'habitación');
        $m2       = (string) ($roomContext['square_meters'] ?? '15');
        $height   = (string) ($roomContext['ceiling_height'] ?? '2.5');
        $wall     = (string) ($roomContext['wall_color'] ?? 'blanco');
        $windows  = (string) ($roomContext['windows'] ?? 'mediana');
        $lumens   = (string) ($roomContext['total_lumens'] ?? '2000');
        $points   = (string) ($roomContext['light_points'] ?? '2');
        $kelvin   = (string) ($roomContext['color_temperature_k'] ?? '3000');
        $category = (string) ($roomContext['color_category'] ?? 'cálida');

        $systemInstruction = <<<SYS
Eres Lumina, una asesora experta y cercana en iluminación y diseño de interiores para el hogar.
El usuario acaba de calcular la iluminación de su habitación con estos datos exactos:
- Habitación: {$type}
- Tamaño: {$m2} m²
- Altura del techo: {$height} m
- Color de paredes: {$wall}
- Ventanas / luz natural: {$windows}
- Lúmenes calculados: {$lumens} lm ({$points} puntos de luz)
- Temperatura de color: {$kelvin}K ({$category})

REGLAS DE ORO:
1. Habla en un español natural, cercano, cálido y 100% práctico (como un amigo arquitecto explicándole a alguien en su casa).
2. NUNCA uses tecnicismos oscuros (prohibido decir UGR, R9, cenital, lux, o lúmenes sin explicar). Explica con cosas tangibles: "un foco empotrado", "una tira LED oculta", "luz cálida para relajar".
3. Da respuestas concisas (máximo 2 párrafos cortos o una lista con viñetas claras).
4. Si te preguntan por compras o qué pedir en la tienda, dale especificaciones prácticas que cualquier empleado de ferretería o Leroy Merlin entienda (tipo de casquillo, potencia en vatios LED equivalente, color de luz).
SYS;

        $contents = [];

        foreach ($history as $msg) {
            $role       = (($msg['role'] ?? '') === 'user') ? 'user' : 'model';
            $contents[] = [
                'role'  => $role,
                'parts' => [['text' => (string) ($msg['text'] ?? '')]],
            ];
        }

        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $systemInstruction]],
            ],
            'contents'         => $contents,
            'generationConfig' => [
                'temperature'     => 0.5,
                'maxOutputTokens' => 600,
                'thinkingConfig'  => ['thinkingBudget' => 0],
            ],
        ];

        try {
            $result = $this->sendRawRequest($payload);
            $data   = json_decode($result, true);
            if (!is_array($data)) {
                return 'No pude procesar la respuesta en este momento.';
            }
            $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            return is_string($reply) && $reply !== '' ? $reply : 'No pude procesar la respuesta en este momento.';
        } catch (\Throwable $e) {
            $this->logger->log('warning', 'Gemini Chat fallback activado', [
                'error' => $e->getMessage(),
            ]);
            return 'Hubo un pequeño corte en la comunicación con el asesor. Probá de nuevo en unos segundos.';
        }
    }

    private function buildAdvicePrompt(Room $room, LightRecommendation $rec): string
    {
        return <<<PROMPT
Actúa como una diseñadora de interiores profesional que habla con un cliente de forma súper clara, cercana y práctica.
Datos de la habitación calculada:
- Tipo: {$room->getType()}
- Superficie: {$room->getSquareMeters()} m² (Altura: {$room->getCeilingHeight()} m)
- Paredes: color {$room->getWallColor()}
- Luz natural: {$room->getWindows()}
- Setup recomendado: {$rec->getTotalLumens()} lúmenes repartidos en {$rec->getLightPoints()} puntos de luz a {$rec->getColorTemperatureK()}K ({$rec->getColorCategory()}).

Misión: Escribe 3 recomendaciones de buen gusto para que la persona sepa EXACTAMENTE dónde poner las luces y cómo ambientar su espacio.

REGLAS CRÍTICAS DE LENGUAJE:
- Habla en español llano y terrenal.
- PROHIBIDO TERMINANTEMENTE usar jerga incomprensible: NO uses 'cenital', NO uses 'UGR < 19', NO uses 'R9 > 50', NO uses 'deslumbramiento supino', NO uses fórmulas.
- Habla como una persona normal: "colocá el foco en el techo sobre la ducha", "poné una luz frontal en el espejo para que no te haga sombras en la cara al afeitarte o maquillarte", "usá un regulador para bajar la luz de noche".

Responde en formato JSON con exactamente estas 3 claves:
{
  "distribucion": "Dónde colocar físicamente los puntos en la habitación con distancias o referencias simples y claras.",
  "estilo_ambiente": "Cómo combinar las luces para crear un ambiente cómodo (luz principal en el techo, luz puntual de lectura o espejo, etc.).",
  "consejo_pro": "Un consejo práctico para cuando vaya a comprar o instalar (ej: evitar sombras, color de bombilla adecuado, protección contra humedad si es baño, etc.)."
}
PROMPT;
    }

    private function callGemini(string $prompt): string
    {
        $payload = [
            'contents' => [
                [
                    'parts' => [['text' => $prompt]],
                ],
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'temperature'      => 0.3,
                'maxOutputTokens'  => 600,
                'thinkingConfig'   => ['thinkingBudget' => 0],
            ],
        ];

        return $this->sendRawRequest($payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function sendRawRequest(array $payload): string
    {
        if ($this->httpTransport !== null) {
            return ($this->httpTransport)($payload);
        }

        $url  = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
        $body = json_encode($payload);
        if ($body === false) {
            throw new \RuntimeException('No se pudo serializar el payload JSON para Gemini.');
        }

        $ch = curl_init($url);
        if ($ch === false) {
            throw new \RuntimeException('No se pudo inicializar cURL.');
        }

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $result   = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error !== '' || $httpCode !== 200 || !is_string($result) || $result === '') {
            throw new \RuntimeException("Error HTTP {$httpCode} al consultar Gemini: {$error}");
        }

        return $result;
    }

    /**
     * @return array{distribucion: string, estilo_ambiente: string, consejo_pro: string}|null
     */
    private function parseAdviceResponse(string $json): ?array
    {
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return null;
        }

        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!is_string($text) || $text === '') {
            return null;
        }

        $parsed = json_decode($text, true);
        if (!is_array($parsed) || !isset($parsed['distribucion'])) {
            return null;
        }

        return [
            'distribucion'    => htmlspecialchars((string) $parsed['distribucion'], ENT_QUOTES, 'UTF-8'),
            'estilo_ambiente' => htmlspecialchars((string) ($parsed['estilo_ambiente'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'consejo_pro'     => htmlspecialchars((string) ($parsed['consejo_pro'] ?? ''), ENT_QUOTES, 'UTF-8'),
        ];
    }
}
