// E2E tests for Indexing Database Admin page
const { test, expect } = require('@playwright/test');

/**
 * Tests for Indexing Database Admin functionality.
 *
 * Note: These pages require CAS authentication. Tests verify:
 * - Correct redirect to CAS login
 * - URL structure is correct
 *
 * For full authenticated tests, you would need to:
 * 1. Set up a test user in CAS
 * 2. Create auth state with storageState
 */

// Base URL for admin indexing database pages (with locale prefix)
const ADMIN_BASE_URL = '/en/admin/indexing-databases';

test.describe('Indexing Database Admin - Security', () => {
  test('should redirect to CAS login for admin list page', async ({ page }) => {
    // Should redirect to CAS
    await page.waitForLoadState('networkidle');
    const currentUrl = page.url();

    // Verify redirect to CAS authentication
    expect(currentUrl).toContain('cas');
    expect(currentUrl).toContain('login');
  });

  test('should redirect to CAS login for create page', async ({ page }) => {
    await page.waitForLoadState('networkidle');
    const currentUrl = page.url();

    expect(currentUrl).toContain('cas');
    expect(currentUrl).toContain('login');
  });

  test('should include return URL in CAS redirect', async ({ page }) => {
    await page.goto(ADMIN_BASE_URL);

    await page.waitForLoadState('networkidle');
    const currentUrl = page.url();

    // The service parameter should contain the original URL
    expect(currentUrl).toContain('service=');
    expect(currentUrl).toContain('indexing-databases');
  });
});

test.describe('Indexing Database Admin - CAS Login Page', () => {
  test('should display CAS login form', async ({ page }) => {
    await page.goto(ADMIN_BASE_URL);
    await page.waitForLoadState('networkidle');

    // CAS login page should have username and password fields
    const usernameField = page.locator(
      'input[name="username"], input[id="username"]'
    );
    const passwordField = page.locator(
      'input[name="password"], input[id="password"], input[type="password"]'
    );

    // At least one login form element should be visible
    const hasLoginForm =
      (await usernameField.isVisible().catch(() => false)) ||
      (await passwordField.isVisible().catch(() => false));

    expect(hasLoginForm).toBeTruthy();
  });

  test('should have submit button on CAS login', async ({ page }) => {
    await page.goto(ADMIN_BASE_URL);
    await page.waitForLoadState('networkidle');

    // Look for submit button
    const submitButton = page.locator(
      'button[type="submit"], input[type="submit"]'
    );
    await expect(submitButton.first()).toBeVisible();
  });
});

/**
 * Authenticated tests template.
 *
 * To enable these tests:
 * 1. Create a global setup that logs in via CAS
 * 2. Save the auth state to .auth/epiadmin.json
 * 3. Uncomment test.use({ storageState: '.auth/epiadmin.json' })
 *
 * Example global-setup.ts:
 * ```
 * import { chromium } from '@playwright/test';
 *
 * async function globalSetup() {
 *   const browser = await chromium.launch();
 *   const page = await browser.newPage();
 *
 *   await page.goto('http://localhost:8082/en/admin/indexing-databases');
 *   // Fill CAS login form
 *   await page.fill('#username', 'your-test-user');
 *   await page.fill('#password', 'your-test-password');
 *   await page.click('button[type="submit"]');
 *
 *   // Wait for redirect back to app
 *   await page.waitForURL(/admin\/indexing-databases/);
 *
 *   // Save auth state
 *   await page.context().storageState({ path: '.auth/epiadmin.json' });
 *   await browser.close();
 * }
 *
 * export default globalSetup;
 * ```
 */

// Uncomment when auth is configured:
// test.describe('Indexing Database Admin - Authenticated', () => {
//   test.use({ storageState: '.auth/epiadmin.json' });
//
//   test('should display admin page with title', async ({ page }) => {
//     await page.goto('/en/admin/indexing-databases');
//     await expect(page.locator('h1')).toContainText(/Indexing|Bases/i);
//   });
//
//   test('should display create button', async ({ page }) => {
//     await page.goto('/en/admin/indexing-databases');
//     await expect(page.locator('a[href*="/create"]')).toBeVisible();
//   });
//
//   test('should display filter buttons', async ({ page }) => {
//     await page.goto('/en/admin/indexing-databases');
//     await expect(page.locator('a[href*="status=pending"]')).toBeVisible();
//     await expect(page.locator('a[href*="status=validated"]')).toBeVisible();
//     await expect(page.locator('a[href*="status=rejected"]')).toBeVisible();
//   });
//
//   test('should display data table', async ({ page }) => {
//     await page.goto('/en/admin/indexing-databases');
//     await expect(page.locator('table')).toBeVisible();
//     await expect(page.locator('thead th')).toHaveCount(6);
//   });
//
//   test('should navigate to create form', async ({ page }) => {
//     await page.goto('/en/admin/indexing-databases');
//     await page.click('a[href*="/create"]');
//     await expect(page.locator('form')).toBeVisible();
//     await expect(page.locator('input[name="name"]')).toBeVisible();
//   });
//
//   test('should create new indexing database', async ({ page }) => {
//     await page.goto('/en/admin/indexing-databases/create');
//     const testName = `Test DB ${Date.now()}`;
//     await page.fill('input[name="name"]', testName);
//     await page.fill('input[name="url"]', 'https://test.example.com');
//     await page.click('button[type="submit"]');
//     await expect(page.locator('.alert-success')).toBeVisible();
//   });
// });
