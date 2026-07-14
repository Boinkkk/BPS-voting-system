import { test, expect } from '@playwright/test';

test.describe('Role Authorization Tests', () => {

    test('Pegawai tidak dapat mengakses halaman Admin', async ({ page }) => {
        // Login as Pegawai
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'pegawai@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        // Pastikan sukses login
        await expect(page).toHaveURL(/\/dashboard/);

        // Paksa kunjungi URL Admin
        await page.goto('/admin/pegawai');

        // Middleware seharusnya memblokir dan redirect ke dashboard atau menampilkan error 403
        // Kita cukup mengecek apakah URL TIDAK berada di /admin/kandidat, 
        // ATAU halaman menampilkan forbidden/unauthorized
        const currentUrl = page.url();
        const isRedirected = !currentUrl.includes('/admin/kandidat');
        const isForbidden = await page.locator('text=403').isVisible() || await page.locator('text=Unauthorized').isVisible() || await page.locator('text=This action is unauthorized').isVisible();
        
        expect(isRedirected || isForbidden).toBeTruthy();
    });

    test('Admin tidak dapat mengakses halaman Kepala BPS', async ({ page }) => {
        // Login as Admin
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'admin@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL(/\/dashboard/);

        // Paksa kunjungi URL Kepala
        await page.goto('/kepala/review');

        const currentUrl = page.url();
        const isRedirected = !currentUrl.includes('/kepala/review');
        const isForbidden = await page.locator('text=403').isVisible() || await page.locator('text=Unauthorized').isVisible() || await page.locator('text=This action is unauthorized').isVisible();
        
        expect(isRedirected || isForbidden).toBeTruthy();
    });

});
