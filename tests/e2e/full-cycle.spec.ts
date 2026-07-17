import { test, expect } from '@playwright/test';
import path from 'path';
import { execSync } from 'child_process';

test('full Cycle (development only)', async ({ page }) => {
    test.setTimeout(10 * 60 * 60 * 1000)
    console.log('Migrate Fresh Database')
    
    execSync('php artisan migrate:fresh --seed', { cwd: process.cwd() });
    console.log('Migration Done!')
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

        expect(page.locator('text="Terima Kasih!"').isVisible());
        expect(page.locator('text="Belum ada kandidat yang terpilih"').isVisible());
        // 1. Test UI Voting (Happy Path) tanpa delay slowMo
        // Kita jalankan di dalam browser context (page.evaluate) agar klik instan
        await page.evaluate(() => {
            // Ambil semua baris kandidat di semua step (meskipun hidden)
            const allRows = document.querySelectorAll('.step-section tbody tr');
            
            allRows.forEach(row => {
                const labels = row.querySelectorAll('label');
                if (labels.length > 0) {
                    const randomIndex = Math.floor(Math.random() * labels.length);
                    (labels[randomIndex] as HTMLElement).click();
                }
            });

            // Pindah langsung ke step terakhir agar tombol Submit muncul
            // @ts-ignore
            if (typeof changeStep === 'function' && typeof totalSteps !== 'undefined') {
                // @ts-ignore
                changeStep(totalSteps);
            }
        });
        // Submit (hanya 1 klik ini yang akan terkena slowMo 1 detik)
        const submitButton = page.locator('button[type="submit"]:has-text("Kirim Semua Penilaian")');
        await Promise.all([
            page.waitForNavigation(),
            submitButton.click()
        ]);
        // Setelah dikirim, pastikan muncul notifikasi sukses
        await expect(page).toHaveURL(/.*survey/);
        await expect(page.locator('.bg-green-50')).toBeVisible();

                // Setelah dikirim, pastikan muncul notifikasi sukses
                await expect(page).toHaveURL(/.*survey/);
                await expect(page.locator('.bg-green-50')).toBeVisible();

        // 2. Inject sisa data voting agar cepat
        console.log('Menjalankan Artisan Command test:seed-votes...');
        execSync('php artisan test:seed-votes', { cwd: process.cwd() });
        console.log('Data voting berhasil di-seed.');

        await page.getByRole('button', { name: 'Logout' }).click();

        // Admin Login set time 3
          // Login Admin
        await page.getByRole('textbox', { name: 'person Username or NIP' }).click();
        await page.getByRole('textbox', { name: 'person Username or NIP' }).fill('admin@bps.go.id');
        await page.getByRole('textbox', { name: 'lock Password' }).click();
        await page.getByRole('textbox', { name: 'lock Password' }).fill('password123');
        await page.getByRole('button', { name: 'Sign In login' }).click();

        //set time 2
        await page.getByRole('textbox').fill('2026-10-09T12:00');
        await page.getByRole('button', { name: 'Set Fake Time' }).click();
        await page.getByRole('button', { name: 'Logout' }).click();

        // Loign Kepala
        await page.getByRole('textbox', { name: 'person Username or NIP' }).click();
        await page.getByRole('textbox', { name: 'person Username or NIP' }).fill('kepala@bps.go.id');
        await page.getByRole('textbox', { name: 'lock Password' }).click();
        await page.getByRole('textbox', { name: 'lock Password' }).fill('password123');
        await page.getByRole('button', { name: 'Sign In login' }).click();

        // Revie nominasi
        await page.getByRole('link', { name: 'Review Nominasi' }).click();
        await page.getByRole('button', { name: 'Tetapkan Sebagai Terbaik' }).nth(1).click();
        await page.getByRole('button', { name: 'Ya, Tetapkan Sekarang' }).click();
        await page.getByRole('button', { name: 'Logout' }).click();

        // Login admin dan settime
        await page.getByRole('textbox', { name: 'person Username or NIP' }).click();
        await page.getByRole('textbox', { name: 'person Username or NIP' }).fill('admin@bps.go.id');
        await page.getByRole('textbox', { name: 'lock Password' }).click();
        await page.getByRole('textbox', { name: 'lock Password' }).fill('password123');

        await page.getByRole('button', { name: 'Sign In login' }).click();
        await page.getByRole('textbox').fill('2026-10-10T12:00');
        await page.getByRole('button', { name: 'Set Fake Time' }).click();

        console.log('already set')
        await page.goto('http://localhost:8000/dashboard');
        await expect(
                page.getByText('HASIL PEMILIHAN KARYAWAN')
            ).toBeVisible();


        await page.pause();

    })
});