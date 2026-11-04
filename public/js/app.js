/**
 * LuminaAdvisor — Wizard JS
 * Maneja la navegación por pasos, validación client-side y UX premium.
 */

(function () {
  'use strict';

  // ── Estado del wizard ──────────────────────────────────────────
  const state = {
    currentStep: 1,
    totalSteps: 2,
  };

  // ── Refs ───────────────────────────────────────────────────────
  const steps        = document.querySelectorAll('.wizard-step');
  const progressBubbles = document.querySelectorAll('.progress-step');
  const progressLines   = document.querySelectorAll('.progress-line');

  // ── Init ───────────────────────────────────────────────────────
  function init() {
    if (!steps.length) return; // No wizard (resultado ya cargado)

    renderStep(state.currentStep);

    // Botones de navegación
    document.querySelectorAll('[data-action="next"]').forEach(btn => {
      btn.addEventListener('click', goNext);
    });
    document.querySelectorAll('[data-action="prev"]').forEach(btn => {
      btn.addEventListener('click', goPrev);
    });

    // Room cards — auto-avanzar al seleccionar
    document.querySelectorAll('.room-option input[type="radio"]').forEach(radio => {
      radio.addEventListener('change', () => {
        setTimeout(() => goNext(), 300); // pequeño delay para que se vea la selección
      });
    });

    // Submit con loading state
    const form = document.getElementById('lighting-form');
    if (form) {
      form.addEventListener('submit', onFormSubmit);
    }
  }

  // ── Navegación ─────────────────────────────────────────────────
  function goNext() {
    if (!validateStep(state.currentStep)) return;
    if (state.currentStep < state.totalSteps) {
      state.currentStep++;
      renderStep(state.currentStep);
      scrollToWizard();
    }
  }

  function goPrev() {
    if (state.currentStep > 1) {
      state.currentStep--;
      renderStep(state.currentStep);
      scrollToWizard();
    }
  }

  function renderStep(step) {
    steps.forEach((el, idx) => {
      el.classList.toggle('active', idx + 1 === step);
    });

    progressBubbles.forEach((bubble, idx) => {
      bubble.classList.remove('active', 'done');
      if (idx + 1 === step) bubble.classList.add('active');
      if (idx + 1 < step)  bubble.classList.add('done');
    });

    progressLines.forEach((line, idx) => {
      line.classList.toggle('done', idx + 1 < step);
    });
  }

  // ── Validación por paso ────────────────────────────────────────
  function validateStep(step) {
    clearErrors();

    if (step === 1) {
      const roomSelected = document.querySelector('.room-option input[type="radio"]:checked');
      if (!roomSelected) {
        showError('error-type', 'Seleccioná el tipo de habitación para continuar.');
        return false;
      }
    }

    if (step === 2) {
      let valid = true;

      const m2 = parseFloat(document.getElementById('square_meters')?.value ?? '');
      if (!m2 || m2 <= 0 || m2 > 1000) {
        showError('error-m2', 'Ingresá los m² (entre 1 y 1000).');
        valid = false;
      }

      const h = parseFloat(document.getElementById('ceiling_height')?.value ?? '');
      if (!h || h < 2.0 || h > 10.0) {
        showError('error-height', 'Ingresá la altura (entre 2.0 y 10.0 m).');
        valid = false;
      }

      const wallSelected = document.querySelector('.swatch-option input[type="radio"]:checked');
      if (!wallSelected) {
        showError('error-wall', 'Seleccioná el color de las paredes.');
        valid = false;
      }

      const windowsSelected = document.querySelector('.window-option input[type="radio"]:checked');
      if (!windowsSelected) {
        showError('error-windows', 'Seleccioná el tipo de ventanas.');
        valid = false;
      }

      return valid;
    }

    return true;
  }

  // ── Errores ────────────────────────────────────────────────────
  function showError(id, message) {
    const el = document.getElementById(id);
    if (el) {
      el.textContent = '⚠ ' + message;
      el.style.display = 'flex';
    }
  }

  function clearErrors() {
    document.querySelectorAll('.form-error').forEach(el => {
      el.textContent = '';
      el.style.display = 'none';
    });
  }

  // ── Submit ─────────────────────────────────────────────────────
  function onFormSubmit(e) {
    if (!validateStep(state.currentStep)) {
      e.preventDefault();
      return;
    }
    const btn = document.getElementById('btn-submit');
    if (btn) btn.classList.add('btn-loading');
  }

  // ── Scroll suave al wizard ─────────────────────────────────────
  function scrollToWizard() {
    const el = document.querySelector('.wizard-progress');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  // ── Animación de números en resultado ─────────────────────────
  function animateNumbers() {
    document.querySelectorAll('[data-count]').forEach(el => {
      const target  = parseInt(el.dataset.count, 10);
      const decimals = parseInt(el.dataset.decimals ?? '0', 10);
      const duration = 1200;
      const start    = performance.now();

      function update(now) {
        const elapsed  = now - start;
        const progress = Math.min(elapsed / duration, 1);
        // ease out cubic
        const eased = 1 - Math.pow(1 - progress, 3);
        const value = target * eased;
        el.textContent = decimals > 0
          ? value.toFixed(decimals)
          : Math.round(value).toLocaleString('es-ES');
        if (progress < 1) requestAnimationFrame(update);
      }

      requestAnimationFrame(update);
    });
  }

  // ── Arranque ───────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', () => {
    init();
    // Si hay resultado (PHP renderizó la sección de resultado), animar
    if (document.querySelector('[data-count]')) {
      animateNumbers();
    }
  });

})();
