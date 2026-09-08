# Tests E2E avec Playwright

## Introduction

Playwright est un framework de tests end-to-end (E2E) qui permet de tester l'application dans de vrais navigateurs (Chromium, Firefox, WebKit).

## Installation

```bash
# Installer les dépendances
npm install

# Installer les navigateurs (une seule fois)
npx playwright install

# Ou installer uniquement Chromium (plus rapide, ~150 MB)
npx playwright install chromium
```

## Structure des fichiers

```
tests/
└── javascript/
    └── e2e/
        ├── homepage.spec.js              # Tests page d'accueil
        ├── ckeditor-functionality.spec.js # Tests éditeur
        └── indexing-database-admin.spec.js # Tests admin indexing
playwright.config.js                       # Configuration
```

## Configuration

Le fichier `playwright.config.js` contient :

```javascript
export default defineConfig({
  testDir: './tests/javascript/e2e',
  baseURL: 'http://localhost:8082',  // Port de l'application Docker

  projects: [
    { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
    { name: 'firefox', use: { ...devices['Desktop Firefox'] } },
    { name: 'webkit', use: { ...devices['Desktop Safari'] } },
  ],
});
```

## Exécution des tests

```bash
# Tous les tests (3 navigateurs)
npm run test:e2e

# Un seul navigateur (plus rapide)
npx playwright test --project=chromium

# Un fichier spécifique
npx playwright test tests/javascript/e2e/indexing-database-admin.spec.js

# Mode debug (avec navigateur visible)
npx playwright test --headed --debug

# Générer un rapport HTML
npx playwright test --reporter=html
npx playwright show-report
```

## Syntaxe de base

### Structure d'un test

```javascript
const { test, expect } = require('@playwright/test');

test.describe('Ma suite de tests', () => {
  test.beforeEach(async ({ page }) => {
    // Exécuté avant chaque test
    await page.goto('/en/');
  });

  test('devrait afficher le titre', async ({ page }) => {
    await expect(page).toHaveTitle(/Mon Titre/);
  });
});
```

### Navigation

```javascript
// Aller à une URL
await page.goto('/en/admin/indexing-databases');

// Attendre le chargement
await page.waitForLoadState('networkidle');

// Attendre une URL spécifique
await page.waitForURL(/admin\/indexing-databases/);
```

### Sélecteurs

```javascript
// Par ID
page.locator('#mon-id')

// Par classe
page.locator('.ma-classe')

// Par attribut
page.locator('[data-testid="mon-element"]')
page.locator('a[href*="/create"]')

// Par texte
page.locator('text=Mon texte')
page.getByText('Mon texte')

// Par rôle
page.getByRole('button', { name: 'Soumettre' })

// Combinaison
page.locator('form').locator('input[name="email"]')
```

### Actions

```javascript
// Cliquer
await page.click('button[type="submit"]');

// Remplir un champ
await page.fill('input[name="name"]', 'Ma valeur');

// Sélectionner dans un dropdown
await page.selectOption('select[name="status"]', 'validated');

// Upload de fichier
await page.setInputFiles('input[type="file"]', 'path/to/file.png');
```

### Assertions

```javascript
// Visibilité
await expect(page.locator('h1')).toBeVisible();
await expect(page.locator('.modal')).toBeHidden();

// Texte
await expect(page.locator('h1')).toHaveText('Mon titre');
await expect(page.locator('p')).toContainText('partiel');

// Attributs
await expect(page.locator('input')).toHaveAttribute('type', 'email');
await expect(page.locator('a')).toHaveClass(/btn-primary/);

// URL
expect(page.url()).toContain('/admin/');

// Comptage
await expect(page.locator('tr')).toHaveCount(5);
expect(await page.locator('li').count()).toBeGreaterThan(0);
```

---

## Pages protégées par CAS

Les pages admin (`/admin/indexing-databases`) nécessitent une authentification CAS. Les tests vérifient que la redirection vers CAS fonctionne correctement :

```javascript
test('should redirect to CAS login', async ({ page }) => {
  await page.goto('/en/admin/indexing-databases');
  await page.waitForLoadState('networkidle');

  // Vérifie la redirection vers CAS
  expect(page.url()).toContain('cas');
  expect(page.url()).toContain('login');
});
```

> **Note** : Les tests authentifiés nécessitent une configuration CAS spéciale et ne sont pas inclus par défaut.

---

## Bonnes pratiques

### 1. Utiliser des data-testid

```html
<button data-testid="submit-button">Soumettre</button>
```

```javascript
await page.click('[data-testid="submit-button"]');
```

### 2. Attendre les éléments

```javascript
// Mauvais - peut échouer si l'élément n'est pas encore là
await page.click('button');

// Bon - attend que l'élément soit visible
await expect(page.locator('button')).toBeVisible();
await page.click('button');
```

### 3. Isoler les tests

Chaque test doit être indépendant. Utiliser `beforeEach` pour l'état initial.

### 4. Gérer les timeouts

```javascript
// Timeout global dans la config
timeout: 30000,

// Timeout par assertion
await expect(page.locator('h1')).toBeVisible({ timeout: 10000 });
```

### 5. Screenshots sur échec

```javascript
// Dans playwright.config.js
use: {
  screenshot: 'only-on-failure',
  trace: 'on-first-retry',
}
```

---

## Commandes utiles

```bash
# Lancer en mode UI (interface graphique)
npx playwright test --ui

# Générer du code en enregistrant les actions
npx playwright codegen http://localhost:8082

# Voir le rapport
npx playwright show-report

# Lancer un test spécifique
npx playwright test -g "should display admin page"

# Debug avec pause
PWDEBUG=1 npx playwright test
```

## Ressources

- [Documentation officielle Playwright](https://playwright.dev/docs/intro)
- [API Reference](https://playwright.dev/docs/api/class-page)
- [Best Practices](https://playwright.dev/docs/best-practices)