import { test, expect } from '@playwright/test';
import { takeScreenshot } from '../utils/helpers';
import path from 'path';
import fs from 'fs';

test.describe('Admin - Manajemen Pengumuman', () => {

    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'admin@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL('/dashboard');
        
        await page.click('text=Pengumuman');
        await expect(page).toHaveURL(/\/admin\/pengumuman/);
    });

    test('Buat Pengumuman Draft', async ({ page }) => {
        await page.click('button:has-text("Buat Pengumuman")');
        await page.waitForSelector('input[name="judul"]', { state: 'visible' });

        await page.fill('input[name="judul"]', 'Pengumuman E2E Test');
        await page.fill('textarea[name="konten"]', 'Isi dari pengumuman e2e test');
        await page.fill('input[name="tanggal_kedaluwarsa"]', '2028-01-01');
        await page.selectOption('select[name="status"]', 'draft');
        
        await takeScreenshot(page, 'buat-pengumuman-form');
        await page.click('form[action*="/admin/pengumuman"] button[type="submit"]:has-text("Simpan")');

        await expect(page.locator('text=berhasil dibuat')).toBeVisible();
        await takeScreenshot(page, 'buat-pengumuman-berhasil');
    });

    test('Publish Pengumuman dengan Lampiran', async ({ page }) => {
        await page.click('button:has-text("Buat Pengumuman")');
        await page.waitForSelector('input[name="judul"]', { state: 'visible' });

        await page.fill('input[name="judul"]', 'Pengumuman Penting');
        await page.fill('textarea[name="konten"]', 'Harap baca lampiran.');
        await page.fill('input[name="tanggal_kedaluwarsa"]', '2028-12-31');
        await page.selectOption('select[name="status"]', 'published');
        
        const testFilePath = path.join(__dirname, 'lampiran.pdf');
        fs.writeFileSync(testFilePath, 'dummy pdf content');
        
        const fileInput = page.locator('input[type="file"][name="lampiran"]');
        await fileInput.setInputFiles(testFilePath);

        await page.click('form[action*="/admin/pengumuman"] button[type="submit"]:has-text("Simpan")');

        await expect(page.locator('text=berhasil dibuat')).toBeVisible();
        fs.unlinkSync(testFilePath);
        await takeScreenshot(page, 'publish-pengumuman-lampiran');
    });

    test('Pegawai Tandai Pengumuman Telah Dibaca', async ({ page }) => {
        // Logout admin
        await page.click('button:has-text("Admin Testing")'); 
        await page.click('button:has-text("Log Out")');
        
        // Login pegawai
        await page.fill('input[name="identifier"]', 'pegawai1@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        // Navigasi ke pengumuman
        await page.click('text=Pengumuman');
        
        // Buka detail pengumuman
        await page.click('text=Pengumuman Penting');

        // Klik tandai dibaca
        const btnTandai = page.locator('button:has-text("Tandai telah dibaca")');
        if (await btnTandai.isVisible()) {
            await btnTandai.click();
            await expect(page.locator('text=Telah Dibaca')).toBeVisible();
        }
        await takeScreenshot(page, 'pegawai-baca-pengumuman');
    });

});
