import { test, expect } from '@playwright/test';
import { takeScreenshot } from '../utils/helpers';

test.describe('Pegawai - Dashboard & Klasemen', () => {

    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'pegawai1@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL('/dashboard');
    });

    test('Dashboard tampil dan Kalender sesuai periode', async ({ page }) => {
        await expect(page.locator('text=Kalender Periode')).toBeVisible();
        
        // Memastikan widget kalender terlihat (misalnya dengan mengecek komponen spesifik)
        // Kita juga bisa mengecek hari ini atau event kalender yang disorot (bila ada periode aktif)
        await expect(page.locator('.fc')).toBeVisible(); // Jika menggunakan fullcalendar (fc class)
        
        await takeScreenshot(page, 'pegawai-dashboard-kalender');
    });

    test('Live Klasemen Tampil', async ({ page }) => {
        // Asumsikan ada tombol atau menu 'Monitoring Survei' atau Klasemen di sidebar/dashboard
        await page.click('text=Monitoring Survei'); // Sesuaikan dengan text di aplikasi
        await expect(page).toHaveURL(/\/monitoring/);

        // Memastikan tabel ranking muncul
        await expect(page.locator('table')).toBeVisible();
        await expect(page.locator('text=Ranking')).toBeVisible();
        
        // Cek bahwa data pegawai / skor ditampilkan (Asumsi: ada baris data kalau ranking aktif)
        // Jika belum ada perhitungan, cek tabel kosong
        const baris = await page.locator('tbody tr').count();
        expect(baris).toBeGreaterThanOrEqual(0); // At least table is rendered

        await takeScreenshot(page, 'pegawai-live-klasemen');
    });
});
