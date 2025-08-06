// Configuration Playwright pour les tests E2E
const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests/javascript/e2e',
  
  // Timeout pour chaque test
  timeout: 30 * 1000,
  expect: {
    timeout: 5000
  },

  // Serveur de test
  webServer: {
    command: 'php -S localhost:8000 -t public/',
    port: 8000,
    reuseExistingServer: !process.env.CI,
  },

  use: {
    // URL de base
    baseURL: 'http://localhost:8000',
    
    // Capture d'écran en cas d'échec
    screenshot: 'only-on-failure',
    
    // Vidéo en cas d'échec
    video: 'retain-on-failure',
  },

  // Navigateurs à tester
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'firefox',
      use: { ...devices['Desktop Firefox'] },
    },
    {
      name: 'webkit',
      use: { ...devices['Desktop Safari'] },
    },
    // Tests mobiles
    {
      name: 'mobile-chrome',
      use: { ...devices['Pixel 5'] },
    },
  ],

  // Dossier pour les rapports
  reporter: [
    ['html', { outputFolder: 'playwright-report' }],
    ['junit', { outputFile: 'playwright-results/junit.xml' }]
  ]
});