// Configuration Playwright pour les tests E2E
const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests/e2e',
  
  // Timeout pour chaque test
  timeout: 30 * 1000,
  expect: {
    timeout: 5000
  },

  // Serveur de test - assumez que Docker est déjà en cours d'exécution
  // Démarrez manuellement avec : docker compose up -d
  webServer: false, // Pas de démarrage automatique

  use: {
    // URL de base - utilise Apache via Docker
    baseURL: 'http://localhost:80',
    
    // Capture d'écran en cas d'échec
    screenshot: 'only-on-failure',
    
    // Vidéo en cas d'échec
    video: 'retain-on-failure',
  },

  // Navigateurs à tester - Chromium seulement pour le CI
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],

  // Dossier pour les rapports
  reporter: [
    ['html', { outputFolder: 'playwright-report' }],
    ['junit', { outputFile: 'playwright-results/junit.xml' }]
  ]
});