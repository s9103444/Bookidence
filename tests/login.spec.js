import { test, expect } from '@playwright/test';

test('登入頁的表單元件都有顯示', async ({ page }) => {
  await page.goto('login');
  await expect(page.getByRole('button', { name: '登入' })).toBeVisible();
  await expect(page.locator('#email')).toBeVisible();
  await expect(page.locator('#pwd')).toBeVisible();
});

test('email、密碼沒填時不能送出', async ({ page }) => {
  await page.goto('login');
  await page.getByRole('button', { name: '登入' }).click();
  // handleLogin 裡沒填資料會直接 return，網址不會變
  await expect(page).toHaveURL(/\/login$/);
});

test('可以切換密碼顯示/隱藏', async ({ page }) => {
  await page.goto('login');
  const pwdInput = page.locator('#pwd');
  await pwdInput.fill('test1234');
  await expect(pwdInput).toHaveAttribute('type', 'password');
  await page.locator('.auth-card__input-toggle').click();
  await expect(pwdInput).toHaveAttribute('type', 'text');
});
