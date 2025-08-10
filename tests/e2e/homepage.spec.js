// E2E tests for homepage
const { test, expect } = require('@playwright/test');

test.describe('Homepage E2E Tests', () => {
  
  test('should load homepage successfully', async ({ page }) => {
    await page.goto('/');
    
    // Verify page loads
    await expect(page).toHaveTitle(/Episciences Manager/);
    
    // Verify logo presence
    await expect(page.locator('img[alt*="Episciences"]')).toBeVisible();
    
    // Verify redirect to /en/
    expect(page.url()).toContain('/en/');
  });

  test('should display language selector', async ({ page }) => {
    await page.goto('/en/');
    
    // Verify language selector presence
    const languageToggle = page.locator('#language-dropdown-toggle');
    await expect(languageToggle).toBeVisible();
    await expect(languageToggle).toContainText('EN');
  });

  test('should switch languages correctly', async ({ page }) => {
    await page.goto('/en/');
    
    // Click on language selector
    await page.click('#language-dropdown-toggle');
    
    // Verify menu opens
    const dropdown = page.locator('#language-dropdown-menu');
    await expect(dropdown).toBeVisible();
    
    // Click on French
    await page.click('[data-locale="fr"]');
    
    // Verify URL change
    await page.waitForURL('**/fr/**');
    expect(page.url()).toContain('/fr/');
    
    // Verify button now displays FR
    await expect(page.locator('#language-dropdown-toggle')).toContainText('FR');
  });

  test('should display journal list', async ({ page }) => {
    await page.goto('/en/');
    
    // Verify journal list presence
    const journalList = page.locator('.list-group');
    
    // If journals are present
    if (await journalList.count() > 0) {
      await expect(journalList).toBeVisible();
      
      // Verify at least one journal exists
      const journalItems = page.locator('.list-group-item');
      expect(await journalItems.count()).toBeGreaterThan(0);
    } else {
      // If no journals, verify message
      await expect(page.locator('.alert-info')).toContainText('No journals available');
    }
  });

  test('should navigate to journal list page', async ({ page }) => {
    await page.goto('/en/');
    
    // Look for journal list link
    const journalLink = page.locator('a[href*="/journal"]').first();
    
    if (await journalLink.count() > 0) {
      await journalLink.click();
      
      // Verify URL
      expect(page.url()).toContain('/journal');
      
      // Verify title presence
      await expect(page.locator('h1')).toBeVisible();
    }
  });

  test('should be responsive on mobile', async ({ page }) => {
    // Set mobile viewport size
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto('/en/');
    
    // Verify page displays correctly
    await expect(page.locator('body')).toBeVisible();
    
    // Verify main elements are present
    await expect(page.locator('img[alt*="Episciences"]')).toBeVisible();
    
    // On mobile, dropdown might be hidden in hamburger menu
    // Verify it exists (without forcing visibility)
    await expect(page.locator('#language-dropdown-toggle')).toBeAttached();
  });

  test('should handle login/logout flow', async ({ page }) => {
    await page.goto('/en/');
    
    // Verify login button presence
    const loginBtn = page.locator('a[href*="/login"]');
    
    if (await loginBtn.count() > 0) {
      await expect(loginBtn).toBeVisible();
      await expect(loginBtn).toContainText(/Login|Connexion/i);
      
      // Click login (won't test full CAS auth)
      await loginBtn.click();
      
      // Verify redirect to CAS or login page
      await page.waitForLoadState('networkidle');
      // URL should contain either /login or CAS server
      const currentUrl = page.url();
      expect(currentUrl.includes('/login') || currentUrl.includes('cas')).toBeTruthy();
    }
  });
});