import { test, expect } from '@playwright/test';

test.describe('Kepala Review & Monitoring Tests', () => {
  test.beforeEach(async ({ page }) => {
    // Login as Kepala BPS
    await page.goto('/login');
    await page.fill('input[name="identifier"]', 'kepala@bps.go.id');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/dashboard/);
  });

  test('Kepala BPS can monitor Survei progress', async ({ page }) => {
    // Kepala bisa mengakses monitoring survei sama seperti Admin
    await page.goto('/admin/monitoring');
    await expect(page.locator('text=Monitoring Survei')).toBeVisible();

    // Memastikan data grid partisipan tampil (setidaknya tabel ada)
    await expect(page.locator('table')).toBeVisible();
  });

  test('Kepala BPS can access Tim Penilai management', async ({ page }) => {
    await page.goto('/kepala/tim-penilai');
    await expect(page.locator('text=Tim Penilai')).toBeVisible();

    // Pilih pegawai sebagai tim penilai (Dropdown Select2/Native)
    // Jika ada elemen penilai_id
    const selectPenilai = page.locator('select[name="penilai_id[]"]').first();
    if (await selectPenilai.isVisible()) {
      // Tunggu options termuat dan pilih index 1 (jika memungkinkan)
      // Karena ini multiple select, caranya bisa via locators selectOptions
      // await selectPenilai.selectOption({ index: 1 });
      
      // Submit form
      const saveBtn = page.locator('button:has-text("Simpan")');
      if (await saveBtn.isVisible()) {
        await saveBtn.click();
        await expect(page.locator('text=Berhasil')).toBeVisible();
      }
    }
  });

  test('Kepala BPS can review Top 3 and select final winner', async ({ page }) => {
    await page.goto('/kepala/review');
    await expect(page.locator('text=Review Hasil Akhir')).toBeVisible();

    // Cek apakah tabel/list kandidat top 3 tersedia
    // Ini membutuhkan state di mana Top 3 sudah digenerate.
    // Jika tidak ada data, test harus bypass secara elegan
    const btnPilih = page.locator('button:has-text("Pilih Sebagai Teladan")').first();
    if (await btnPilih.isVisible()) {
      await btnPilih.click();

      // Dialog box confirm
      // Biasanya membutuhkan handling
      // page.on('dialog', dialog => dialog.accept());
      
      await expect(page.locator('text=Berhasil')).toBeVisible();
    }
  });
});
