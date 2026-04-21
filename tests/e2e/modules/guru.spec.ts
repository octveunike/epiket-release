import { test, expect } from '@playwright/test';
import { login } from '../auth/auth-helper';
import { randomNumber } from '../utils/random';

test('CRUD Guru', async ({ page }) => {
  const nip = randomNumber(10);
  const nama = 'Fitriani';

  await login(page);

  await page.getByRole('link', { name: /Data Guru/i }).click();

  // CREATE
  await page.getByRole('link', { name: /Tambah Guru/i }).click();

  await page.getByRole('textbox', { name: 'Masukkan NIP' }).fill(nip);
  await page.getByRole('textbox', { name: 'Masukkan Nama Guru' }).fill(nama);
  await page.getByRole('textbox', { name: 'Contoh: Matematika, Bahasa' }).fill('Matematika');

  await page.getByRole('button', { name: /Simpan/i }).click();

  await expect(page.locator('text=Data Guru berhasil ditambahkan')).toBeVisible();

  // SEARCH
  await page.getByRole('searchbox', { name: 'Cari:' }).fill(nama);

  const row = page.getByRole('row', { name: new RegExp(nama, 'i') });
  await expect(row).toBeVisible();

  // EDIT
  await row.getByRole('link', { name: /Edit/i }).click();

  await page.getByRole('textbox', { name: 'Masukkan NIP' }).fill(nip + '1');
  await page.getByRole('textbox', { name: 'Masukkan Nama Guru' }).fill(nama + ' Edited');
  await page.getByRole('textbox', { name: 'Contoh: Matematika, Bahasa' }).fill('Matematika Edited');

  await page.getByRole('button', { name: /Simpan Perubahan/i }).click();

  await expect(page.locator('text=Data Guru berhasil diupdate')).toBeVisible();

  // DELETE
  await page.getByRole('searchbox', { name: 'Cari:' }).fill(nama);

  const rowDelete = page.getByRole('row', { name: new RegExp(nama, 'i') });
  await rowDelete.getByRole('button', { name: /Hapus/i }).click();

  await page.getByRole('button', { name: /Ya, Hapus/i }).click();

  await expect(page.locator('text=Data Guru berhasil dihapus')).toBeVisible();
});