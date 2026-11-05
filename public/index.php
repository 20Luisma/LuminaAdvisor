<?php

declare(strict_types=1);

// Front Controller — LuminaAdvisor
// Punto de entrada único. Cero lógica de negocio aquí.

// Cargar variables de entorno locales si existen
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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

require_once __DIR__ . '/../src/Autoloader.php';

use LuminaAdvisor\Presentation\Controllers\ChatController;
use LuminaAdvisor\Presentation\Controllers\LightingController;

$action = $_GET['action'] ?? '';

if ($action === 'chat') {
    (new ChatController())->handle();
    exit;
}

(new LightingController())->handle();
