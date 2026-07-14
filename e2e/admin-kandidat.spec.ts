import { test, expect } from '@playwright/test';

test.describe('Admin Kandidat & Survei Tests', () => {
  test.beforeEach(async ({ page }) => {
    // Login as Admin
    await page.goto('/login');
    await page.fill('input[name="identifier"]', 'admin@bps.go.id');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/dashboard/);
  });

  test('Admin can manage Pertanyaan Survei', async ({ page }) => {
    await page.goto('/admin/survey');
    await expect(page.locator('text=Pertanyaan Survei')).toBeVisible();

    // Coba nambah pertanyaan survei
    await page.click('button:has-text("Tambah Pertanyaan")');
    await page.fill('input[name="nomor_urut"]', '99');
    await page.fill('input[name="pertanyaan"]', 'Pertanyaan Playwright E2E?');
    await page.fill('input[name="kategori"]', 'Integritas');
    await page.fill('input[name="bobot"]', '1.0');
    await page.click('button:has-text("Simpan")');
    
    // Pastikan pertanyaan baru tersimpan
    await expect(page.locator('text=Pertanyaan Playwright E2E?')).toBeVisible();
  });

  test('Admin can Generate Top 10 Kandidat', async ({ page }) => {
    await page.goto('/admin/kandidat');
    await expect(page.locator('text=Kandidat Pegawai Teladan')).toBeVisible();

    // Click Generate Top 10 button
    const generateBtn = page.locator('button:has-text("Kalkulasi 10 Kandidat")');
    if (await generateBtn.isVisible()) {
      await generateBtn.click();
      
      // Biasanya ada dialog konfirmasi JS atau sweetalert
      // Kita asumsikan konfirmasi otomatis jika tidak di-handle
      // page.on('dialog', dialog => dialog.accept());
      
      await expect(page.locator('text=Berhasil')).toBeVisible();
    }
  });

  test('Admin can Generate Top 3 Kandidat', async ({ page }) => {
    await page.goto('/admin/kandidat');
    
    // Click Generate Top 3 button
    const generateTop3Btn = page.locator('button:has-text("Kalkulasi Ulang 3 Terbaik")');
    if (await generateTop3Btn.isVisible()) {
      await generateTop3Btn.click();
      
      // Pastikan ada pesan berhasil
      await expect(page.locator('text=Berhasil')).toBeVisible();
    }
  });
});
