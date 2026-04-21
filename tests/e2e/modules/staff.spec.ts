import { test, expect } from '@playwright/test';
import { login } from '../auth/auth-helper';

test('CRUD Staff', async ({ page }) => {
  const nama = 'Syamsudin';

  await login(page);

  await page.getByRole('link', { name: /Data Staf/i }).click();

  // CREATE
  await page.getByRole('link', { name: /Tambah Staf/i }).click();

  await page.getByRole('textbox', { name: 'Masukkan Nama Staf' }).fill(nama);

  await page.getByRole('button', { name: /Simpan/i }).click();

  await expect(page.locator('text=Data Staff berhasil')).toBeVisible();

  // SEARCH
  await page.getByRole('searchbox', { name: 'Cari:' }).fill(nama);

  const row = page.getByRole('row', { name: new RegExp(nama, 'i') });
  await expect(row).toBeVisible();

  // EDIT
  await row.getByRole('link', { name: /Edit/i }).click();

  await page.getByRole('textbox', { name: 'Masukkan Nama Staf' }).fill(nama + ' Edited');

  await page.getByRole('button', { name: /Simpan Perubahan/i }).click();

  await expect(page.locator('text=Data Staff berhasil diupdate')).toBeVisible();

  // DELETE
  await page.getByRole('searchbox', { name: 'Cari:' }).fill(nama);

  const rowDelete = page.getByRole('row', { name: new RegExp(nama, 'i') });
  await rowDelete.getByRole('button', { name: /Hapus/i }).click();

  await page.getByRole('button', { name: /Ya, Hapus/i }).click();

  await expect(page.locator('text=Data Staff berhasil dihapus')).toBeVisible();
});