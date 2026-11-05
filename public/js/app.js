/**
 * LuminaAdvisor — Wizard JS & Chat Interactivo
 * Maneja la navegación por pasos, validación client-side, animación y chat con IA.
 */

(function () {
  'use strict';

  // ── Estado del wizard ──────────────────────────────────────────
  const state = {
    currentStep: 1,
    totalSteps: 2,
    chatHistory: [],
    isSendingChat: false,
  };

  // ── Refs ───────────────────────────────────────────────────────
  const steps           = document.querySelectorAll('.wizard-step');
  const progressBubbles = document.querySelectorAll('.progress-step');
  const progressLines   = document.querySelectorAll('.progress-line');

  // ── Init ───────────────────────────────────────────────────────
  function init() {
    if (steps.length > 0) {
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
          setTimeout(() => goNext(), 300);
        });
      });

      // Submit con loading state
      const form = document.getElementById('lighting-form');
      if (form) {
        form.addEventListener('submit', onFormSubmit);
      }
    }

    // Inicializar chat si estamos en la vista de resultados
    initChat();
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
      const target   = parseInt(el.dataset.count, 10);
      const decimals = parseInt(el.dataset.decimals ?? '0', 10);
      const duration = 1200;
      const start    = performance.now();

      function update(now) {
        const elapsed  = now - start;
        const progress = Math.min(elapsed / duration, 1);
        const eased    = 1 - Math.pow(1 - progress, 3);
        const value    = target * eased;
        el.textContent = decimals > 0
          ? value.toFixed(decimals)
          : Math.round(value).toLocaleString('es-ES');
        if (progress < 1) requestAnimationFrame(update);
      }

      requestAnimationFrame(update);
    });
  }

  // ── Chat Interactivo con el Asesor (Lumina) ───────────────────
  function initChat() {
    const chatForm     = document.getElementById('chat-form');
    const chatInput    = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    const contextEl    = document.getElementById('room-context-data');

    if (!chatForm || !chatInput || !chatMessages || !contextEl) return;

    let roomContext = {};
    try {
      roomContext = JSON.parse(contextEl.textContent);
    } catch (err) {
      console.error('Error parseando contexto de habitación:', err);
    }

    // Chips de preguntas sugeridas
    document.querySelectorAll('.chat-chip').forEach(chip => {
      chip.addEventListener('click', () => {
        const q = chip.dataset.question;
        if (q && !state.isSendingChat) {
          chatInput.value = q;
          sendChatMessage(q, roomContext, chatMessages, chatInput);
        }
      });
    });

    // Envío por formulario
    chatForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const text = chatInput.value.trim();
      if (!text || state.isSendingChat) return;
      sendChatMessage(text, roomContext, chatMessages, chatInput);
    });
  }

  function sendChatMessage(text, context, messagesContainer, inputEl) {
    state.isSendingChat = true;
    inputEl.value = '';

    // Agregar mensaje del usuario en UI
    appendBubble(messagesContainer, text, 'user');

    // Agregar indicador de "escribiendo..."
    const typingBubble = appendBubble(messagesContainer, 'Pensando una respuesta clara...', 'bot', true);

    // Preparar payload
    const payload = {
      message: text,
      context: context,
      history: state.chatHistory.slice(-6), // últimos 6 mensajes de memoria
    };

    fetch('index.php?action=chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    })
      .then(res => res.json())
      .then(data => {
        typingBubble.remove();
        if (data.ok && data.reply) {
          appendBubble(messagesContainer, data.reply, 'bot');
          // Guardar en historial
          state.chatHistory.push({ role: 'user', text: text });
          state.chatHistory.push({ role: 'model', text: data.reply });
        } else {
          appendBubble(messagesContainer, 'Disculpá, no pude procesar la consulta. Probá preguntarme de nuevo.', 'bot');
        }
      })
      .catch(err => {
        typingBubble.remove();
        console.error('Error en fetch chat:', err);
        appendBubble(messagesContainer, 'Hubo un error de conexión con el asesor. Verificá tu red y probá de nuevo.', 'bot');
      })
      .finally(() => {
        state.isSendingChat = false;
        inputEl.focus();
      });
  }

  function appendBubble(container, text, role, isTyping = false) {
    const msgDiv = document.createElement('div');
    msgDiv.className = `chat-msg chat-msg-${role}`;
    if (isTyping) msgDiv.style.opacity = '0.7';

    const bubble = document.createElement('div');
    bubble.className = 'chat-bubble';

    // Parse simple markdown-like newlines or bold
    const formatted = escapeHtml(text).replace(/\n/g, '<br>');
    bubble.innerHTML = formatted;

    msgDiv.appendChild(bubble);
    container.appendChild(msgDiv);
    container.scrollTop = container.scrollHeight;
    return msgDiv;
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  // ── Arranque ───────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', () => {
    init();
    if (document.querySelector('[data-count]')) {
      animateNumbers();
    }
  });

})();
