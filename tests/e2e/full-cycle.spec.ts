import { test, expect } from '@playwright/test';

test('full Cycle (development only)', async ({ page }) => {
  await page.goto('http://localhost:8000/login');

  // Login Admin
  await page.getByRole('textbox', { name: 'person Username or NIP' }).click();
  await page.getByRole('textbox', { name: 'person Username or NIP' }).fill('admin@bps.go.id');
  await page.getByRole('textbox', { name: 'lock Password' }).click();
  await page.getByRole('textbox', { name: 'lock Password' }).fill('password123');
  await page.getByRole('button', { name: 'Sign In login' }).click();

  // Set Time
  await page.getByRole('link', { name: 'Manajemen Periode' }).click();
  await page.getByRole('textbox').fill('2026-10-01T12:00');
  await page.getByRole('button', { name: 'Set Fake Time' }).click();
  await page.getByRole('button', { name: 'Logout' }).click();

  // Login Kepala Umum
  await page.getByRole('textbox', { name: 'person Username or NIP' }).click();
  await page.getByRole('textbox', { name: 'person Username or NIP' }).fill('kepalaumum@bps.go.id');
  await page.getByRole('textbox', { name: 'lock Password' }).click();
  await page.getByRole('textbox', { name: 'lock Password' }).fill('password123');
  await page.getByRole('button', { name: 'Sign In login' }).click();

  // Input Presensi
  await page.getByRole('link', { name: 'Input Presensi' }).click();
  await page.getByRole('button', { name: 'Choose File' }).setInputFiles('../data/Rekap Presensi Kab. Bangkalan/01_Rekap Presensi Januari 2026.xlsx');
  await page.getByRole('button', { name: 'Upload Data' }).click();
  await page.locator('#upload_bulan').selectOption('8');
  await page.getByRole('button', { name: 'Choose File' }).setInputFiles('../data/Rekap Presensi Kab. Bangkalan/02_Rekap Presensi Februari 2026.xlsx');
  await page.getByRole('button', { name: 'Upload Data' }).click();
  await page.locator('#upload_bulan').selectOption('9');
  await page.getByRole('button', { name: 'Choose File' }).setInputFiles('../data/Rekap Presensi Kab. Bangkalan/03_Rekap Presensi Maret 2026.xlsx');
  await page.getByRole('button', { name: 'Upload Data' }).click();

  // Input CKP
  await page.getByRole('link', { name: 'Input CKP' }).click();
  await page.getByRole('button', { name: 'Choose File' }).setInputFiles('../data/data_nilai_ckp.csv');
  await page.getByRole('button', { name: 'Upload Data' }).click();
  await page.getByRole('button', { name: 'Logout' }).click();

  // Login Admin
  await page.getByRole('textbox', { name: 'person Username or NIP' }).click();
  await page.getByRole('textbox', { name: 'person Username or NIP' }).fill('admin@bps.go.id');
  await page.getByRole('textbox', { name: 'lock Password' }).click();
  await page.getByRole('textbox', { name: 'lock Password' }).fill('password123');
  await page.getByRole('button', { name: 'Sign In login' }).click();

  //set time 2
  await page.getByRole('textbox').fill('2026-10-06T12:00');
  await page.getByRole('button', { name: 'Set Fake Time' }).click();
  await page.getByRole('button', { name: 'Logout' }).click();
  await page.getByRole('textbox', { name: 'person Username or NIP' }).click();
  await page.getByRole('textbox', { name: 'person Username or NIP' }).fill('pegawai@bps.go.id');
  await page.getByRole('textbox', { name: 'lock Password' }).click();
  await page.getByRole('textbox', { name: 'lock Password' }).fill('password123');
  await page.getByRole('button', { name: 'Sign In login' }).click();
  await page.getByRole('link', { name: 'Voting Kandidat Terbaik' }).click();
  expect(page.getByText('Pegawai memberikan pelayanan'));
});