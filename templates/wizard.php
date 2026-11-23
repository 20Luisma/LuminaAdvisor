<?php declare(strict_types=1);
// Template: wizard.php — Contenedor del formulario en 2 pasos
// Variables: $data (array), $errors (array)

use LuminaAdvisor\Presentation\View\WizardViewData;

$view = new WizardViewData($data ?? [], $errors ?? []);
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

  <?php if ($view->hasError('general')): ?>
    <div class="error-banner" role="alert"><?= $view->errorText('general') ?></div>
  <?php endif; ?>

  <?php require __DIR__ . '/partials/wizard_step1.php'; ?>
  <?php require __DIR__ . '/partials/wizard_step2.php'; ?>

</form>
