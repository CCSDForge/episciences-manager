// Tests E2E pour la page d'accueil
const { test, expect } = require('@playwright/test');

test.describe('Homepage E2E Tests', () => {
  
  test('should load homepage successfully', async ({ page }) => {
    await page.goto('/');
    
    // Vérifier que la page se charge
    await expect(page).toHaveTitle(/Episciences Manager/);
    
    // Vérifier la présence du logo
    await expect(page.locator('img[alt*="Episciences"]')).toBeVisible();
    
    // Vérifier la redirection vers /en/
    expect(page.url()).toContain('/en/');
  });

  test('should display language selector', async ({ page }) => {
    await page.goto('/en/');
    
    // Vérifier la présence du sélecteur de langue
    const languageToggle = page.locator('#language-dropdown-toggle');
    await expect(languageToggle).toBeVisible();
    await expect(languageToggle).toContainText('EN');
  });

  test('should switch languages correctly', async ({ page }) => {
    await page.goto('/en/');
    
    // Cliquer sur le sélecteur de langue
    await page.click('#language-dropdown-toggle');
    
    // Vérifier que le menu s'ouvre
    const dropdown = page.locator('#language-dropdown-menu');
    await expect(dropdown).toBeVisible();
    
    // Cliquer sur français
    await page.click('[data-locale="fr"]');
    
    // Vérifier le changement d'URL
    await page.waitForURL('**/fr/**');
    expect(page.url()).toContain('/fr/');
    
    // Vérifier que le bouton affiche maintenant FR
    await expect(page.locator('#language-dropdown-toggle')).toContainText('FR');
  });

  test('should display journal list', async ({ page }) => {
    await page.goto('/en/');
    
    // Vérifier la présence de la liste des journaux
    const journalList = page.locator('.list-group');
    
    // Si des journaux sont présents
    if (await journalList.count() > 0) {
      await expect(journalList).toBeVisible();
      
      // Vérifier qu'il y a au moins un journal
      const journalItems = page.locator('.list-group-item');
      expect(await journalItems.count()).toBeGreaterThan(0);
    } else {
      // Si pas de journaux, vérifier le message
      await expect(page.locator('.alert-info')).toContainText('No journals available');
    }
  });

  test('should navigate to journal list page', async ({ page }) => {
    await page.goto('/en/');
    
    // Chercher un lien vers la liste des journaux
    const journalLink = page.locator('a[href*="/journal"]').first();
    
    if (await journalLink.count() > 0) {
      await journalLink.click();
      
      // Vérifier l'URL
      expect(page.url()).toContain('/journal');
      
      // Vérifier la présence du titre
      await expect(page.locator('h1')).toBeVisible();
    }
  });

  test('should be responsive on mobile', async ({ page }) => {
    // Définir une taille mobile
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto('/en/');
    
    // Vérifier que la page s'affiche correctement
    await expect(page.locator('body')).toBeVisible();
    
    // Vérifier que les éléments principaux sont visibles
    await expect(page.locator('img[alt*="Episciences"]')).toBeVisible();
    await expect(page.locator('#language-dropdown-toggle')).toBeVisible();
  });

  test('should handle login/logout flow', async ({ page }) => {
    await page.goto('/en/');
    
    // Vérifier la présence du bouton de connexion
    const loginBtn = page.locator('a[href*="/login"]');
    
    if (await loginBtn.count() > 0) {
      await expect(loginBtn).toBeVisible();
      await expect(loginBtn).toContainText(/Login|Connexion/i);
      
      // Cliquer sur login (ne testera pas l'auth CAS complète)
      await loginBtn.click();
      
      // Vérifier la redirection vers CAS ou page de login
      await page.waitForLoadState('networkidle');
      // L'URL devrait contenir soit /login soit le serveur CAS
      const currentUrl = page.url();
      expect(currentUrl.includes('/login') || currentUrl.includes('cas')).toBeTruthy();
    }
  });
});