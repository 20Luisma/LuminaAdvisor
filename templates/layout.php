<?php declare(strict_types=1);
// Template: layout.php — Shell HTML completo
// Variables disponibles: $data, $errors, $result (de LightingController)

$cssFile = __DIR__ . '/../public/css/style.css';
$jsFile  = __DIR__ . '/../public/js/app.js';
$cssVer  = file_exists($cssFile) ? (string) filemtime($cssFile) : (string) time();
$jsVer   = file_exists($jsFile)  ? (string) filemtime($jsFile)  : (string) time();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="LuminaAdvisor — Calcula la iluminación perfecta para tu hogar según la norma UNE-EN 12464-1">
  <title>LuminaAdvisor — Asesor de Iluminación</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css?v=<?= $cssVer ?>">
</head>
<body>

<div class="app-wrapper">

  <!-- HEADER -->
  <header class="app-header">
    <div class="container">
      <div class="app-logo">
        <div class="logo-icon">💡</div>
        <span class="logo-text">LuminaAdvisor</span>
      </div>
      <p class="app-tagline">Calcula la iluminación perfecta para cada habitación · Norma UNE-EN 12464-1</p>
    </div>
  </header>

  <!-- MAIN -->
  <main class="app-main">
    <div class="container">
      <?php if ($result !== null): ?>
        <?php require __DIR__ . '/result.php'; ?>
      <?php else: ?>
        <?php require __DIR__ . '/wizard.php'; ?>
      <?php endif; ?>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="app-footer">
    <div class="container">
      <p>LuminaAdvisor · Estándares <strong>UNE-EN 12464-1</strong> · Martín © 2026</p>
    </div>
  </footer>

</div>

<?php require __DIR__ . '/copilot.php'; ?>

<script src="js/app.js?v=<?= $jsVer ?>"></script>
</body>
</html>
