import { Page } from '@playwright/test';

export async function login(page: Page) {
  await page.goto('http://epiket.test/login');

  await page.getByRole('textbox', { name: 'Username' }).fill('admin');
  await page.getByRole('textbox', { name: 'Password' }).fill('admin');

  await page.getByRole('button', { name: 'Masuk' }).click();
}