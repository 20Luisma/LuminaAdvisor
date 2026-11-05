<?php declare(strict_types=1);
// Template: result.php — Resultado visual + Croquis 2D + Asesor IA + Chat interactivo
// Variables: $result (LightRecommendation), $data (array del formulario), $aiAdvice (array|null)

/** @var \LuminaAdvisor\Domain\Entities\LightRecommendation $result */
/** @var array $data */
/** @var array|null $aiAdvice */

$res = $result->toArray();

$roomLabels = [
    'dormitorio' => 'Dormitorio', 'salon'   => 'Salón',
    'cocina'     => 'Cocina',    'bano'     => 'Baño',
    'despacho'   => 'Despacho',  'pasillo'  => 'Pasillo',
    'garaje'     => 'Garaje',
];

$roomType   = htmlspecialchars($data['type'] ?? '', ENT_QUOTES, 'UTF-8');
$roomLabel  = $roomLabels[$roomType] ?? ucfirst($roomType);
$m2         = htmlspecialchars($data['square_meters'] ?? '', ENT_QUOTES, 'UTF-8');
$height     = htmlspecialchars($data['ceiling_height'] ?? '2.5', ENT_QUOTES, 'UTF-8');
$wallColor  = htmlspecialchars($data['wall_color'] ?? 'blanco', ENT_QUOTES, 'UTF-8');
$windows    = htmlspecialchars($data['windows'] ?? 'pequena', ENT_QUOTES, 'UTF-8');
$colorCat   = $res['color_category']; // calida | neutra | fria
$colorLabels = ['calida' => 'Cálida', 'neutra' => 'Neutra', 'fria' => 'Fría'];
$colorLabel  = $colorLabels[$colorCat] ?? $colorCat;
$pointsCount = max(1, $res['light_points']);

// Contexto serializado seguro para el Chat JS
$roomContextJson = json_encode([
    'type'                => $roomLabel,
    'square_meters'       => (float) $m2,
    'ceiling_height'      => (float) $height,
    'wall_color'          => $wallColor,
    'windows'             => $windows,
    'total_lumens'        => $res['total_lumens'],
    'light_points'        => $pointsCount,
    'color_temperature_k' => $res['color_temperature_k'],
    'color_category'      => $colorLabel,
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>

<div class="result-wrapper">

  <!-- HERO: Lúmenes totales -->
  <div class="result-hero">
    <div class="result-badge">✓ Cálculo completado · UNE-EN 12464-1</div>
    <div class="result-lumens" data-count="<?= $res['total_lumens'] ?>">0</div>
    <div class="result-lumens-label">lúmenes necesarios · <?= $roomLabel ?> de <?= $m2 ?> m²</div>
  </div>

  <!-- GRID DE DATOS -->
  <div class="result-grid">

    <!-- Puntos de luz -->
    <div class="result-card">
      <div class="result-card-icon">💡</div>
      <div class="result-card-value accent" data-count="<?= $res['light_points'] ?>">0</div>
      <div class="result-card-label">Puntos de luz</div>
      <div class="result-card-desc">Distribución uniforme recomendada</div>
    </div>

    <!-- Temperatura de color -->
    <div class="result-card">
      <div class="result-card-icon">🌡️</div>
      <div class="result-card-value"><?= $res['color_temperature_k'] ?>K</div>
      <div class="result-card-label">Temperatura de color</div>
      <div class="result-card-desc"><?= $colorLabel ?> — <?= $res['color_temperature_k'] ?>K</div>
      <div class="temp-bar temp-bar-<?= htmlspecialchars($colorCat, ENT_QUOTES, 'UTF-8') ?>"></div>
    </div>

    <!-- Consumo kWh -->
    <div class="result-card">
      <div class="result-card-icon">⚡</div>
      <div class="result-card-value" data-count="<?= (int)($res['monthly_kwh'] * 100) ?>" data-decimals="2">0</div>
      <div class="result-card-label">kWh / mes</div>
      <div class="result-card-desc">Basado en 5h/día · 30 días/mes</div>
    </div>

    <!-- Coste mensual -->
    <div class="result-card result-card-cost">
      <div class="result-card-icon">💶</div>
      <div class="result-card-value accent"><?= number_format($res['monthly_cost_eur'], 2, ',', '.') ?> €</div>
      <div class="result-card-label">Coste estimado / mes</div>
      <div class="result-card-desc">a 0,18 €/kWh (tarifa media España)</div>
    </div>

    <!-- Tipo de luminaria — full width -->
    <div class="result-card result-card-full">
      <div class="result-card-icon">🔦</div>
      <div style="font-size:1rem;font-weight:700;color:var(--text-primary);margin-bottom:4px;">
        <?= htmlspecialchars($res['luminaire_type'], ENT_QUOTES, 'UTF-8') ?>
      </div>
      <div class="result-card-label">Luminaria recomendada</div>
      <div class="result-card-desc" style="margin-top:8px;">
        Potencia estimada del setup completo:
        <strong style="color:var(--accent);"><?= number_format($res['estimated_watts'], 1, ',', '.') ?> W</strong>
      </div>
    </div>

  </div>

  <!-- CROQUIS 2D: Plano esquemático de distribución -->
  <div class="blueprint-card">
    <div class="blueprint-header">
      <div class="blueprint-title">
        <span>🗺️</span>
        <h4>Croquis 2D: Distribución de puntos en tu <?= $roomLabel ?></h4>
      </div>
      <span class="blueprint-legend">Vista cenital esquemática</span>
    </div>

    <div class="blueprint-canvas">
      <!-- Indicadores de entorno -->
      <div class="blueprint-wall-top">Pared norte / Ventana <?= ($windows !== 'ninguna') ? '🪟' : '' ?></div>
      <div class="blueprint-door">🚪 Entrada</div>

      <!-- Puntos de luz renderizados dinámicamente -->
      <div class="blueprint-lights-grid points-<?= min(8, $pointsCount) ?>">
        <?php for ($i = 1; $i <= min(8, $pointsCount); $i++): ?>
          <div class="blueprint-light-spot">
            <div class="light-halo"></div>
            <div class="light-bulb">💡</div>
            <span class="light-spot-tag">Punto <?= $i ?></span>
          </div>
        <?php endfor; ?>
      </div>
    </div>

    <div class="blueprint-footer">
      <span>💡 Total sugerido: <strong><?= $pointsCount ?> puntos de luz</strong></span>
      <span>📏 Espaciado: <strong>~1 a 1.2 m entre focos</strong></span>
    </div>
  </div>

  <?php if (!empty($aiAdvice)): ?>
  <!-- ASESOR DE INTERIORISMO POR IA -->
  <div class="ai-advisor-card">
    <div class="ai-advisor-header">
      <span class="ai-advisor-badge">✨ Asesor de Interiorismo · Gemini IA</span>
      <h3 class="ai-advisor-title">Recomendaciones de diseño para tu <?= $roomLabel ?></h3>
    </div>
    
    <div class="ai-advisor-items">
      <div class="ai-advisor-item">
        <div class="ai-advisor-icon">📐</div>
        <div>
          <strong>Distribución en el espacio</strong>
          <p><?= $aiAdvice['distribucion'] ?></p>
        </div>
      </div>
      
      <div class="ai-advisor-item">
        <div class="ai-advisor-icon">🕯️</div>
        <div>
          <strong>Capas de luz y ambiente</strong>
          <p><?= $aiAdvice['estilo_ambiente'] ?></p>
        </div>
      </div>
      
      <div class="ai-advisor-item">
        <div class="ai-advisor-icon">💡</div>
        <div>
          <strong>Consejo práctico</strong>
          <p><?= $aiAdvice['consejo_pro'] ?></p>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- CHAT INTERACTIVO CON LUMINA (EL AGENTE) -->
  <div class="chat-advisor-card" id="chat-advisor">
    <div class="chat-header">
      <div class="chat-avatar">🤖</div>
      <div>
        <h4 class="chat-title">Preguntale a Lumina (Tu Asesora)</h4>
        <p class="chat-subtitle">¿Te quedó alguna duda sobre cómo poner las luces o qué bombilla comprar? Preguntale en criollo.</p>
      </div>
    </div>

    <!-- Chips de preguntas rápidas -->
    <div class="chat-chips">
      <button type="button" class="chat-chip" data-question="¿Qué bombilla exacta tengo que pedir en Leroy Merlin o la ferretería?">🛒 ¿Qué bombilla pido en la tienda?</button>
      <button type="button" class="chat-chip" data-question="Explicame de forma bien simple y paso a paso dónde ubicar las luces.">📐 Explicame la distribución simple</button>
      <button type="button" class="chat-chip" data-question="¿Cómo puedo regular la intensidad o crear luz de noche?">🎛️ ¿Cómo regulo la intensidad?</button>
    </div>

    <!-- Historial de mensajes -->
    <div class="chat-messages" id="chat-messages">
      <div class="chat-msg chat-msg-bot">
        <div class="chat-bubble">
          ¡Buenas! Ya tengo en mente tu <strong><?= $roomLabel ?> de <?= $m2 ?> m²</strong>. ¿Qué duda te quedó sobre la instalación, las bombillas o la colocación? Decime y te lo explico sin palabras raras.
        </div>
      </div>
    </div>

    <!-- Input bar -->
    <form id="chat-form" class="chat-input-bar">
      <input type="text" id="chat-input" class="chat-input" placeholder="Preguntale lo que quieras (ej: ¿dónde pongo el interruptor?)..." autocomplete="off">
      <button type="submit" class="btn btn-primary chat-send-btn" id="chat-send">
        <span>Enviar</span> ➔
      </button>
    </form>
  </div>

  <!-- JSON Context oculto para el Chat JS -->
  <script id="room-context-data" type="application/json">
    <?= $roomContextJson ?>
  </script>

  <!-- CTA: Calcular otra habitación -->
  <div class="text-center mt-8">
    <a href="./" class="btn btn-ghost" style="display:inline-flex;">
      ← Calcular otra habitación
    </a>
  </div>

</div>
