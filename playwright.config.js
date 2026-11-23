const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests/e2e',
  timeout: 30000,
  retries: 0,
  reporter: [['list']],
  use: {
    baseURL: 'http://127.0.0.1:8099',
    headless: true,
    trace: 'on-first-retry',
  },
  webServer: {
    command: 'php -S 127.0.0.1:8099 -t public',
    url: 'http://127.0.0.1:8099',
    reuseExistingServer: !process.env.CI,
    timeout: 15000,
  },
});
