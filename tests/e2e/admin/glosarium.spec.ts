import { test, expect } from '@playwright/test';
import { takeScreenshot } from '../utils/helpers';

test.describe('Admin - Manajemen Glosarium', () => {

    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'admin@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL('/dashboard');
        
        await page.click('text=Glosarium');
        await expect(page).toHaveURL(/\/admin\/glosarium/);
    });

    test('Tambah Istilah Baru', async ({ page }) => {
        await page.click('button:has-text("Tambah Istilah")');
        await page.waitForSelector('input[name="istilah"]', { state: 'visible' });

        await page.fill('input[name="istilah"]', 'E2E Istilah');
        await page.fill('textarea[name="definisi"]', 'Ini adalah definisi untuk E2E testing.');
        
        await takeScreenshot(page, 'tambah-glosarium-form');
        await page.click('form[action*="/admin/glosarium"] button[type="submit"]:has-text("Simpan")');

        await expect(page.locator('text=berhasil ditambahkan')).toBeVisible();
        await takeScreenshot(page, 'tambah-glosarium-berhasil');
    });

    test('Edit Istilah', async ({ page }) => {
        const row = page.locator('tr', { hasText: 'E2E Istilah' });
        await row.locator('button[data-bs-target*="#editIstilah"]').click();

        await page.waitForSelector('input[name="istilah"]', { state: 'visible' });
        await page.fill('input[name="istilah"]', 'E2E Istilah Diedit');
        
        const modal = page.locator('.modal.show');
        await modal.locator('button[type="submit"]:has-text("Simpan")').click();

        await expect(page.locator('text=berhasil diperbarui')).toBeVisible();
        await expect(page.locator('text=E2E Istilah Diedit')).toBeVisible();
        await takeScreenshot(page, 'edit-glosarium-berhasil');
    });

    test('Hapus Istilah', async ({ page }) => {
        page.on('dialog', async dialog => {
            await dialog.accept();
        });

        const row = page.locator('tr', { hasText: 'E2E Istilah Diedit' });
        await row.locator('button:has-text("Hapus")').click();

        await expect(page.locator('text=berhasil dihapus')).toBeVisible();
        await takeScreenshot(page, 'hapus-glosarium-berhasil');
    });

});
