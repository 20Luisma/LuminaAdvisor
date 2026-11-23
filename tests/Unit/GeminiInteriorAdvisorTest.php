<?php

declare(strict_types=1);

namespace LuminaAdvisor\Tests\Unit;

use LuminaAdvisor\Application\UseCases\CalculateLightingUseCase;
use LuminaAdvisor\Domain\Entities\Room;
use LuminaAdvisor\Infrastructure\AI\GeminiInteriorAdvisor;
use LuminaAdvisor\Infrastructure\Observability\ErrorLogger;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class GeminiInteriorAdvisorTest extends TestCase
{
    public function testReturnsNullAdviceAndFallbackChatWhenApiKeyIsEmpty(): void
    {
        $room    = new Room('salon', 20.0, 2.5, 'blanco', 'grande');
        $rec     = (new CalculateLightingUseCase())->execute($room);
        $advisor = new GeminiInteriorAdvisor(apiKey: '');

        self::assertNull($advisor->getAdvice($room, $rec));
        self::assertStringContainsString('no está configurada', $advisor->chat('Hola', []));
    }

    public function testParsesStructuredAdviceFromInjectedTransport(): void
    {
        $room = new Room('cocina', 12.0, 2.5, 'blanco', 'pequena');
        $rec  = (new CalculateLightingUseCase())->execute($room);

        $fakeTransport = static function (array $payload): string {
            self::assertArrayHasKey('contents', $payload);
            return (string) json_encode([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => json_encode([
                                'distribucion'    => 'Focos sobre encimera <test>',
                                'estilo_ambiente' => 'Tira LED bajo mueble',
                                'consejo_pro'     => 'Pedir 4000K neutra',
                            ]),
                        ]],
                    ],
                ]],
            ]);
        };

        $advisor = new GeminiInteriorAdvisor(apiKey: 'test-key', httpTransport: $fakeTransport);
        $advice  = $advisor->getAdvice($room, $rec);

        self::assertIsArray($advice);
        self::assertSame('Focos sobre encimera &lt;test&gt;', $advice['distribucion']);
        self::assertSame('Tira LED bajo mueble', $advice['estilo_ambiente']);
        self::assertSame('Pedir 4000K neutra', $advice['consejo_pro']);
    }

    public function testHandlesTransportFailureGracefullyWithLogger(): void
    {
        $logFile = sys_get_temp_dir() . '/lumina_test_' . uniqid('', true) . '.log';
        $logger  = new ErrorLogger($logFile, '');
        $room    = new Room('bano', 4.0, 2.5, 'blanco', 'ninguna');
        $rec     = (new CalculateLightingUseCase())->execute($room);

        $failingTransport = static function (): string {
            throw new RuntimeException('Simulated timeout');
        };

        $advisor = new GeminiInteriorAdvisor(
            apiKey: 'test-key',
            httpTransport: $failingTransport,
            logger: $logger,
        );

        self::assertNull($advisor->getAdvice($room, $rec));
        self::assertStringContainsString('corte en la comunicación', $advisor->chat('¿Qué bombilla?', ['type' => 'Baño']));
        self::assertFileExists($logFile);

        @unlink($logFile);
    }

    public function testChatReturnsModelReplyWithHistory(): void
    {
        $fakeTransport = static function (array $payload): string {
            self::assertCount(3, $payload['contents']);
            return (string) json_encode([
                'candidates' => [[
                    'content' => [
                        'parts' => [['text' => 'Pedí bombilla LED E27 de 8W cálida.']],
                    ],
                ]],
            ]);
        };

        $advisor = new GeminiInteriorAdvisor(apiKey: 'test-key', httpTransport: $fakeTransport);
        $reply   = $advisor->chat(
            '¿Y el casquillo?',
            ['type' => 'Salón', 'square_meters' => 18],
            [
                ['role' => 'user', 'text' => 'Hola'],
                ['role' => 'model', 'text' => '¡Hola! Decime.'],
            ],
        );

        self::assertSame('Pedí bombilla LED E27 de 8W cálida.', $reply);
    }
}
