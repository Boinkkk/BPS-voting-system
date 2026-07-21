import { test, expect } from '@playwright/test';
import { takeScreenshot } from '../utils/helpers';

test.describe('Admin - Manajemen User', () => {

    test.beforeEach(async ({ page }) => {
        // Bypass login as admin
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'admin@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL('/dashboard');
        
        // Navigate to Pegawai / Manajemen User
        await page.click('text=Daftar Pegawai');
        await expect(page).toHaveURL(/\/admin\/pegawai/);
    });

    test('Tambah User berhasil', async ({ page }) => {
        await page.click('button:has-text("Tambah Pegawai")');
        
        await page.waitForSelector('input[name="nip"]', { state: 'visible' });

        await page.fill('input[name="nip"]', '999999999999999999');
        await page.fill('input[name="nama"]', 'Pegawai Baru E2E');
        await page.fill('input[name="identifier"]', 'barue2e@bps.go.id');
        await page.selectOption('select[name="role_id"]', { label: 'Pegawai' }); // Assuming role label is Pegawai
        await page.fill('input[name="jabatan"]', 'Staf Baru');
        await page.fill('input[name="tanggal_masuk"]', '2026-08-01');
        
        await takeScreenshot(page, 'tambah-user-form');
        await page.click('form[action*="/admin/pegawai"] button[type="submit"]:has-text("Simpan")');

        await expect(page.locator('text=Pegawai berhasil ditambahkan')).toBeVisible();
        await takeScreenshot(page, 'tambah-user-berhasil');
    });

    test('Gagal tambah user karena NIP duplikat', async ({ page }) => {
        await page.click('button:has-text("Tambah Pegawai")');
        await page.waitForSelector('input[name="nip"]', { state: 'visible' });

        await page.fill('input[name="nip"]', '999999999999999999');
        await page.fill('input[name="nama"]', 'Pegawai Duplikat');
        await page.fill('input[name="identifier"]', 'duplikat@bps.go.id');
        await page.selectOption('select[name="role_id"]', { label: 'Pegawai' });
        
        await page.click('form[action*="/admin/pegawai"] button[type="submit"]:has-text("Simpan")');

        await expect(page.locator('text=sudah digunakan')).toBeVisible();
        await takeScreenshot(page, 'tambah-user-gagal-nip-duplikat');
    });

    test('Edit User berhasil', async ({ page }) => {
        // Asumsi ada tombol edit (misal icon pensil atau text "Edit") di baris Pegawai Baru E2E
        const row = page.locator('tr', { hasText: 'Pegawai Baru E2E' });
        await row.locator('button[data-bs-target*="#editPegawai"]').click();

        // Modal Edit muncul
        await page.waitForSelector('input[name="nama"]', { state: 'visible' });
        await page.fill('input[name="nama"]', 'Pegawai Baru Diedit');
        
        await takeScreenshot(page, 'edit-user-form');
        // Because there are multiple forms in modals, we find the visible one
        const modal = page.locator('.modal.show');
        await modal.locator('button[type="submit"]:has-text("Simpan")').click();

        await expect(page.locator('text=Pegawai berhasil diperbarui')).toBeVisible();
        await expect(page.locator('text=Pegawai Baru Diedit')).toBeVisible();
        await takeScreenshot(page, 'edit-user-berhasil');
    });

    test('Reset Password berhasil', async ({ page }) => {
        // Handle JS confirm if reset password triggers one
        page.on('dialog', async dialog => {
            await dialog.accept();
        });

        const row = page.locator('tr', { hasText: 'Pegawai Baru Diedit' });
        await row.locator('button:has-text("Reset Password")').click(); // Adjust selector based on actual UI

        await expect(page.locator('text=Password berhasil direset')).toBeVisible();
        await takeScreenshot(page, 'reset-password-berhasil');
    });

    test('Hapus User berhasil', async ({ page }) => {
        page.on('dialog', async dialog => {
            await dialog.accept();
        });

        const row = page.locator('tr', { hasText: 'Pegawai Baru Diedit' });
        await row.locator('button:has-text("Hapus")').click(); // Adjust selector based on actual UI (could be trash icon)

        await expect(page.locator('text=berhasil dihapus')).toBeVisible();
        await takeScreenshot(page, 'hapus-user-berhasil');
    });

    test('Tidak dapat menghapus akun sendiri', async ({ page }) => {
        const row = page.locator('tr', { hasText: 'Admin Testing' });
        
        // Assert that the delete button is disabled or does not exist for this row
        const deleteBtn = row.locator('button:has-text("Hapus")');
        // EITHER not visible OR has a disabled attribute
        // Check if there is an error message when deleting or if the button simply isn't rendered
        
        // For testing, let's assume if it exists, it should be disabled
        if (await deleteBtn.count() > 0) {
            await expect(deleteBtn).toBeDisabled();
        } else {
            // The button correctly doesn't render
            expect(await deleteBtn.count()).toBe(0);
        }
        await takeScreenshot(page, 'hapus-akun-sendiri-tidak-bisa');
    });
});
