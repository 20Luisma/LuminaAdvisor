/**
 * LuminaAdvisor — Wizard JS & Copiloto IA (Lumina)
 * Maneja wizard, animaciones de números y drawer flotante de Copiloto IA.
 */

(function () {
  'use strict';

  // ── Estado Global ──────────────────────────────────────────────
  const state = {
    currentStep: 1,
    totalSteps: 2,
    chatHistory: [],
    isSendingChat: false,
    drawerOpen: false,
  };

  // ── Inicialización ─────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', () => {
    initWizard();
    initNumbersAnimation();
    initCopilotDrawer();
  });

  // ── Wizard Form ────────────────────────────────────────────────
  function initWizard() {
    const steps = document.querySelectorAll('.wizard-step');
    if (!steps.length) return;

    renderStep(state.currentStep);

    document.querySelectorAll('[data-action="next"]').forEach(btn => {
      btn.addEventListener('click', goNext);
    });
    document.querySelectorAll('[data-action="prev"]').forEach(btn => {
      btn.addEventListener('click', goPrev);
    });

    document.querySelectorAll('.room-option input[type="radio"]').forEach(radio => {
      radio.addEventListener('change', () => {
        setTimeout(() => goNext(), 300);
      });
    });

    const form = document.getElementById('lighting-form');
    if (form) {
      form.addEventListener('submit', onFormSubmit);
    }
  }

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
    const steps           = document.querySelectorAll('.wizard-step');
    const progressBubbles = document.querySelectorAll('.progress-step');
    const progressLines   = document.querySelectorAll('.progress-line');

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

  function onFormSubmit(e) {
    if (!validateStep(state.currentStep)) {
      e.preventDefault();
      return;
    }
    const btn = document.getElementById('btn-submit');
    if (btn) btn.classList.add('btn-loading');
  }

  function scrollToWizard() {
    const el = document.querySelector('.wizard-progress');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  // ── Números Animados ───────────────────────────────────────────
  function initNumbersAnimation() {
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

  // ── Copiloto Flotante (Drawer & Chat) ──────────────────────────
  function initCopilotDrawer() {
    const fab           = document.getElementById('openCopilotBtn');
    const overlay       = document.getElementById('copilotDrawerOverlay');
    const closeBtn      = document.getElementById('closeCopilotBtn');
    const clearBtn      = document.getElementById('copilotClearBtn');
    const form          = document.getElementById('copilotForm');
    const input         = document.getElementById('copilotInput');
    const messages      = document.getElementById('copilotMessages');
    const calloutBtn    = document.getElementById('btnOpenCopilotFromCallout');

    if (!overlay) return;

    // Abrir Drawer
    function openDrawer() {
      overlay.style.display = 'flex';
      setTimeout(() => {
        overlay.classList.add('open');
      }, 10);
      state.drawerOpen = true;
      setTimeout(() => input?.focus(), 300);
      updateGreetingWithContext();
    }

    // Cerrar Drawer
    function closeDrawer() {
      overlay.classList.remove('open');
      setTimeout(() => {
        overlay.style.display = 'none';
      }, 250);
      state.drawerOpen = false;
    }

    if (fab) fab.addEventListener('click', openDrawer);
    if (calloutBtn) calloutBtn.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);

    // Cerrar haciendo clic fuera del drawer
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeDrawer();
    });

    // Tecla ESC para cerrar
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && state.drawerOpen) closeDrawer();
    });

    // Limpiar chat
    if (clearBtn) {
      clearBtn.addEventListener('click', () => {
        state.chatHistory = [];
        messages.innerHTML = '';
        updateGreetingWithContext(true);
      });
    }

    // Chips de preguntas rápidas
    document.querySelectorAll('.copilot-chip').forEach(chip => {
      chip.addEventListener('click', () => {
        const q = chip.dataset.question;
        if (q && !state.isSendingChat) {
          input.value = q;
          sendMessage(q, messages, input);
        }
      });
    });

    // Enviar mensaje
    if (form) {
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        const text = input.value.trim();
        if (!text || state.isSendingChat) return;
        sendMessage(text, messages, input);
      });
    }
  }

  function getRoomContext() {
    const contextEl = document.getElementById('room-context-data');
    if (contextEl) {
      try {
        return JSON.parse(contextEl.textContent);
      } catch (err) {
        console.error('Error parseando contexto:', err);
      }
    }
    // Contexto genérico si aún no calcularon
    return {
      type: 'hogar',
      square_meters: 15,
      ceiling_height: 2.5,
      wall_color: 'blanco',
      windows: 'mediana',
      total_lumens: 3000,
      light_points: 3,
      color_temperature_k: 3000,
      color_category: 'cálida',
    };
  }

  function updateGreetingWithContext(force = false) {
    const messages = document.getElementById('copilotMessages');
    const ctx = getRoomContext();

    if (!messages) return;

    // Solo actualizar si está vacío o si es forzado por "limpiar"
    if (messages.children.length === 0 || force) {
      const greeting = ctx.type && ctx.type !== 'hogar'
        ? `¡Hola! Soy <strong>Lumina</strong>. Ya tengo cargados los datos de tu <strong>${escapeHtml(ctx.type)} de ${ctx.square_meters} m²</strong> con sus <strong>${ctx.light_points} puntos de luz recomendados</strong>.<br><br>¿Qué duda te quedó sobre la instalación o qué bombilla comprar? Decime y te lo explico sin rodeos.`
        : `¡Hola! Soy <strong>Lumina</strong>, tu asesora de iluminación para el hogar.<br><br>Podés preguntarme lo que quieras sobre cómo calcular tus focos, qué bombillas pedir en la ferretería o cómo elegir luz cálida o neutra.`;

      appendBubble(messages, greeting, 'bot');
    }
  }

  function sendMessage(text, messagesContainer, inputEl) {
    state.isSendingChat = true;
    inputEl.value = '';

    // Burbuja del usuario
    appendBubble(messagesContainer, text, 'user');

    // Burbuja pensando
    const typingBubble = appendBubble(messagesContainer, 'Pensando una respuesta clara y práctica...', 'bot', true);

    const payload = {
      message: text,
      context: getRoomContext(),
      history: state.chatHistory.slice(-6),
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
          state.chatHistory.push({ role: 'user', text: text });
          state.chatHistory.push({ role: 'model', text: data.reply });
        } else {
          appendBubble(messagesContainer, 'Disculpá, no pude procesar la consulta. Probá preguntarme de nuevo.', 'bot');
        }
      })
      .catch(err => {
        typingBubble.remove();
        console.error('Error en fetch chat:', err);
        appendBubble(messagesContainer, 'Hubo un error de conexión con la asesora. Probá de nuevo en unos segundos.', 'bot');
      })
      .finally(() => {
        state.isSendingChat = false;
        inputEl.focus();
      });
  }

  function appendBubble(container, text, role, isTyping = false) {
    const msgDiv = document.createElement('div');
    msgDiv.className = `copilot-msg copilot-msg-${role}`;
    if (isTyping) msgDiv.style.opacity = '0.7';

    const bubble = document.createElement('div');
    bubble.className = 'copilot-bubble';

    // Parse simple newlines or HTML
    bubble.innerHTML = text.includes('<br>') || text.includes('<strong>') || text.includes('<ul>')
      ? text
      : escapeHtml(text).replace(/\n/g, '<br>');

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

})();
