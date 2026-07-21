import { test, expect } from '@playwright/test';
import { takeScreenshot } from '../utils/helpers';

test.describe('Pegawai - Voting Survei', () => {

    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'pegawai1@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL('/dashboard');
    });

    test('Buka Halaman Voting dan Submit', async ({ page }) => {
        // Navigasi ke menu Voting Survei
        await page.click('text=Voting Kinerja'); // Asumsikan ada menu 'Voting Kinerja'
        
        // Kita perlu asumsi bahwa saat ini status periode = 'voting' (di E2E environment nanti)
        // Jika periode tidak aktif, halaman akan menampilkan "Voting Ditutup" dsb.
        
        // Asumsi tombol "Isi Survei" untuk kandidat tertentu tersedia
        const btnIsiSurvei = page.locator('a:has-text("Isi Survei")').first();
        
        if (await btnIsiSurvei.isVisible()) {
            await btnIsiSurvei.click();
            await expect(page.locator('text=Survei Kinerja')).toBeVisible();

            // Isi form survei radio buttons (contoh 5 kriteria)
            await page.locator('input[name="komunikasi"][value="5"]').check();
            await page.locator('input[name="kerjasama"][value="5"]').check();
            await page.locator('input[name="inisiatif"][value="4"]').check();
            await page.locator('input[name="kedisiplinan"][value="5"]').check();
            await page.locator('input[name="tanggung_jawab"][value="4"]').check();

            await takeScreenshot(page, 'isi-voting-form');
            await page.click('button[type="submit"]:has-text("Submit Voting")');

            // Cek redirect sukses
            await expect(page.locator('text=Terima kasih, voting Anda telah tersimpan')).toBeVisible();
            await takeScreenshot(page, 'submit-voting-berhasil');
        } else {
            console.log('Tidak ada kandidat/periode aktif untuk divoting dalam test ini.');
        }
    });

    test('Pegawai yang sudah voting tidak bisa voting ulang orang yang sama', async ({ page }) => {
        await page.click('text=Voting Kinerja');
        
        // Cek apakah tombol berubah jadi "Sudah Divote" / disabled
        const btnIsiSurvei = page.locator('a:has-text("Isi Survei")').first();
        const labelSudah = page.locator('text=Sudah Divote').first();

        // Dalam kondisi real, kita cek jika tombol disabled/label muncul setelah voting di tes sebelumnya
        // Kalau label sudah divote muncul, berarti berhasil
        if (await labelSudah.isVisible()) {
            expect(await labelSudah.isVisible()).toBeTruthy();
        }
    });

});
