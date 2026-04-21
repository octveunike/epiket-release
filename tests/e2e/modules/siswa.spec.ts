import { test, expect } from '@playwright/test';
import { login } from '../auth/auth-helper';
import { randomNumber } from '../utils/random';

test('CRUD Siswa', async ({ page }) => {
  const nis = randomNumber(8);
  const nama = 'Salsabila';

  await login(page);

  await page.getByRole('link', { name: /Data Siswa/i }).click();

  // CREATE
  await page.getByRole('link', { name: /Tambah Siswa/i }).click();

  await page.getByRole('textbox', { name: 'Masukkan NIS' }).fill(nis);
  await page.getByRole('textbox', { name: 'Masukkan Nama Siswa' }).fill(nama);

  await page.locator('select[name="jenis_kelamin"]').selectOption('P');
  await page.locator('input[name="tanggal_masuk"]').fill('2026-04-01');
  await page.locator('select[name="kelas_id"]').selectOption('1');
  await page.locator('select[name="status_siswa_id"]').selectOption('1');

  await page.getByRole('button', { name: /Simpan/i }).click();

  await expect(page.locator('text=Data Siswa berhasil')).toBeVisible();

  // SEARCH
  await page.getByRole('searchbox', { name: 'Cari:' }).fill(nama);

  const row = page.getByRole('row', { name: new RegExp(nama, 'i') });
  await expect(row).toBeVisible();

  // EDIT
  await row.getByRole('link', { name: /Edit/i }).click();

  await page.getByRole('textbox', { name: 'Masukkan NIS' }).fill(nis + '1');
  await page.getByRole('textbox', { name: 'Masukkan Nama Siswa' }).fill(nama + ' Edited');

  await page.locator('select[name="jenis_kelamin"]').selectOption('L');
  await page.locator('input[name="tanggal_masuk"]').fill('2026-08-01');
  await page.locator('select[name="kelas_id"]').selectOption('2');
  await page.locator('select[name="status_siswa_id"]').selectOption('2');

  await page.getByRole('button', { name: /Simpan Perubahan/i }).click();

  await expect(page.locator('text=Data Siswa berhasil diupdate')).toBeVisible();

  // DELETE
  await page.getByRole('searchbox', { name: 'Cari:' }).fill(nama);

  const rowDelete = page.getByRole('row', { name: new RegExp(nama, 'i') });
  await rowDelete.getByRole('button', { name: /Hapus/i }).click();

  await page.getByRole('button', { name: /Ya, Hapus/i }).click();

  await expect(page.locator('text=Data Siswa berhasil dihapus')).toBeVisible();
});