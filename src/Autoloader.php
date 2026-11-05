<?php

declare(strict_types=1);

/**
 * Autoloader PSR-4 manual — sin Composer.
 * Namespace raíz: LuminaAdvisor\ → src/
 */
spl_autoload_register(function (string $class): void {
    $prefix  = 'LuminaAdvisor\\';
    $baseDir = __DIR__ . '/';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

