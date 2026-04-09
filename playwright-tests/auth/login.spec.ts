import { test, expect } from '@playwright/test';

test('test', async ({ page }) => {
  await page.goto('http://epiket.test/login');
  await page.getByRole('textbox', { name: 'Username' }).click();
  await page.getByRole('textbox', { name: 'Username' }).fill('pretty');
  await page.getByRole('textbox', { name: 'Password' }).click();
  await page.getByRole('textbox', { name: 'Password' }).fill('testing');
  await page.getByRole('button', { name: 'Masuk' }).click();

  await expect(page).toHaveURL('http://epiket.test/admin/dashboard');
  await expect(
    page.getByRole('heading', { name: 'SISTEM INFORMASI PIKET SMAN 1' })
  ).toBeVisible();
});