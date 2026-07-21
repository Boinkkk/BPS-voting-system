import { test, expect } from '@playwright/test';
import { takeScreenshot } from '../utils/helpers';

test.describe('Authentication & Authorization', () => {
    
    test.beforeEach(async ({ page }) => {
        // Navigasi ke halaman login sebelum tiap tes
        await page.goto('/login');
    });

    test('Login Admin berhasil', async ({ page }) => {
        await page.fill('input[name="identifier"]', 'admin@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL('/dashboard');
        await expect(page.locator('text=Admin Testing')).toBeVisible();
        await takeScreenshot(page, 'login-admin-berhasil');
    });

    test('Login Kepala Kantor berhasil', async ({ page }) => {
        await page.fill('input[name="identifier"]', 'kepala@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL('/dashboard');
        await expect(page.locator('text=Kepala Kantor Testing')).toBeVisible();
        await takeScreenshot(page, 'login-kepala-berhasil');
    });

    test('Login Pegawai berhasil', async ({ page }) => {
        await page.fill('input[name="identifier"]', 'pegawai1@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL('/dashboard');
        await expect(page.locator('text=Pegawai Satu')).toBeVisible();
        await takeScreenshot(page, 'login-pegawai-berhasil');
    });

    test('Login dengan password salah', async ({ page }) => {
        await page.fill('input[name="identifier"]', 'admin@bps.go.id');
        await page.fill('input[name="password"]', 'wrongpassword');
        await page.click('button[type="submit"]');

        await expect(page.locator('text=Email atau password salah')).toBeVisible();
        await takeScreenshot(page, 'login-gagal-password-salah');
    });

    test('Login dengan email tidak terdaftar', async ({ page }) => {
        await page.fill('input[name="identifier"]', 'unknown@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        await expect(page.locator('text=Email atau password salah')).toBeVisible();
    });

    test('Login dengan seluruh field kosong', async ({ page }) => {
        await page.click('button[type="submit"]');
        
        // HTML5 validation or backend validation
        const emailInput = page.locator('input[name="identifier"]');
        // Check if required message is visible, or if backend returns validation error
        // Many modern apps use HTML5 'required' attribute which intercepts the submit.
        const isRequired = await emailInput.evaluate((el: HTMLInputElement) => el.required);
        expect(isRequired).toBeTruthy();
    });

    test('Logout berhasil', async ({ page }) => {
        // Login first
        await page.fill('input[name="identifier"]', 'admin@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL('/dashboard');

        // Click Profile -> Logout
        await page.click('button:has-text("Admin Testing")'); // or whatever opens the dropdown
        await page.click('button:has-text("Log Out")'); // Assuming the logout button has text "Log Out"

        await expect(page).toHaveURL('/login');
        await takeScreenshot(page, 'logout-berhasil');
    });

    // Authorization checks
    test.describe('Authorization Checks', () => {
        
        test('Pegawai tidak dapat membuka halaman Admin', async ({ page }) => {
            await page.fill('input[name="identifier"]', 'pegawai1@bps.go.id');
            await page.fill('input[name="password"]', 'password123');
            await page.click('button[type="submit"]');
            await expect(page).toHaveURL('/dashboard');

            // Coba paksa buka URL manajemen user
            await page.goto('/admin/pegawai');
            
            // Should be redirected or 403 Forbidden
            await expect(page).not.toHaveURL('/admin/pegawai');
            // Assuming it redirects to dashboard with error
            await expect(page).toHaveURL('/dashboard'); 
            await takeScreenshot(page, 'pegawai-akses-admin-ditolak');
        });

        test('Admin tidak dapat mengakses halaman khusus Kepala Kantor', async ({ page }) => {
            await page.fill('input[name="identifier"]', 'admin@bps.go.id');
            await page.fill('input[name="password"]', 'password123');
            await page.click('button[type="submit"]');
            await expect(page).toHaveURL('/dashboard');

            // Coba paksa buka URL review kepala
            await page.goto('/kepala/review');
            
            // Should be redirected or 403 Forbidden
            await expect(page).not.toHaveURL('/kepala/review');
            await takeScreenshot(page, 'admin-akses-kepala-ditolak');
        });
    });
});
