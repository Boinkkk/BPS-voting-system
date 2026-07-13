import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';
import path from 'path';

let users: { email?: string, nip?: string }[] = [];

// Fetch users synchronously before declaring tests
try {
    const scriptPath = path.resolve(process.cwd(), 'e2e', 'get_users.php');
    const output = execSync(`php "${scriptPath}"`).toString();
    const jsonMatch = output.match(/\[.*\]/);
    if (jsonMatch) {
        users = JSON.parse(jsonMatch[0]);
    } else {
        console.error("Could not find JSON in PHP output:", output);
    }
} catch (e) {
    console.error("Failed to fetch users:", e);
}

// Aktifkan mode parallel agar worker bisa memproses file ini secara bersamaan
test.describe.configure({ mode: 'parallel' });

test.describe('Survey Automation untuk Semua User', () => {
    
    if (users.length === 0) {
        test('Tidak ada user ditemukan', async () => {
            console.log('Pastikan ada user Pegawai di database.');
        });
        return;
    }

    for (const user of users) {
        const identifier = user.nip || user.email || 'unknown';
        
        // Setiap user mendapatkan blok test mandiri yang akan dijalankan oleh worker secara paralel
        test(`User ${identifier} mengisi survey`, async ({ browser }) => {
            test.setTimeout(1000 * 60 * 15); // Beri batas waktu 15 menit per user

            const context = await browser.newContext({
                baseURL: 'http://localhost:8000'
            });
            const page = await context.newPage();

            // 1. Log in
            await page.goto('/login');
            await page.fill('input[name="identifier"]', identifier);
            await page.fill('input[name="password"]', 'password123'); // Password default
            await page.click('button[type="submit"]');

            // Cek sukses login
            await expect(page).toHaveURL(/.*dashboard/);

            // 2. Ke halaman index survey
            await page.goto('/survey');

            // Cek apakah sudah mengisi (muncul teks "Terima Kasih!")
            const isDone = await page.locator('text="Terima Kasih!"').isVisible();
            const isEmpty = await page.locator('text="Belum ada kandidat yang terpilih"').isVisible();
            
            if (!isDone && !isEmpty) {
                // 3. Mengisi Survey (UI Baru: Setiap step mewakili 1 pertanyaan, di dalamnya ada list SEMUA kandidat)
                let hasNextStep = true;
                while (hasNextStep) {
                    // Pastikan menunggu elemen dirender sepenuhnya
                    await page.waitForTimeout(300);

                    // Cari semua baris kandidat di step yang TAMPIL
                    const visibleRows = await page.locator('.step-section:visible tbody tr').all();
                    
                    for (const row of visibleRows) {
                        // Cari bintang / label (dari 1 sampai 5)
                        const labels = await row.locator('label').all();
                        if (labels.length > 0) {
                            // Pilih bintang secara acak untuk kandidat tersebut
                            const randomIndex = Math.floor(Math.random() * labels.length);
                            // force: true dibutuhkan karena input asli disembunyikan CSS
                            await labels[randomIndex].click({ force: true });
                        }
                    }

                    // Cek tombol di step ini
                    const nextButton = page.locator('.step-section:visible button:has-text("Selanjutnya")');
                    const submitButton = page.locator('.step-section:visible button[type="submit"]:has-text("Kirim Semua Penilaian")');

                    if (await nextButton.isVisible()) {
                        await nextButton.click();
                        // Tunggu sebentar untuk transisi UI
                        await page.waitForTimeout(400); 
                    } else if (await submitButton.isVisible()) {
                        // Jika tidak ada "Selanjutnya" tapi ada "Kirim Semua Penilaian", ini step terakhir
                        hasNextStep = false;
                        await Promise.all([
                            page.waitForNavigation(),
                            submitButton.click()
                        ]);
                    } else {
                        // Jika tidak ada tombol sama sekali, keluar (menghindari infinite loop)
                        hasNextStep = false;
                        console.log(`Tidak menemukan tombol navigasi pada User ${identifier}`);
                    }
                }

                // Setelah dikirim, pastikan muncul notifikasi sukses
                await expect(page).toHaveURL(/.*survey/);
                await expect(page.locator('.bg-green-50')).toBeVisible();
            }

            // Playwright akan mereset session secara otomatis melalui context.close() 
            // tanpa perlu melakukan klik tombol logout (terutama karena tombol Sign Out sudah tidak ada di layout utama)
            await context.close();
        });
    }
});
