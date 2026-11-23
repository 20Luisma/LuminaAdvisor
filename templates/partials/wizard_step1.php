<?php declare(strict_types=1);
/** @var \LuminaAdvisor\Presentation\View\WizardViewData $view */
?>

<!-- ══ STEP 1: Tipo de habitación ═════════════════════════════ -->
<div class="wizard-step active" id="step-1">
  <div class="glass-card">
    <h2 class="glass-card-title">¿Qué habitación querés iluminar?</h2>
    <p class="glass-card-subtitle">Seleccioná el tipo de espacio y calculamos el setup ideal.</p>

    <fieldset class="room-grid" style="border:0;padding:0;margin:0;">
      <legend class="sr-only">Tipo de habitación</legend>
      <?php foreach ($view::roomTypes() as $key => $room): ?>
      <div class="room-option">
        <input
          type="radio"
          name="type"
          aria-label="Tipo de habitación"
          id="type-<?= $key ?>"
          value="<?= $key ?>"
          <?= $view->checkedAttr('type', $key) ?>
        >
        <label for="type-<?= $key ?>">
          <span class="room-icon" aria-hidden="true"><?= $room['icon'] ?></span>
          <span class="room-label"><?= $room['label'] ?></span>
        </label>
      </div>
      <?php endforeach; ?>
    </fieldset>

    <div class="form-error" id="error-type" role="alert" style="<?= $view->errorDisplayStyle('type') ?>margin-top:12px;">
      <?= $view->errorText('type') ?>
    </div>

    <div class="wizard-nav" style="justify-content:flex-end;">
      <button type="button" class="btn btn-primary" data-action="next">
        Continuar →
      </button>
    </div>
  </div>
</div>
