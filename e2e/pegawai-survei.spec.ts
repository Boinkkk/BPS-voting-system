import { test, expect } from '@playwright/test';

test.describe('Pegawai Survei Tests', () => {
  test.beforeEach(async ({ page }) => {
    // Login as Pegawai
    await page.goto('/login');
    await page.fill('input[name="identifier"]', 'pegawai@bps.go.id');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/dashboard/);
  });

  test('Pegawai can access and submit survey for candidates', async ({ page }) => {
    await page.goto('/survey');
    
    // Cek apakah sudah mengisi (muncul teks "Terima Kasih!")
    const isDone = await page.locator('text="Terima Kasih!"').isVisible();
    const isEmpty = await page.locator('text="Belum ada kandidat yang terpilih"').isVisible();
    
    if (!isDone && !isEmpty) {
        // Mengisi Survey (UI Baru: multi-step)
        let hasNextStep = true;
        while (hasNextStep) {
            await page.waitForTimeout(300);

            // Cari semua baris kandidat di step yang TAMPIL
            const visibleRows = await page.locator('.step-section:visible tbody tr').all();
            
            for (const row of visibleRows) {
                const labels = await row.locator('label').all();
                if (labels.length > 0) {
                    const randomIndex = Math.floor(Math.random() * labels.length);
                    await labels[randomIndex].click({ force: true });
                }
            }

            const nextButton = page.locator('.step-section:visible button:has-text("Selanjutnya")');
            const submitButton = page.locator('.step-section:visible button[type="submit"]:has-text("Kirim Semua Penilaian")');

            if (await nextButton.isVisible()) {
                await nextButton.click();
                await page.waitForTimeout(400); 
            } else if (await submitButton.isVisible()) {
                hasNextStep = false;
                await Promise.all([
                    page.waitForNavigation(),
                    submitButton.click()
                ]);
            } else {
                hasNextStep = false;
            }
        }

        // Setelah dikirim, pastikan muncul notifikasi sukses
        await expect(page).toHaveURL(/.*survey/);
        await expect(page.locator('.bg-green-50')).toBeVisible();
    }
  });

  test('Pegawai can view and update Profile', async ({ page }) => {
    await page.goto('/profile');
    
    // Test open modal
    await page.click('text=Change Password');

    // Coba update password
    await page.fill('input[name="current_password"]', 'password123');
    await page.fill('input[name="password"]', 'password1234');
    await page.fill('input[name="password_confirmation"]', 'password1234');
    await page.click('button:has-text("Simpan Perubahan")');
    
    // Pastikan password berhasil diubah (kemudian kita kembalikan lagi agar tidak merusak tes lain yang memakai user ini)
    // Teks di backend biasanya mengembalikan redirect back() with('success', '...')
    await expect(page.locator('.bg-green-50')).toBeVisible();

    // Revert password
    await page.click('text=Change Password');
    await page.fill('input[name="current_password"]', 'password1234');
    await page.fill('input[name="password"]', 'password123');
    await page.fill('input[name="password_confirmation"]', 'password123');
    await page.click('button:has-text("Simpan Perubahan")');
    await expect(page.locator('.bg-green-50')).toBeVisible();
  });
});
