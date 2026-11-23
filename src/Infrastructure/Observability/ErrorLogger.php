<?php

declare(strict_types=1);

namespace LuminaAdvisor\Infrastructure\Observability;

/**
 * Logger estructurado fail-safe con soporte opcional de Sentry vía SENTRY_DSN.
 * Nunca interrumpe la ejecución de la aplicación si falla el disco o falta configuración.
 */
final class ErrorLogger
{
    private string $logFile;
    private string $sentryDsn;

    public function __construct(?string $logFile = null, ?string $sentryDsn = null)
    {
        $this->logFile   = $logFile ?? dirname(__DIR__, 3) . '/storage/logs/app.log';
        $this->sentryDsn = $sentryDsn ?? (string) ($_ENV['SENTRY_DSN'] ?? getenv('SENTRY_DSN') ?: '');
    }

    /**
     * Registra handlers globales de excepciones y errores PHP de forma segura.
     */
    public static function register(?self $logger = null): void
    {
        $instance = $logger ?? new self();

        set_exception_handler(static function (\Throwable $exception) use ($instance): void {
            $instance->logException($exception);
            if (!headers_sent()) {
                http_response_code(500);
            }
        });

        set_error_handler(static function (int $severity, string $message, string $file, int $line) use ($instance): bool {
            if (!(error_reporting() & $severity)) {
                return false;
            }
            $instance->log('error', $message, [
                'severity' => $severity,
                'file'     => $file,
                'line'     => $line,
            ]);
            return false;
        });
    }

    /**
     * @param array<string, mixed> $context
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $entry = [
            'timestamp' => gmdate('c'),
            'level'     => $level,
            'app'       => 'LuminaAdvisor',
            'message'   => $message,
            'context'   => $context,
        ];

        $this->writeJsonLine($entry);
    }

    public function logException(\Throwable $exception): void
    {
        $this->log('critical', $exception->getMessage(), [
            'exception' => $exception::class,
            'file'      => $exception->getFile(),
            'line'      => $exception->getLine(),
        ]);

        if ($this->sentryDsn !== '') {
            $this->notifySentryFailSafe($exception);
        }
    }

    public function hasSentryEnabled(): bool
    {
        return $this->sentryDsn !== '';
    }

    /**
     * @param array<string, mixed> $entry
     */
    private function writeJsonLine(array $entry): void
    {
        try {
            $dir = dirname($this->logFile);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $encoded = json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($encoded !== false) {
                @file_put_contents($this->logFile, $encoded . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        } catch (\Throwable) {
            // Fail-safe: nunca romper la app por fallo de logging
        }
    }

    private function notifySentryFailSafe(\Throwable $exception): void
    {
        try {
            $parsed = parse_url($this->sentryDsn);
            if (!is_array($parsed) || empty($parsed['host']) || empty($parsed['user']) || empty($parsed['path'])) {
                return;
            }

            $projectId = ltrim($parsed['path'], '/');
            $scheme    = $parsed['scheme'] ?? 'https';
            $endpoint  = "{$scheme}://{$parsed['host']}/api/{$projectId}/store/";
            $payload   = json_encode([
                'event_id' => bin2hex(random_bytes(16)),
                'level'    => 'error',
                'platform' => 'php',
                'message'  => $exception->getMessage(),
            ]);

            if ($payload === false || !function_exists('curl_init')) {
                return;
            }

            $ch = curl_init($endpoint);
            if ($ch === false) {
                return;
            }
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 2,
                CURLOPT_CONNECTTIMEOUT => 1,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    "X-Sentry-Auth: Sentry sentry_version=7, sentry_key={$parsed['user']}",
                ],
                CURLOPT_POSTFIELDS     => $payload,
            ]);
            @curl_exec($ch);
            curl_close($ch);
        } catch (\Throwable) {
            // Fail-safe
        }
    }
}
