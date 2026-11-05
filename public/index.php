<?php declare(strict_types=1);
// Front Controller — LuminaAdvisor
// Punto de entrada único. No expone lógica de negocio.

require_once __DIR__ . '/../src/Autoloader.php';

use LuminaAdvisor\Presentation\Controllers\LightingController;

(new LightingController())->handle();

