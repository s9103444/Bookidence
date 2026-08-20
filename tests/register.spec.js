import { test, expect } from '@playwright/test';

test('Step1：條款沒全部勾選不能過關', async ({ page }) => {
  await page.goto('front/register');
  const nextBtn = page.locator('.register__next');
  await expect(nextBtn).toBeDisabled();

  const checkboxes = page.locator('input[type="checkbox"]');
  await checkboxes.nth(0).check(); // 我已年滿 13 歲
  await checkboxes.nth(1).check(); // 我同意服務條款
  await checkboxes.nth(2).check(); // 我同意隱私權政策
  await expect(nextBtn).toBeEnabled();
});

test('走完四步驟後會跳回首頁', async ({ page }) => {
  await page.goto('front/register');
  const nextBtn = page.locator('.register__next');

  // Step1：條款
  const checkboxes = page.locator('input[type="checkbox"]');
  await checkboxes.nth(0).check();
  await checkboxes.nth(1).check();
  await checkboxes.nth(2).check();
  await nextBtn.click();

  // Step2：帳號密碼
  await page.getByPlaceholder('請輸入E-mail').fill('test@example.com');
  await page.getByPlaceholder('請設定6位數密碼').fill('test1234');
  await page.getByPlaceholder('確認密碼').fill('test1234');
  await expect(nextBtn).toBeEnabled();
  await nextBtn.click();

  // Step3：閱讀偏好（不強制選，直接過）
  await nextBtn.click();

  // Step4：建立角色
  await expect(nextBtn).toBeDisabled();
  await page.getByPlaceholder('請輸入暱稱(限10個字)').fill('測試角色');
  await expect(nextBtn).toBeEnabled();
  await nextBtn.click();

  // completeRegistration() 目前還沒接後端 API（見程式碼註解），
  // 現在只會直接 push 回首頁，不代表帳號真的寫進資料庫了
  await expect(page).toHaveURL(/\/front\/?$/);
});
