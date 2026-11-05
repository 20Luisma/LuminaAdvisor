<?php declare(strict_types=1);
// Template: wizard.php — Formulario en 2 pasos
// Variables: $data (array), $errors (array)

$d = $data ?? [];
$e = $errors ?? [];

// Helper para recuperar valor previo con escape XSS
$val = fn(string $key): string => htmlspecialchars($d[$key] ?? '', ENT_QUOTES, 'UTF-8');
$checked = fn(string $field, string $value): string => (($d[$field] ?? '') === $value) ? 'checked' : '';

$roomTypes = [
    'dormitorio' => ['icon' => '🛏️',  'label' => 'Dormitorio'],
    'salon'      => ['icon' => '🛋️',  'label' => 'Salón'],
    'cocina'     => ['icon' => '🍳',  'label' => 'Cocina'],
    'bano'       => ['icon' => '🚿',  'label' => 'Baño'],
    'despacho'   => ['icon' => '💼',  'label' => 'Despacho'],
    'pasillo'    => ['icon' => '🚪',  'label' => 'Pasillo'],
    'garaje'     => ['icon' => '🚗',  'label' => 'Garaje'],
];

$wallColors = [
    'blanco' => ['class' => 'swatch-blanco', 'label' => 'Blanco'],
    'beige'  => ['class' => 'swatch-beige',  'label' => 'Beige'],
    'gris'   => ['class' => 'swatch-gris',   'label' => 'Gris'],
    'azul'   => ['class' => 'swatch-azul',   'label' => 'Azul'],
    'rojo'   => ['class' => 'swatch-rojo',   'label' => 'Rojo'],
    'negro'  => ['class' => 'swatch-negro',  'label' => 'Oscuro'],
];

$windowTypes = [
    'ninguna'  => ['icon' => '🪟', 'label' => 'Sin ventanas',    'hint' => '100% iluminación artificial'],
    'pequena'  => ['icon' => '🔲', 'label' => 'Ventana pequeña', 'hint' => 'Poca luz natural'],
    'grande'   => ['icon' => '🔳', 'label' => 'Ventana grande',  'hint' => 'Buena luz natural'],
    'muchas'   => ['icon' => '☀️', 'label' => 'Muchas ventanas', 'hint' => 'Máxima luz natural'],
];
?>

<!-- Progress Bar -->
<div class="wizard-progress">
  <div class="progress-step active" id="pb-step-1">
    <div class="progress-step-bubble">1</div>
    <span class="progress-step-label">Habitación</span>
  </div>
  <div class="progress-line" id="pb-line-1"></div>
  <div class="progress-step" id="pb-step-2">
    <div class="progress-step-bubble">2</div>
    <span class="progress-step-label">Detalles</span>
  </div>
</div>

<!-- FORM -->
<form method="POST" action="" id="lighting-form" novalidate>

  <?php if (!empty($e['general'])): ?>
    <div class="error-banner">⚠ <?= $e['general'] ?></div>
  <?php endif; ?>

  <!-- ══ STEP 1: Tipo de habitación ═════════════════════════════ -->
  <div class="wizard-step active" id="step-1">
    <div class="glass-card">
      <h2 class="glass-card-title">¿Qué habitación querés iluminar?</h2>
      <p class="glass-card-subtitle">Seleccioná el tipo de espacio y calculamos el setup ideal.</p>

      <div class="room-grid">
        <?php foreach ($roomTypes as $key => $room): ?>
        <div class="room-option">
          <input
            type="radio"
            name="type"
            id="type-<?= $key ?>"
            value="<?= $key ?>"
            <?= $checked('type', $key) ?>
          >
          <label for="type-<?= $key ?>">
            <span class="room-icon"><?= $room['icon'] ?></span>
            <span class="room-label"><?= $room['label'] ?></span>
          </label>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="form-error" id="error-type" style="display:none;margin-top:12px;">
        <?= !empty($e['type']) ? '⚠ ' . htmlspecialchars($e['type'], ENT_QUOTES, 'UTF-8') : '' ?>
      </div>

      <div class="wizard-nav" style="justify-content:flex-end;">
        <button type="button" class="btn btn-primary" data-action="next">
          Continuar →
        </button>
      </div>
    </div>
  </div>

  <!-- ══ STEP 2: Dimensiones + Paredes + Ventanas ═══════════════ -->
  <div class="wizard-step" id="step-2">
    <div class="glass-card">
      <h2 class="glass-card-title">Dimensiones y características</h2>
      <p class="glass-card-subtitle">Con estos datos aplicamos la fórmula UNE-EN 12464-1.</p>

      <!-- m² y altura -->
      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="square_meters">Superficie (m²)</label>
          <input
            class="form-input"
            type="number"
            id="square_meters"
            name="square_meters"
            value="<?= $val('square_meters') ?>"
            min="1" max="1000" step="0.5"
            placeholder="Ej: 15"
            autocomplete="off"
          >
          <div class="form-error" id="error-m2" style="display:none;">
            <?= !empty($e['square_meters']) ? '⚠ ' . htmlspecialchars($e['square_meters'], ENT_QUOTES, 'UTF-8') : '' ?>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="ceiling_height">Altura del techo (m)</label>
          <input
            class="form-input"
            type="number"
            id="ceiling_height"
            name="ceiling_height"
            value="<?= $val('ceiling_height') ?>"
            min="2.0" max="10.0" step="0.1"
            placeholder="Ej: 2.5"
            autocomplete="off"
          >
          <div class="form-error" id="error-height" style="display:none;">
            <?= !empty($e['ceiling_height']) ? '⚠ ' . htmlspecialchars($e['ceiling_height'], ENT_QUOTES, 'UTF-8') : '' ?>
          </div>
        </div>
      </div>

      <!-- Color de paredes -->
      <div class="form-group mt-4">
        <label class="form-label">Color de las paredes</label>
        <div class="swatch-grid">
          <?php foreach ($wallColors as $key => $color): ?>
          <div class="swatch-option">
            <input
              type="radio"
              name="wall_color"
              id="wall-<?= $key ?>"
              value="<?= $key ?>"
              <?= $checked('wall_color', $key) ?>
            >
            <label for="wall-<?= $key ?>">
              <div class="swatch-circle <?= $color['class'] ?>"></div>
              <span class="swatch-name"><?= $color['label'] ?></span>
            </label>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="form-error" id="error-wall" style="display:none;">
          <?= !empty($e['wall_color']) ? '⚠ ' . htmlspecialchars($e['wall_color'], ENT_QUOTES, 'UTF-8') : '' ?>
        </div>
        <p class="form-hint">Afecta al factor de reflexión lumínica de la habitación.</p>
      </div>

      <!-- Ventanas / Luz natural -->
      <div class="form-group mt-4">
        <label class="form-label">Luz natural</label>
        <div class="windows-grid">
          <?php foreach ($windowTypes as $key => $win): ?>
          <div class="window-option">
            <input
              type="radio"
              name="windows"
              id="win-<?= $key ?>"
              value="<?= $key ?>"
              <?= $checked('windows', $key) ?>
            >
            <label for="win-<?= $key ?>">
              <span class="window-icon"><?= $win['icon'] ?></span>
              <div>
                <div style="font-weight:600;"><?= $win['label'] ?></div>
                <div style="font-size:0.75rem;color:var(--text-muted);"><?= $win['hint'] ?></div>
              </div>
            </label>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="form-error" id="error-windows" style="display:none;">
          <?= !empty($e['windows']) ? '⚠ ' . htmlspecialchars($e['windows'], ENT_QUOTES, 'UTF-8') : '' ?>
        </div>
      </div>

      <!-- Navegación -->
      <div class="wizard-nav">
        <button type="button" class="btn btn-ghost" data-action="prev">← Atrás</button>
        <button type="submit" class="btn btn-primary" id="btn-submit">
          ⚡ Calcular iluminación
        </button>
      </div>
    </div>
  </div>

</form>

