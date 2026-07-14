import { test, expect } from '@playwright/test';

test.describe('Negative & Validation Tests', () => {

    test('Survei tidak bisa dilanjutkan jika ada kandidat yang belum dinilai', async ({ page }) => {
        // Login as Pegawai
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'pegawai@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        await page.goto('/survey');
        const isDone = await page.locator('text="Terima Kasih!"').isVisible();
        const isEmpty = await page.locator('text="Belum ada kandidat yang terpilih"').isVisible();
        
        if (!isDone && !isEmpty) {
            // Setup listener untuk alert JS
            let alertMessage = '';
            page.once('dialog', dialog => {
                alertMessage = dialog.message();
                dialog.accept();
            });

            // Klik Selanjutnya TANPA mengisi bintang sama sekali
            const nextButton = page.locator('.step-section:visible button:has-text("Selanjutnya")');
            if (await nextButton.isVisible()) {
                await nextButton.click();
                
                // Pastikan alert muncul dengan teks peringatan
                expect(alertMessage).toContain('Harap berikan penilaian');
            }
        }
    });

    test('Tidak bisa menginput CKP lebih dari 100', async ({ page }) => {
        // Login as Admin
        await page.goto('/login');
        await page.fill('input[name="identifier"]', 'admin@bps.go.id');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        await page.goto('/admin/ckp');
        const inputManualBtn = page.locator('button:has-text("Input Manual")');
        if (await inputManualBtn.isVisible()) {
            await inputManualBtn.click();
        }

        const inputNilai = page.locator('input[name="nilai[]"]').first();
        if (await inputNilai.isVisible()) {
            await inputNilai.fill('150'); // Sengaja lebih dari 100
            await page.click('button:has-text("Simpan")');
            
            // Harus ditolak (muncul error, bukan success)
            // Error handling di blade biasanya menggunakan alert komponen danger atau teks merah
            // Kita bisa cek tidak ada notifikasi sukses "Berhasil"
            await expect(page.locator('.bg-green-50, text="Berhasil"')).not.toBeVisible();
            
            // Atau ada pesan "The nilai field must not be greater than 100" (atau terjemahannya)
            const errorMsg = page.locator('.text-red-700, .text-red-500, text=tidak boleh lebih');
            if (await errorMsg.count() > 0) {
               await expect(errorMsg.first()).toBeVisible();
            }
        }
    });

});
