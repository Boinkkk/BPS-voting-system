import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';
import path from 'path';

// Fetch users from the database using a simple artisan command via execSync
let users: { email: string }[] = [];

test.beforeAll(async () => {
    try {
        const scriptPath = path.resolve(process.cwd(), 'e2e', 'get_users.php');
        const output = execSync(`php "${scriptPath}"`).toString();
        // Look for JSON array in the output
        const jsonMatch = output.match(/\[.*\]/);
        if (jsonMatch) {
            users = JSON.parse(jsonMatch[0]);
        } else {
            console.error("Could not find JSON in PHP output:", output);
        }
    } catch (e) {
        console.error("Failed to fetch users:", e);
    }
});

test('Setiap User mengisi survey secara lengkap dengan penilaian random', async ({ browser }) => {
    test.setTimeout(1000 * 60 * 60); // Allow 1 hour for all users to complete

    if (users.length === 0) {
        console.log('No users found to process.');
        return;
    }

    for (const user of users) {
        const context = await browser.newContext({
            baseURL: 'http://localhost:8000'
        });
        const page = await context.newPage();

        // 1. Log in
        await page.goto('/login');
        await page.fill('input[name="identifier"]', user.email);
        await page.fill('input[name="password"]', 'password123'); // Default password from seeder
        await page.click('button[type="submit"]');

        // Check if successfully logged in
        await expect(page).toHaveURL(/.*dashboard/);

        // 2. Go to survey index
        await page.goto('/survey');

        // 3. Find all candidate survey links that have not been filled
        const candidateLinks = await page.locator('a:has-text("Mulai Survey")').evaluateAll(elements => 
            elements.map(el => (el as HTMLAnchorElement).href)
        );

        for (const link of candidateLinks) {
            // Go to the survey form
            await page.goto(link);

            // 4. Fill random ratings for all questions
            // Find all questions (table rows that contain radio buttons)
            const rows = await page.locator('tbody tr').all();
            
            for (const row of rows) {
                const radios = await row.locator('input[type="radio"]').all();
                if (radios.length > 0) {
                    // Pick a random rating from 1 to 5
                    const randomIndex = Math.floor(Math.random() * radios.length);
                    await radios[randomIndex].check();
                }
            }

            // 5. Submit survey
            await page.click('button[type="submit"]:has-text("Simpan Penilaian")');
            
            // Wait to return to index and see success message
            await expect(page).toHaveURL(/.*survey/);
            await expect(page.locator('.bg-green-50')).toBeVisible();
        }

        // Logout
        await page.click('button:has-text("Logout"), a:has-text("Logout"), button:has-text("Sign Out")');
        await context.close();
    }
});
