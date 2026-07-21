import { test, expect } from '@playwright/test';
import { takeScreenshot } from '../utils/helpers';

test.describe('Admin - Manajemen Periode', () => {

    test.beforeEach(async ({ page }) => {
        // Bypass login as admin
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'admin@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL('/dashboard');
        
        // Navigate to Periode Penilaian
        await page.click('text=Periode Penilaian');
        await expect(page).toHaveURL(/\/admin\/periode/);
    });

    test('Tambah periode berhasil', async ({ page }) => {
        await page.click('button:has-text("Tambah Periode")'); // assuming there's a button opening modal
        
        // Wait for modal to be visible (or just wait for input)
        await page.waitForSelector('select[name="triwulan"]', { state: 'visible' });

        await page.selectOption('select[name="triwulan"]', '1');
        await page.fill('input[name="tahun"]', '2027');
        await page.fill('input[name="tanggal_mulai"]', '2027-01-01');
        await page.fill('input[name="tanggal_selesai_persiapan"]', '2027-01-05');
        await page.fill('input[name="tanggal_mulai_voting"]', '2027-01-06');
        await page.fill('input[name="tanggal_selesai_voting"]', '2027-01-15');
        await page.fill('input[name="tanggal_review_kepala"]', '2027-01-16');
        await page.fill('input[name="tanggal_selesai"]', '2027-01-20');
        
        await takeScreenshot(page, 'tambah-periode-form');

        // Submit form inside the modal
        await page.click('form[action*="/admin/periode"] button[type="submit"]:has-text("Simpan")');

        // Check for success message or redirection
        await expect(page.locator('text=Periode berhasil ditambahkan')).toBeVisible();
        await takeScreenshot(page, 'tambah-periode-berhasil');
    });

    test('Tambah periode gagal jika tahun duplikat', async ({ page }) => {
        // Try adding the same period again
        await page.click('button:has-text("Tambah Periode")');
        await page.waitForSelector('select[name="triwulan"]', { state: 'visible' });

        await page.selectOption('select[name="triwulan"]', '1');
        await page.fill('input[name="tahun"]', '2027');
        await page.fill('input[name="tanggal_mulai"]', '2027-01-01');
        await page.fill('input[name="tanggal_selesai_persiapan"]', '2027-01-05');
        await page.fill('input[name="tanggal_mulai_voting"]', '2027-01-06');
        await page.fill('input[name="tanggal_selesai_voting"]', '2027-01-15');
        await page.fill('input[name="tanggal_review_kepala"]', '2027-01-16');
        await page.fill('input[name="tanggal_selesai"]', '2027-01-20');

        await page.click('form[action*="/admin/periode"] button[type="submit"]:has-text("Simpan")');

        // Should see error
        await expect(page.locator('text=Periode untuk triwulan dan tahun ini sudah ada')).toBeVisible();
        await takeScreenshot(page, 'tambah-periode-duplikat-gagal');
    });

    test('Ubah status periode ke penginputan', async ({ page }) => {
        // Find the specific period in the table and change its status
        // Here we assume there's a select dropdown for status in the table row
        const selectStatus = page.locator('tr', { hasText: 'Triwulan 1 Tahun 2027' }).locator('select[name="status"]');
        await selectStatus.selectOption('penginputan');

        // Assume it auto-submits or there's an 'Ubah' button nearby
        // Example: await page.locator('tr', { hasText: 'Triwulan 1 Tahun 2027' }).locator('button:has-text("Ubah")').click();

        await expect(page.locator('text=Status berhasil diubah')).toBeVisible();
        await takeScreenshot(page, 'ubah-status-penginputan');
    });

    test('Hapus periode berhasil', async ({ page }) => {
        // Handle JS confirm dialog
        page.on('dialog', async dialog => {
            expect(dialog.message()).toContain('hapus');
            await dialog.accept();
        });

        // Click delete button for the created period
        // For deleting, we usually have a form with action pointing to the delete endpoint
        const deleteBtn = page.locator('tr', { hasText: 'Triwulan 1 Tahun 2027' }).locator('button:has-text("Hapus")');
        await deleteBtn.click();

        await expect(page.locator('text=berhasil dihapus')).toBeVisible();
        await takeScreenshot(page, 'hapus-periode-berhasil');
    });
});
