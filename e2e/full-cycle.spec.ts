import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';
import path from 'path';

test.describe.configure({ mode: 'serial' });

test.describe('Full Cycle E2E Test (Admin -> Kepala Umum -> Pegawai -> Kepala Kantor)', () => {
    // Nama periode sekarang di-generate otomatis oleh sistem (misal: "Triwulan 1 2026")
    const triwulanName = "Triwulan 1";

    test('Fase 1: Setup Periode, CKP, Absensi dan Ubah Status ke Voting', async ({ page }) => {
        test.setTimeout(120000); // Set timeout ke 2 menit karena banyak aksi
        // 1. Admin Login -> Tambahkan Periode status masa penginputan pilih triwulan 1
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'admin@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL(/\/dashboard/);

        // Buat Periode Baru
        await page.goto('/admin/periode');
        await page.click('button:has-text("Tambah Periode Baru")');
        await page.selectOption('select[name="triwulan"]', '1');
        await page.selectOption('select[name="status"]', 'penginputan'); // Status awal
        await page.fill('input[name="tahun"]', '2026');
        await page.fill('input[name="tanggal_mulai"]', '2026-01-01');
        await page.fill('input[name="tanggal_selesai"]', '2026-03-31');
        await page.click('button:has-text("Simpan")');
        
        await expect(page.locator(`text=Periode Penilaian baru berhasil ditambahkan.`)).toBeVisible();
        await page.context().clearCookies(); // Logout

        // 2. Kepala Umum -> Login -> Input data CKP
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'kepalaumum@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        await page.goto('/admin/ckp');
        // Upload CKP
        // Pilih opsi periode terakhir di form upload_periode
        const selectPeriodeCkp = page.locator('select#upload_periode');
        if (await selectPeriodeCkp.isVisible()) {
            const options = await selectPeriodeCkp.locator('option').allInnerTexts();
            await selectPeriodeCkp.selectOption({ label: options[options.length - 1].trim() });
        }
        await page.setInputFiles('input[type="file"][name="file"]', 'C:\\BPS\\voting\\web\\data\\data_nilai_ckp.csv');
        await page.click('button[type="submit"]:has-text("Upload")');
        await expect(page.locator('.bg-green-50, .bg-red-50').first()).toBeVisible({ timeout: 10000 });

        // 3-5. Kepala Umum -> Input data absensi Januari, Februari, Maret
        await page.goto('/admin/absensi');
        const selectPeriodeAbsen = page.locator('select#upload_periode');
        
        // Januari
        if (await selectPeriodeAbsen.isVisible()) {
            const options = await selectPeriodeAbsen.locator('option').allInnerTexts();
            await selectPeriodeAbsen.selectOption({ label: options[options.length - 1].trim() });
        }
        await page.selectOption('select#upload_bulan', '1');
        await page.setInputFiles('input[type="file"][name="file"]', 'C:\\BPS\\voting\\web\\data\\Rekap Presensi Kab. Bangkalan\\01_Rekap Presensi Januari 2026.xlsx');
        await page.click('button[type="submit"]:has-text("Upload & Proses")');
        await expect(page.locator('.bg-green-50, .bg-red-50').first()).toBeVisible({ timeout: 10000 });

        // Februari
        if (await selectPeriodeAbsen.isVisible()) {
            const options = await selectPeriodeAbsen.locator('option').allInnerTexts();
            await selectPeriodeAbsen.selectOption({ label: options[options.length - 1].trim() });
        }
        await page.selectOption('select#upload_bulan', '2');
        await page.setInputFiles('input[type="file"][name="file"]', 'C:\\BPS\\voting\\web\\data\\Rekap Presensi Kab. Bangkalan\\02_Rekap Presensi Februari 2026.xlsx');
        await page.click('button[type="submit"]:has-text("Upload & Proses")');
        await expect(page.locator('.bg-green-50, .bg-red-50').first()).toBeVisible({ timeout: 10000 });

        // Maret
        if (await selectPeriodeAbsen.isVisible()) {
            const options = await selectPeriodeAbsen.locator('option').allInnerTexts();
            await selectPeriodeAbsen.selectOption({ label: options[options.length - 1].trim() });
        }
        await page.selectOption('select#upload_bulan', '3');
        await page.setInputFiles('input[type="file"][name="file"]', 'C:\\BPS\\voting\\web\\data\\Rekap Presensi Kab. Bangkalan\\03_Rekap Presensi Maret 2026.xlsx');
        await page.click('button[type="submit"]:has-text("Upload & Proses")');
        await expect(page.locator('.bg-green-50, .bg-red-50').first()).toBeVisible({ timeout: 10000 });

        await page.context().clearCookies(); // Logout

        // 6. Admin -> Ubah Periode ke masa voting
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'admin@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        await page.goto('/admin/periode');
        const row = page.locator(`tr:has-text("${triwulanName}")`).first();
        const btnEdit = row.locator('button:has-text("Edit")').first();
        if (await btnEdit.isVisible()) {
            await btnEdit.click();
            await page.selectOption('#edit_status', 'voting');
            await page.click('button:has-text("Simpan Perubahan")');
            await expect(page.locator('text=Periode Penilaian berhasil diperbarui.')).toBeVisible({ timeout: 10000 });
        }
        await page.context().clearCookies(); // Logout
    });

    test('Fase 2: Jalankan fill-surveys.spec.ts agar semua pegawai mengisi', async () => {
        // Karena pengisian seluruh pegawai dilakukan di fill-surveys.spec.ts 
        // yang secara dinamis me-loop semua akun pegawai, 
        // kita akan mengeksekusi spec tersebut secara terprogrammatic (child process).
        test.setTimeout(1000 * 60 * 20); // 20 Menit Timeout karena proses ini sangat lama
        
        console.log('Menjalankan Fase 2: Mengisi survei untuk semua pegawai (fill-surveys.spec.ts)...');
        try {
            // Jalankan npm command untuk menjalankan spesifik file e2e tanpa reset database
            execSync('npx playwright test e2e/fill-surveys.spec.ts --project=chromium --headed', { 
                stdio: 'inherit',
                env: { ...process.env, SKIP_DB_RESET: '1' }
            });
            console.log('Fase 2 selesai dengan sukses.');
        } catch (error) {
            console.error('Fase 2 (fill-surveys) gagal:', error);
            throw new Error('Eksekusi fill-surveys.spec.ts gagal.');
        }
    });

    test('Fase 3: Admin Tutup Voting & Kepala Kantor Pilih Pemenang', async ({ page }) => {
        test.setTimeout(60000); // Set timeout ke 1 menit
        // 1. Admin ubah status ke review_kepala dan kalkulasi top 3
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'admin@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        await page.goto('/admin/periode');
        const row = page.locator(`tr:has-text("${triwulanName}")`).first();
        const btnEdit = row.locator('button:has-text("Edit")').first();
        if (await btnEdit.isVisible()) {
            await btnEdit.click();
            await page.selectOption('#edit_status', 'review_kepala');
            await page.click('button:has-text("Simpan Perubahan")');
        }

        await page.goto('/admin/kandidat');
        const btn3 = page.locator('button:has-text("Kalkulasi Ulang 3 Terbaik")');
        if (await btn3.isVisible()) {
            page.once('dialog', dialog => dialog.accept());
            await btn3.click();
            await page.waitForTimeout(500);
        }
        await page.context().clearCookies(); // Logout

        // 2. Login Kepala Kantor
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'kepala@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        await page.goto('/kepala/review');
        
        // Pilih pemenang
        const btnPilih = page.locator('button:has-text("Pilih Sebagai Teladan")').first();
        if (await btnPilih.isVisible()) {
            page.once('dialog', dialog => dialog.accept());
            await btnPilih.click();
            await expect(page.locator('.bg-green-50, text=Berhasil')).toBeVisible();
        }
    });
});
