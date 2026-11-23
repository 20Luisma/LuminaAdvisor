<?php

declare(strict_types=1);

// Front Controller — LuminaAdvisor
// Punto de entrada único. Cero lógica de negocio aquí.

// Cabeceras de endurecimiento HTTP (funciona en Apache y PHP built-in server)
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// Cargar variables de entorno locales si existen
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (is_array($lines)) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                [$k, $v] = explode('=', $line, 2);
                $key = trim($k);
                $val = trim($v);
                $_ENV[$key] = $val;
                putenv("{$key}={$val}");
            }
        }
    }
}

require_once __DIR__ . '/../src/Autoloader.php';

use LuminaAdvisor\Infrastructure\Observability\ErrorLogger;
use LuminaAdvisor\Presentation\Controllers\ChatController;
use LuminaAdvisor\Presentation\Controllers\LightingController;

// Observabilidad fail-safe global (logs JSON locales + Sentry opcional vía SENTRY_DSN)
ErrorLogger::register();

$action = $_GET['action'] ?? '';

if ($action === 'chat') {
    (new ChatController())->handle();
    exit;
}

(new LightingController())->handle();
