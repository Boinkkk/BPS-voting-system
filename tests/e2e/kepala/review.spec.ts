import { test, expect } from '@playwright/test';
import { takeScreenshot } from '../utils/helpers';

test.describe('Kepala Kantor - Review & Penetapan Pemenang', () => {

    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'kepala@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL('/dashboard');
    });

    test('Halaman Review tampil dan hanya memuat Top 3', async ({ page }) => {
        // Navigasi ke menu Review Kandidat
        await page.click('text=Review Kandidat'); // Sesuaikan dengan text di aplikasi
        await expect(page).toHaveURL(/\/kepala\/review/);
        
        // Memastikan tabel top 3 muncul
        await expect(page.locator('table')).toBeVisible();
        
        // Asumsi jika data sudah ada, baris data tidak lebih dari 3
        const barisKandidat = await page.locator('tbody tr').count();
        expect(barisKandidat).toBeLessThanOrEqual(3);

        await takeScreenshot(page, 'kepala-review-top-3');
    });

    test('Memilih pemenang berhasil', async ({ page }) => {
        await page.click('text=Review Kandidat');
        
        // Pilih salah satu kandidat sebagai pemenang. Asumsi ada tombol "Pilih"
        const btnPilih = page.locator('button:has-text("Pilih")').first();

        if (await btnPilih.isVisible()) {
            await btnPilih.click();

            // Wait for modal konfirmasi penetapan pemenang
            await page.waitForSelector('textarea[name="catatan_kepala"]', { state: 'visible' });

            // Isi catatan pemenang
            await page.fill('textarea[name="catatan_kepala"]', 'Pegawai ini sangat layak menjadi pemenang karena dedikasinya yang luar biasa pada triwulan ini.');

            await takeScreenshot(page, 'form-pilih-pemenang');
            
            // Simpan
            await page.click('form[action*="/kepala/pilih"] button[type="submit"]:has-text("Simpan Penetapan")');

            // Cek notifikasi sukses
            await expect(page.locator('text=Pemenang berhasil ditetapkan')).toBeVisible();
            await takeScreenshot(page, 'penetapan-pemenang-berhasil');
        } else {
            console.log('Belum ada kandidat / belum periode review.');
        }
    });

    test('Gagal simpan jika catatan kosong', async ({ page }) => {
        await page.click('text=Review Kandidat');
        const btnPilih = page.locator('button:has-text("Pilih")').first();

        if (await btnPilih.isVisible()) {
            await btnPilih.click();

            await page.waitForSelector('textarea[name="catatan_kepala"]', { state: 'visible' });
            
            // Kosongkan catatan
            await page.fill('textarea[name="catatan_kepala"]', '');
            
            await page.click('form[action*="/kepala/pilih"] button[type="submit"]:has-text("Simpan Penetapan")');
            
            // Should show validation error (either HTML5 required or backend error)
            const input = page.locator('textarea[name="catatan_kepala"]');
            const isRequired = await input.evaluate((el: HTMLTextAreaElement) => el.required);
            
            if (isRequired) {
                expect(isRequired).toBeTruthy();
            } else {
                await expect(page.locator('text=Catatan wajib diisi')).toBeVisible();
            }
        }
    });

});
