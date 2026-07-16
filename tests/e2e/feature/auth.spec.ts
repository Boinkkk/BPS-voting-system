import { loginAdmin,logout,loginKepalaUmum,loginPegawai,loginKepalaKantor } from "../helpers/auth";
import { test, expect, Page } from "@playwright/test";

test('Admin Login', async ({page}) => {
    await loginAdmin(page);
    expect(page.getByRole('link', { name: 'Data Pegawai' }))
    await logout(page)
});

test('Pegawai Login', async ({page}) => {
    await loginPegawai(page);
    expect(page.getByRole('link', { name: 'Voting Kandidat Terbaik' }))
    await logout(page)
});

test('Kepala Umum Login', async ({page}) => {
    await loginKepalaUmum(page);
    expect(page.getByRole('link', { name: 'Input Presensi' }))
    await logout(page)
});

test('Kepala Kantor Login', async ({page}) => {
    await loginKepalaKantor(page);
    expect(page.getByRole('link', { name: 'Review Nominasi' }))
    await logout(page)
});