const { test, expect } = require('@playwright/test');

test.describe('LuminaAdvisor E2E', () => {
  test('carga la vista principal con WCAG lang="es" y headers de seguridad', async ({ page }) => {
    const response = await page.goto('/');
    expect(response.status()).toBe(200);
    expect(response.headers()['x-content-type-options']).toBe('nosniff');
    expect(response.headers()['x-frame-options']).toBe('SAMEORIGIN');

    const lang = await page.locator('html').getAttribute('lang');
    expect(lang).toBe('es');
    await expect(page.locator('.logo-text')).toHaveText('LuminaAdvisor');
    await expect(page.locator('#step-1')).toHaveClass(/active/);
  });

  test('camino negativo: valida paso 1 sin seleccionar habitación', async ({ page }) => {
    await page.goto('/');
    await page.locator('[data-action="next"]').click();
    await expect(page.locator('#error-type')).toBeVisible();
    await expect(page.locator('#error-type')).toContainText('Seleccioná el tipo de habitación');
  });

  test('flujo completo: calcula iluminación de cocina, muestra croquis 2D y abre Copiloto', async ({ page }) => {
    await page.goto('/');

    // Paso 1: seleccionar cocina
    await page.locator('label[for="type-cocina"]').click();
    await expect(page.locator('#step-2')).toHaveClass(/active/, { timeout: 3000 });

    // Paso 2: dimensiones, paredes y ventanas
    await page.fill('#square_meters', '15');
    await page.fill('#ceiling_height', '2.5');
    await page.locator('label[for="wall-blanco"]').click();
    await page.locator('label[for="win-grande"]').click();

    await page.locator('#btn-submit').click();

    // Resultado UNE-EN 12464-1
    await expect(page.locator('.result-hero')).toBeVisible();
    await expect(page.locator('.result-lumens')).toHaveAttribute('data-count', '11538');
    await expect(page.locator('.blueprint-card')).toBeVisible();

    // Abrir y cerrar drawer de Copiloto Lumina
    await page.locator('#btnOpenCopilotFromCallout').click();
    await expect(page.locator('#copilotDrawerOverlay')).toHaveClass(/open/);
    await page.locator('#closeCopilotBtn').click();
    await expect(page.locator('#copilotDrawerOverlay')).not.toHaveClass(/open/);
  });

  test('caminos negativos de API chat: 405, 400 y 422', async ({ request }) => {
    const resGet = await request.get('/index.php?action=chat');
    expect(resGet.status()).toBe(405);

    const resBadJson = await request.post('/index.php?action=chat', {
      headers: { 'Content-Type': 'application/json' },
      data: 'not-valid-json',
    });
    expect(resBadJson.status()).toBe(400);

    const resEmptyMsg = await request.post('/index.php?action=chat', {
      headers: { 'Content-Type': 'application/json' },
      data: JSON.stringify({ message: '   ' }),
    });
    expect(resEmptyMsg.status()).toBe(422);
  });
});
