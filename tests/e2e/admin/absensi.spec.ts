import { test, expect } from '@playwright/test';
import { takeScreenshot } from '../utils/helpers';
import path from 'path';
import fs from 'fs';

test.describe('Admin - Manajemen Absensi', () => {

    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'timpenilai@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL('/dashboard');
        
        await page.click('text=Rekap Absensi');
        await expect(page).toHaveURL(/\/admin\/absensi/);
    });

    test('Upload Absensi Berhasil', async ({ page }) => {
        const testFilePath = path.join(__dirname, 'dummy-absen.xlsx');
        fs.writeFileSync(testFilePath, 'dummy data absen');
        
        await page.click('button:has-text("Upload Excel")');
        
        const fileInput = page.locator('input[type="file"][name="file"]');
        await fileInput.setInputFiles(testFilePath);
        // Pilih bulan absensi (Misal Juli = 7)
        await page.selectOption('select[name="bulan"]', '7');
        
        await takeScreenshot(page, 'upload-absensi-form');
        
        await page.click('form[action*="/admin/absensi/upload"] button[type="submit"]:has-text("Upload")');
        
        fs.unlinkSync(testFilePath);
    });

    test('Input Manual Absensi Berhasil', async ({ page }) => {
        await page.click('button:has-text("Input Manual")');
        
        await page.waitForSelector('select[name="id_pegawai"]', { state: 'visible' });

        await page.selectOption('select[name="id_pegawai"]', { label: 'Pegawai Dua' });
        await page.fill('input[name="bulan"]', '7');
        await page.fill('input[name="kjk"]', '10');
        await page.fill('input[name="tk"]', '0');
        await page.fill('input[name="terlambat"]', '1');
        await page.fill('input[name="pulang_cepat"]', '0');
        
        await takeScreenshot(page, 'input-manual-absensi-form');
        await page.click('form[action*="/admin/absensi/manual"] button[type="submit"]:has-text("Simpan")');

        await expect(page.locator('text=Data absensi manual berhasil ditambahkan')).toBeVisible();
        await takeScreenshot(page, 'input-manual-absensi-berhasil');
    });

});
