import { test, expect } from '@playwright/test';
import path from 'path';

test('full Cycle (development only)', async ({ page }) => {
    test.setTimeout(10 * 60 * 60 * 1000)

    await test.step("Fase 1 (manajemen Periode, input absensi dan , input-ckp", async () => {
        await page.goto('http://localhost:8000/login');

        // Login Admin
        await page.getByRole('textbox', { name: 'person Username or NIP' }).click();
        await page.getByRole('textbox', { name: 'person Username or NIP' }).fill('admin@bps.go.id');
        await page.getByRole('textbox', { name: 'lock Password' }).click();
        await page.getByRole('textbox', { name: 'lock Password' }).fill('password123');
        await page.getByRole('button', { name: 'Sign In login' }).click();

        // Set Time
        await page.getByRole('link', { name: 'Manajemen Periode' }).click();
        await page.getByRole('textbox').fill('2026-10-01T12:00');
        await page.getByRole('button', { name: 'Set Fake Time' }).click();
        await page.getByRole('button', { name: 'Logout' }).click();

        // Login Kepala Umum
        await page.getByRole('textbox', { name: 'person Username or NIP' }).click();
        await page.getByRole('textbox', { name: 'person Username or NIP' }).fill('kepalaumum@bps.go.id');
        await page.getByRole('textbox', { name: 'lock Password' }).click();
        await page.getByRole('textbox', { name: 'lock Password' }).fill('password123');
        await page.getByRole('button', { name: 'Sign In login' }).click();

        // Input Presensi
        await page.getByRole('link', { name: 'Input Presensi' }).click();
        await page.getByRole('button', { name: 'Choose File' }).setInputFiles(path.join(process.cwd(), 'data', 'Rekap Presensi Kab. Bangkalan', '01_Rekap Presensi Januari 2026.xlsx'));
        await page.getByRole('button', { name: 'Upload Data' }).click();
        await page.locator('#upload_bulan').selectOption('8');
        await page.getByRole('button', { name: 'Choose File' }).setInputFiles(path.join(process.cwd(), 'data', 'Rekap Presensi Kab. Bangkalan', '02_Rekap Presensi Februari 2026.xlsx'));
        await page.getByRole('button', { name: 'Upload Data' }).click();
        await page.locator('#upload_bulan').selectOption('9');
        await page.getByRole('button', { name: 'Choose File' }).setInputFiles(path.join(process.cwd(), 'data', 'Rekap Presensi Kab. Bangkalan', '03_Rekap Presensi Maret 2026.xlsx'));
        await page.getByRole('button', { name: 'Upload Data' }).click();

        // Input CKP
        await page.getByRole('link', { name: 'Input CKP' }).click();
        await page.getByRole('button', { name: 'Choose File' }).setInputFiles(path.join(process.cwd(),'data' , 'data_nilai_ckp.csv'));
        await page.getByRole('button', { name: 'Upload Data' }).click();
        await page.getByRole('button', { name: 'Logout' }).click();

        // Login Admin
        await page.getByRole('textbox', { name: 'person Username or NIP' }).click();
        await page.getByRole('textbox', { name: 'person Username or NIP' }).fill('admin@bps.go.id');
        await page.getByRole('textbox', { name: 'lock Password' }).click();
        await page.getByRole('textbox', { name: 'lock Password' }).fill('password123');
        await page.getByRole('button', { name: 'Sign In login' }).click();

        //set time 2
        await page.getByRole('textbox').fill('2026-10-06T12:00');
        await page.getByRole('button', { name: 'Set Fake Time' }).click();
        await page.getByRole('button', { name: 'Logout' }).click();
        // Login Pegawai untuk testing UI Voting 1 user
        await page.getByRole('textbox', { name: 'person Username or NIP' }).click();
        await page.getByRole('textbox', { name: 'person Username or NIP' }).fill('pegawai@bps.go.id');
        await page.getByRole('textbox', { name: 'lock Password' }).click();
        await page.getByRole('textbox', { name: 'lock Password' }).fill('password123');
        await page.getByRole('button', { name: 'Sign In login' }).click();
        await page.getByRole('link', { name: 'Voting Kandidat Terbaik' }).click();
        
        // Pastikan masuk ke halaman voting
        await expect(page.getByText('Survey Penilaian Kandidat Pegawai Terbaik')).toBeVisible();

        // 1. Test UI Voting (Happy Path)
        // Isi semua radio button dengan nilai 5 (Sangat Baik)
        await page.waitForSelector('input[type="radio"]');
        const radios = await page.locator('input[type="radio"][value="5"]').all();
        for (const radio of radios) {
            await radio.check({ force: true });
        }

        // Klik "Selanjutnya" sampai halaman terakhir
        while (await page.locator('button:has-text("Selanjutnya"):visible').count() > 0) {
            await page.locator('button:has-text("Selanjutnya"):visible').click();
        }

        // Submit
        await page.getByRole('button', { name: 'Kirim Semua Penilaian' }).click();
        await expect(page.getByText('Survey berhasil disimpan!')).toBeVisible();
        await page.getByRole('button', { name: 'Logout' }).click();

        // 2. Inject sisa data voting agar cepat
        console.log('Menjalankan Artisan Command test:seed-votes...');
        const { execSync } = require('child_process');
        execSync('php artisan test:seed-votes', { cwd: process.cwd() });
        console.log('Data voting berhasil di-seed.');

    })
});