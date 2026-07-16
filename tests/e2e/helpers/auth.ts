import {expect, Page} from '@playwright/test'

export async function loginAdmin(page: Page) {
    await page.goto('/login')
    await page.fill('input[name="identifier"]', 'admin@bps.go.id');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/dashboard/)  
}

export async function loginKepalaUmum(page: Page) {
    await page.goto('/login')
    await page.fill('input[name="identifier"]', 'kepalaumum@bps.go.id');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/dashboard/)   
}

export async function loginKepalaKantor(page: Page) {
    await page.goto('/login')
    await page.fill('input[name="identifier"]', 'kepala@bps.go.id');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/dashboard/)   
}

export async function loginPegawai(page: Page) {
    await page.goto('/login')
    await page.fill('input[name="identifier"]', 'tatok13@bps.go.id');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/dashboard/)   
}

export async function logout(page: Page){
    await expect(page.getByRole('button', {name: 'Logout'}))
    await page.getByRole('button', { name: 'Logout' }).click()
    await expect(page).toHaveURL(/\/login/)
}