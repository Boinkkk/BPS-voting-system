import { test, expect } from '@playwright/test';

test.describe('Authentication Tests', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
  });

  test('Valid Admin Login redirects to Dashboard', async ({ page }) => {
    await page.fill('input[name="identifier"]', 'admin@bps.go.id');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    // Pastikan ter-redirect ke halaman dashboard
    await expect(page).toHaveURL(/\/dashboard/);
    await expect(page.getByRole('heading', { name: 'Dashboard Utama' })).toBeVisible();
    
    await page.click('button:has-text("Logout"), a:has-text("Logout")');
    await expect(page).toHaveURL(/\/login/);
  });

  test('Valid Pegawai Login redirects to Dashboard', async ({ page }) => {
    await page.fill('input[name="identifier"]', 'pegawai@bps.go.id');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    await expect(page).toHaveURL(/\/dashboard/);
    await expect(page.getByRole('heading', { name: 'Dashboard Utama' })).toBeVisible();
  });

  test('Valid Kepala BPS Login redirects to Dashboard', async ({ page }) => {
    await page.fill('input[name="identifier"]', 'kepala@bps.go.id');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    await expect(page).toHaveURL(/\/dashboard/);
    await expect(page.getByRole('heading', { name: 'Dashboard Utama' })).toBeVisible();
  });

  test('Invalid Login shows error message', async ({ page }) => {
    await page.fill('input[name="identifier"]', 'admin@bps.go.id');
    await page.fill('input[name="password"]', 'wrongpassword');
    await page.click('button[type="submit"]');

    await expect(page.locator('text=Kredensial yang diberikan tidak cocok dengan data kami.')).toBeVisible(); // Sesuaikan dengan pesan error dari AuthController
  });
});
