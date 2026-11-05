<?php declare(strict_types=1);
// Template: result.php — Resultado visual de la recomendación
// Variables: $result (LightRecommendation), $data (array del formulario)

/** @var \LuminaAdvisor\Domain\Entities\LightRecommendation $result */
/** @var array $data */

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
$colorCat   = $res['color_category']; // calida | neutra | fria
$colorLabels = ['calida' => 'Cálida', 'neutra' => 'Neutra', 'fria' => 'Fría'];
$colorLabel  = $colorLabels[$colorCat] ?? $colorCat;
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
          <strong>Consejo profesional</strong>
          <p><?= $aiAdvice['consejo_pro'] ?></p>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- CTA: Calcular otra habitación -->
  <div class="text-center mt-8">
    <a href="./" class="btn btn-ghost" style="display:inline-flex;">
      ← Calcular otra habitación
    </a>
  </div>

</div>

