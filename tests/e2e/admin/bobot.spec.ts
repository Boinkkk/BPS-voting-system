import { test, expect } from '@playwright/test';
import { takeScreenshot } from '../utils/helpers';

test.describe('Admin - Pengaturan Bobot', () => {

    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'admin@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL('/dashboard');
        
        await page.click('text=Pengaturan Bobot');
        await expect(page).toHaveURL(/\/admin\/pengaturan/);
    });

    test('Simpan bobot berhasil (Total = 100)', async ({ page }) => {
        // Asumsi form fields
        await page.fill('input[name="ckp"]', '40');
        await page.fill('input[name="absensi"]', '30');
        await page.fill('input[name="survey"]', '30');
        
        await takeScreenshot(page, 'pengaturan-bobot-form');
        await page.click('button[type="submit"]:has-text("Simpan Pengaturan")');

        await expect(page.locator('text=Pengaturan bobot berhasil diperbarui')).toBeVisible();
        await takeScreenshot(page, 'pengaturan-bobot-berhasil');
    });

    test('Gagal simpan jika total bobot > 100', async ({ page }) => {
        await page.fill('input[name="ckp"]', '50');
        await page.fill('input[name="absensi"]', '50');
        await page.fill('input[name="survey"]', '50');
        
        await page.click('button[type="submit"]:has-text("Simpan Pengaturan")');

        await expect(page.locator('text=Total keseluruhan harus bernilai 100')).toBeVisible();
        await takeScreenshot(page, 'pengaturan-bobot-gagal-lebih');
    });

    test('Gagal simpan jika total bobot < 100', async ({ page }) => {
        await page.fill('input[name="ckp"]', '10');
        await page.fill('input[name="absensi"]', '10');
        await page.fill('input[name="survey"]', '10');
        
        await page.click('button[type="submit"]:has-text("Simpan Pengaturan")');

        await expect(page.locator('text=Total keseluruhan harus bernilai 100')).toBeVisible();
        await takeScreenshot(page, 'pengaturan-bobot-gagal-kurang');
    });
});
