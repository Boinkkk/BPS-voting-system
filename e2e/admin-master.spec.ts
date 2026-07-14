import { test, expect } from '@playwright/test';

test.describe('Admin Master Data Tests', () => {
  test.beforeEach(async ({ page }) => {
    // Login as Admin before each test
    await page.goto('/login');
    await page.fill('input[name="identifier"]', 'admin@bps.go.id');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/dashboard/);
  });

  test('Admin can manage Periode Penilaian', async ({ page }) => {
    await page.goto('/admin/periode');
    await expect(page.locator('text=Periode Penilaian')).toBeVisible();

    // Tambah Periode
    await page.click('button:has-text("Tambah Periode")');
    await page.fill('input[name="nama"]', 'Triwulan 3 2026');
    await page.selectOption('select[name="triwulan"]', '3');
    await page.fill('input[name="tahun"]', '2026');
    await page.fill('input[name="tanggal_mulai"]', '2026-07-01');
    await page.fill('input[name="tanggal_selesai"]', '2026-09-30');
    await page.click('button:has-text("Simpan")');
    
    // Pastikan tersimpan dan muncul di tabel
    await expect(page.locator('text=Triwulan 3 2026')).toBeVisible();

    // Edit status periode (misal menjadi 'voting')
    // Asumsi: Ada tombol edit atau form langsung di row tersebut
    // Tergantung UI, kita klik tombol Edit pertama yang muncul
    await page.locator('button.btn-edit').first().click();
    await page.selectOption('select[name="status"]', 'voting');
    await page.click('button:has-text("Update")');
    
    // Pastikan status berubah
    // Note: Ini sangat bergantung pada implementasi class/teks di tabel
  });

  test('Admin can manage Pegawai', async ({ page }) => {
    await page.goto('/admin/pegawai');
    await expect(page.locator('text=Data Pegawai')).toBeVisible();

    // Tambah Pegawai
    await page.click('button:has-text("Tambah Pegawai")');
    await page.fill('input[name="nip"]', '1234567890123456');
    await page.fill('input[name="nama"]', 'Pegawai Baru E2E');
    await page.fill('input[name="email"]', 'pegawaie2e@bps.go.id');
    await page.fill('input[name="password"]', 'password123');
    // Pilih role_id dan departemen_id berdasarkan value/teks jika menggunakan select
    // await page.selectOption('select[name="role_id"]', { label: 'Pegawai' });
    await page.fill('input[name="jabatan"]', 'Staff E2E');
    await page.fill('input[name="tanggal_masuk"]', '2026-01-01');
    await page.click('button:has-text("Simpan")');
    
    // Pastikan tersimpan
    await expect(page.locator('text=Pegawai Baru E2E')).toBeVisible();
  });

  test('Admin can update Pengaturan Bobot', async ({ page }) => {
    await page.goto('/admin/pengaturan-bobot');
    await expect(page.locator('text=Pengaturan Bobot')).toBeVisible();

    // Ubah nilai CKP, Absensi, Survei
    await page.fill('input[name="ckp"]', '40');
    await page.fill('input[name="absensi"]', '30');
    await page.fill('input[name="survey"]', '30');
    await page.click('button:has-text("Simpan")');
    
    // Pastikan berhasil tersimpan
    await expect(page.locator('text=Berhasil')).toBeVisible();
    await expect(page.locator('input[name="ckp"]')).toHaveValue('40');
  });
});
