<?php

declare(strict_types=1);

namespace LuminaAdvisor\Tests\Unit;

use LuminaAdvisor\Infrastructure\AI\GeminiInteriorAdvisor;
use LuminaAdvisor\Infrastructure\Observability\ErrorLogger;
use LuminaAdvisor\Presentation\Controllers\ChatController;
use LuminaAdvisor\Presentation\Controllers\LightingController;
use LuminaAdvisor\Presentation\View\WizardViewData;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ControllersAndViewTest extends TestCase
{
    public function testLightingControllerValidatesAndProcessesValidPost(): void
    {
        $advisor    = new GeminiInteriorAdvisor(apiKey: '');
        $controller = new LightingController(advisor: $advisor);

        [, $errors, $result, $aiAdvice] = $controller->processPost([
            'type'           => 'salon',
            'square_meters'  => '20',
            'ceiling_height' => '2.6',
            'wall_color'     => 'blanco',
            'windows'        => 'grande',
        ]);

        self::assertSame([], $errors);
        self::assertNotNull($result);
        self::assertSame(9615, $result->getTotalLumens());
        self::assertNull($aiAdvice);
    }

    public function testLightingControllerReturnsValidationErrorsOnInvalidInput(): void
    {
        $controller = new LightingController(advisor: new GeminiInteriorAdvisor(apiKey: ''));

        [, $errors, $result] = $controller->processPost([
            'type'           => '',
            'square_meters'  => '0',
            'ceiling_height' => '1.2',
            'wall_color'     => '',
            'windows'        => '',
        ]);

        self::assertNull($result);
        self::assertArrayHasKey('type', $errors);
        self::assertArrayHasKey('square_meters', $errors);
        self::assertArrayHasKey('ceiling_height', $errors);
        self::assertArrayHasKey('wall_color', $errors);
        self::assertArrayHasKey('windows', $errors);
    }

    public function testLightingControllerCapturesDomainException(): void
    {
        $controller = new LightingController(advisor: new GeminiInteriorAdvisor(apiKey: ''));

        [, $errors, $result] = $controller->processPost([
            'type'           => 'inexistente',
            'square_meters'  => '15',
            'ceiling_height' => '2.5',
            'wall_color'     => 'blanco',
            'windows'        => 'grande',
        ]);

        self::assertNull($result);
        self::assertArrayHasKey('general', $errors);
    }

    public function testChatControllerHandlesHttpStatusesAndValidReply(): void
    {
        $fakeAdvisor = new GeminiInteriorAdvisor(
            apiKey: 'fake',
            httpTransport: static fn (): string => (string) json_encode([
                'candidates' => [[
                    'content' => ['parts' => [['text' => 'Respuesta de prueba']]],
                ]],
            ]),
        );

        $controller = new ChatController($fakeAdvisor);

        [$status405] = $controller->processRequest('GET', '');
        self::assertSame(405, $status405);

        [$status400] = $controller->processRequest('POST', '{not-json');
        self::assertSame(400, $status400);

        [$status422] = $controller->processRequest('POST', '{"message":"   "}');
        self::assertSame(422, $status422);

        [$status200, $body] = $controller->processRequest(
            'POST',
            (string) json_encode(['message' => 'Hola', 'context' => ['type' => 'Cocina']]),
        );
        self::assertSame(200, $status200);
        self::assertTrue($body['ok']);
        self::assertSame('Respuesta de prueba', $body['reply']);
    }

    public function testWizardViewDataEscapesValuesAndFormatsErrors(): void
    {
        $view = new WizardViewData(
            ['type' => 'cocina', 'square_meters' => '<script>15</script>'],
            ['type' => 'Error de prueba'],
        );

        self::assertCount(7, WizardViewData::roomTypes());
        self::assertCount(6, WizardViewData::wallColors());
        self::assertCount(4, WizardViewData::windowTypes());
        self::assertSame('&lt;script&gt;15&lt;/script&gt;', $view->value('square_meters'));
        self::assertSame('checked', $view->checkedAttr('type', 'cocina'));
        self::assertSame('', $view->checkedAttr('type', 'salon'));
        self::assertTrue($view->hasError('type'));
        self::assertFalse($view->hasError('windows'));
        self::assertSame('⚠ Error de prueba', $view->errorText('type'));
        self::assertSame('display:flex;', $view->errorDisplayStyle('type'));
        self::assertSame('display:none;', $view->errorDisplayStyle('windows'));
    }

    public function testErrorLoggerWritesStructuredJsonLogFailSafe(): void
    {
        $logFile = sys_get_temp_dir() . '/lumina_logger_' . uniqid('', true) . '.log';
        $logger  = new ErrorLogger($logFile, '');

        self::assertFalse($logger->hasSentryEnabled());
        $logger->logException(new RuntimeException('Fallo controlado'));

        self::assertFileExists($logFile);
        $content = (string) file_get_contents($logFile);
        self::assertStringContainsString('"level":"critical"', $content);
        self::assertStringContainsString('Fallo controlado', $content);

        @unlink($logFile);
    }
}
