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

            // 3. Looping semua kandidat yang belum diisi (tombol "Mulai Survey")
            while (true) {
                // Ambil ulang semua link kandidat karena kita akan bolak-balik halaman
                const candidateLinks = await page.locator('a:has-text("Mulai Survey")').evaluateAll(elements => 
                    elements.map(el => (el as HTMLAnchorElement).href)
                );

                if (candidateLinks.length === 0) {
                    break; // Tidak ada lagi survey yang harus diisi
                }

                // Masuk ke kandidat pertama di daftar yang tersisa
                await page.goto(candidateLinks[0]);

                // 4. Mengisi Survey (Multi-step UI Form)
                let hasNextStep = true;
                while (hasNextStep) {
                    // Cari baris pertanyaan di tahap/step yang sedang TAMPIL saja
                    const visibleRows = await page.locator('.step-section:visible tbody tr').all();
                    
                    for (const row of visibleRows) {
                        // Cari bintang / label (dari 1 sampai 5)
                        const labels = await row.locator('label').all();
                        if (labels.length > 0) {
                            // Pilih bintang secara acak
                            const randomIndex = Math.floor(Math.random() * labels.length);
                            // force: true dibutuhkan karena struktur UI Bintang menyembunyikan input aslinya
                            await labels[randomIndex].click({ force: true });
                        }
                    }

                    // Cek apakah ada tombol "Selanjutnya" di tahap ini
                    const nextButton = page.locator('.step-section:visible button:has-text("Selanjutnya")');
                    if (await nextButton.isVisible()) {
                        await nextButton.click();
                        // Tunggu sebentar untuk transisi UI
                        await page.waitForTimeout(400); 
                    } else {
                        // Jika tidak ada "Selanjutnya", berarti ini tahap terakhir
                        hasNextStep = false;
                        
                        // Klik tombol Simpan
                        await page.click('.step-section:visible button[type="submit"]:has-text("Simpan Penilaian")');
                    }
                }

                // Setelah simpan, pastikan kembali ke halaman index survey dan ada notifikasi sukses
                await expect(page).toHaveURL(/.*survey/);
                await expect(page.locator('.bg-green-50')).toBeVisible();
            }

            // Logout setelah semua kandidat selesai disurvey
            await page.click('button:has-text("Logout"), a:has-text("Logout"), button:has-text("Sign Out")');
            await context.close();
        });
    }
});
