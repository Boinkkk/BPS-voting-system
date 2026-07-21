import { Page } from '@playwright/test';
import path from 'path';
import fs from 'fs';

/**
 * Takes a screenshot only if ENABLE_SCREENSHOTS is set to 'true'.
 * Saves the screenshot to tests/e2e/screenshots directory.
 * 
 * @param page The Playwright Page object.
 * @param name The name of the screenshot (without extension).
 */
export async function takeScreenshot(page: Page, name: string) {
    const enableScreenshots = process.env.ENABLE_SCREENSHOTS === 'true';
    
    if (enableScreenshots) {
        const screenshotsDir = path.resolve(__dirname, '../screenshots');
        
        // Ensure directory exists
        if (!fs.existsSync(screenshotsDir)) {
            fs.mkdirSync(screenshotsDir, { recursive: true });
        }

        const safeName = name.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        const timestamp = new Date().getTime();
        const filepath = path.join(screenshotsDir, `${safeName}-${timestamp}.png`);
        
        await page.screenshot({ path: filepath, fullPage: true });
        console.log(`📸 Screenshot saved: ${filepath}`);
    }
}
