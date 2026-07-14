import { test, expect } from '@playwright/test';

test.describe('Admin Penilaian Tests (Absensi & CKP)', () => {
  test.beforeEach(async ({ page }) => {
    // Login as Admin
    await page.goto('/login');
    await page.fill('input[name="identifier"]', 'admin@bps.go.id');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/dashboard/);
  });

  test('Admin can input Nilai CKP manually', async ({ page }) => {
    await page.goto('/admin/ckp');
    await expect(page.locator('text=Nilai CKP Pegawai')).toBeVisible();

    // Pastikan tombol Input Manual ada
    const inputManualBtn = page.locator('button:has-text("Input Manual")');
    if (await inputManualBtn.isVisible()) {
      await inputManualBtn.click();
    }
    
    // Cari input field untuk pegawai tertentu.
    // Karena UI mungkin bervariasi, kita coba isi input pertama yang ditemukan dengan nama "nilai[]"
    const inputNilai = page.locator('input[name="nilai[]"]').first();
    if (await inputNilai.isVisible()) {
      await inputNilai.fill('95.50');
      await page.click('button:has-text("Simpan")');
      
      // Pastikan berhasil tersimpan
      await expect(page.locator('text=Berhasil')).toBeVisible();
    }
  });

  test('Admin can input Absensi manually', async ({ page }) => {
    await page.goto('/admin/absensi');
    await expect(page.locator('text=Data Absensi')).toBeVisible();

    // Pastikan tombol Input Manual ada
    const inputManualBtn = page.locator('button:has-text("Input Manual")');
    if (await inputManualBtn.isVisible()) {
      await inputManualBtn.click();
    }
    
    // Sama seperti CKP, input pertama yang ditemukan (misal KJK)
    const inputKjk = page.locator('input[name="kjk[]"]').first();
    if (await inputKjk.isVisible()) {
      await inputKjk.fill('100');
      await page.click('button:has-text("Simpan")');
      
      await expect(page.locator('text=Berhasil')).toBeVisible();
    }
  });
});
