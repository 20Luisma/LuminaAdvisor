<?php declare(strict_types=1);
/** @var \LuminaAdvisor\Presentation\View\WizardViewData $view */
?>

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
          value="<?= $view->value('square_meters') ?>"
          min="1" max="1000" step="0.5"
          placeholder="Ej: 15"
          autocomplete="off"
        >
        <div class="form-error" id="error-m2" role="alert" style="<?= $view->errorDisplayStyle('square_meters') ?>">
          <?= $view->errorText('square_meters') ?>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="ceiling_height">Altura del techo (m)</label>
        <input
          class="form-input"
          type="number"
          id="ceiling_height"
          name="ceiling_height"
          value="<?= $view->value('ceiling_height') ?>"
          min="2.0" max="10.0" step="0.1"
          placeholder="Ej: 2.5"
          autocomplete="off"
        >
        <div class="form-error" id="error-height" role="alert" style="<?= $view->errorDisplayStyle('ceiling_height') ?>">
          <?= $view->errorText('ceiling_height') ?>
        </div>
      </div>
    </div>

    <!-- Color de paredes -->
    <fieldset class="form-group mt-4" style="border:0;padding:0;margin-left:0;margin-right:0;">
      <legend class="form-label">Color de las paredes</legend>
      <div class="swatch-grid">
        <?php foreach ($view::wallColors() as $key => $color): ?>
        <div class="swatch-option">
          <input
            type="radio"
            name="wall_color"
            aria-label="Color de pared"
            id="wall-<?= $key ?>"
            value="<?= $key ?>"
            <?= $view->checkedAttr('wall_color', $key) ?>
          >
          <label for="wall-<?= $key ?>">
            <span class="swatch-circle <?= $color['class'] ?>" aria-hidden="true"></span>
            <span class="swatch-name"><?= $color['label'] ?></span>
          </label>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="form-error" id="error-wall" role="alert" style="<?= $view->errorDisplayStyle('wall_color') ?>">
        <?= $view->errorText('wall_color') ?>
      </div>
      <p class="form-hint">Afecta al factor de reflexión lumínica de la habitación.</p>
    </fieldset>

    <!-- Ventanas / Luz natural -->
    <fieldset class="form-group mt-4" style="border:0;padding:0;margin-left:0;margin-right:0;">
      <legend class="form-label">Luz natural</legend>
      <div class="windows-grid">
        <?php foreach ($view::windowTypes() as $key => $win): ?>
        <div class="window-option">
          <input
            type="radio"
            name="windows"
            aria-label="Nivel de luz natural"
            id="win-<?= $key ?>"
            value="<?= $key ?>"
            <?= $view->checkedAttr('windows', $key) ?>
          >
          <label for="win-<?= $key ?>">
            <span class="window-icon" aria-hidden="true"><?= $win['icon'] ?></span>
            <span>
              <strong style="display:block;font-weight:600;"><?= $win['label'] ?></strong>
              <small style="font-size:0.75rem;color:var(--text-muted);"><?= $win['hint'] ?></small>
            </span>
          </label>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="form-error" id="error-windows" role="alert" style="<?= $view->errorDisplayStyle('windows') ?>">
        <?= $view->errorText('windows') ?>
      </div>
    </fieldset>

    <!-- Navegación -->
    <div class="wizard-nav">
      <button type="button" class="btn btn-ghost" data-action="prev">← Atrás</button>
      <button type="submit" class="btn btn-primary" id="btn-submit">
        ⚡ Calcular iluminación
      </button>
    </div>
  </div>
</div>
