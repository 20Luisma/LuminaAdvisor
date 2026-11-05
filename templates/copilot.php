<?php declare(strict_types=1);
// Template: copilot.php — Drawer Flotante del Agente IA (Lumina)
?>

<!-- BOTÓN FLOTANTE DEL AGENTE IA -->
<button id="openCopilotBtn" class="copilot-fab" title="Hablar con Lumina, tu Asesora de Iluminación">
  <span class="fab-pulse"></span>
  <span class="fab-icon">💡</span>
  <span class="fab-label">Asesora Lumina</span>
</button>

<!-- DRAWER / VENTANA DEL AGENTE IA -->
<div id="copilotDrawerOverlay" class="copilot-drawer-overlay">
  <div class="copilot-drawer">
    
    <!-- Header -->
    <div class="copilot-header">
      <div class="copilot-header-info">
        <div class="copilot-avatar">💡</div>
        <div>
          <h3 class="copilot-title">Lumina — Asesora IA</h3>
          <span class="copilot-status"><span class="live-dot"></span> En línea · Gemini Flash</span>
        </div>
      </div>
      <div class="copilot-header-actions">
        <button id="copilotClearBtn" class="copilot-action-btn" title="Limpiar conversación">🧹 Limpiar</button>
        <button id="closeCopilotBtn" class="copilot-close-btn" title="Cerrar ventana">✕</button>
      </div>
    </div>

    <!-- Chips de preguntas rápidas sugeridas -->
    <div class="copilot-chips" id="copilotChips">
      <button type="button" class="copilot-chip" data-question="¿Qué bombilla exacta pido en Leroy Merlin o en la tienda para no equivocarme?">🛒 ¿Qué bombilla pido en la tienda?</button>
      <button type="button" class="copilot-chip" data-question="Explicame de forma bien simple y paso a paso cómo ubicar las luces en el techo y paredes.">📐 Explicame la distribución simple</button>
      <button type="button" class="copilot-chip" data-question="¿Qué tono de luz me conviene más para no cansar la vista y sentirme cómodo?">🌡️ ¿Luz cálida o neutra?</button>
      <button type="button" class="copilot-chip" data-question="¿Cómo puedo regular la intensidad con un dimmer o crear luz suave de noche?">🎛️ ¿Cómo regulo la intensidad?</button>
    </div>

    <!-- Historial de Mensajes -->
    <div class="copilot-messages" id="copilotMessages">
      <div class="copilot-msg copilot-msg-bot">
        <div class="copilot-bubble">
          ¡Hola! Soy <strong>Lumina</strong>, tu asesora de iluminación y diseño para el hogar.
          <br><br>
          Preguntame lo que quieras sobre cómo distribuir tus focos, qué bombillas pedir en la tienda o cómo crear un ambiente acogedor. <strong>Te respondo en criollo y sin palabras raras.</strong>
        </div>
      </div>
    </div>

    <!-- Input Bar -->
    <form id="copilotForm" class="copilot-input-bar">
      <input type="text" id="copilotInput" class="copilot-text-input" placeholder="Escribile a Lumina (ej: ¿dónde pongo el foco?)..." autocomplete="off">
      <button type="submit" id="copilotSendBtn" class="copilot-send-btn" title="Enviar mensaje">
        ➤
      </button>
    </form>

  </div>
</div>
