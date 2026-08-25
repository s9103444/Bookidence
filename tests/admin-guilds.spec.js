import { test, expect } from '@playwright/test';

// /admin 路由有登入檢查（router/index.js），開發環境沒有真的後端可以登入，
// 所以每個測試開始前塞一個假 token 進 localStorage 繞過檢查，純粹是為了能打開頁面測試畫面。
//
// AdminLayout 掛載時還會打 admin_me.php 驗證這個 token，本機如果剛好有跑真的後端，
// 假 token 會被判定失效、被登出，換頁就會被踢回登入頁——所以順便把這支 API 攔截掉。
test.beforeEach(async ({ page }) => {
  await page.addInitScript(() => {
    localStorage.setItem('admin', JSON.stringify({ token: 'test-token' }));
  });

  await page.route('**/admin_me.php', (route) =>
    route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({ success: true, staff: { staff_account: 'tester', staff_name: '測試管理員' } }),
    }),
  );
});

test.describe('公會列表 GuildsView', () => {
  test('顯示公會列表，並可以用狀態篩選', async ({ page }) => {
    await page.goto('admin/guilds');

    await expect(page.getByRole('heading', { name: '公會管理' })).toBeVisible();
    await expect(page.getByText('週三夜讀圈')).toBeVisible();
    await expect(page.getByText('口袋書局')).toBeVisible();

    await page.getByRole('button', { name: /已停權/ }).click();
    await expect(page.getByText('口袋書局')).toBeVisible();
    await expect(page.getByText('週三夜讀圈')).toHaveCount(0);
  });

  test('可以用關鍵字搜尋公會', async ({ page }) => {
    await page.goto('admin/guilds');

    await page.getByPlaceholder(/搜尋公會名稱/).fill('推理小說');
    await expect(page.getByText('推理小說同好會')).toBeVisible();
    await expect(page.getByText('週三夜讀圈')).toHaveCount(0);
  });

  test('點檢視公會會進入詳情頁', async ({ page }) => {
    await page.goto('admin/guilds');

    await page.getByRole('link', { name: '檢視公會' }).first().click();
    await expect(page).toHaveURL(/admin\/guilds\/G-\d{4}$/);
  });
});

test.describe('公會詳情 GuildDetailView', () => {
  test('顯示公會摘要，分頁可以切換', async ({ page }) => {
    await page.goto('admin/guilds/G-0027');

    await expect(page.locator('.guild__name')).toHaveText('週三夜讀圈');
    await expect(page.locator('.guild__stat', { hasText: '成員' }).first()).toContainText('10');

    await page.getByRole('button', { name: '活動與出席' }).click();
    await expect(page.getByText('第 8 章討論')).toBeVisible();

    await page.getByRole('button', { name: '留言檢舉紀錄' }).click();
    await expect(page.getByText('前往檢舉案 R-0192')).toBeVisible();
  });

  test('編輯公會資料會即時反映在畫面上', async ({ page }) => {
    await page.goto('admin/guilds/G-0027');

    await page.getByRole('button', { name: '編輯公會資料' }).click();
    await page.getByLabel('公會名稱', { exact: true }).fill('週三夜讀圈（測試改名）');
    await page.getByRole('button', { name: '儲存修改' }).click();

    await expect(page.locator('.guild__name')).toHaveText('週三夜讀圈（測試改名）');
  });

  test('指派會長／副會長後成員角色會更新', async ({ page }) => {
    await page.goto('admin/guilds/G-0027');

    await page.getByRole('button', { name: '指派會長／副會長' }).click();
    // 副會長預設帶入的就是「會員_0244」，要先清空，不然新會長跟舊副會長是同一人會被擋下來
    await page.getByLabel('副會長', { exact: false }).selectOption({ label: '不指派' });
    await page.getByLabel('會長', { exact: true }).selectOption({ label: '會員_0244' });
    await page.getByRole('button', { name: '確認指派' }).click();

    const updatedRow = page.locator('tbody tr', { hasText: '會員_0244' });
    await expect(updatedRow.getByText('會長', { exact: true })).toBeVisible();

    const oldLeaderRow = page.locator('tbody tr', { hasText: '晨讀時光' });
    await expect(oldLeaderRow.getByText('成員', { exact: true })).toBeVisible();
  });

  test('停權後可以解除停權', async ({ page }) => {
    await page.goto('admin/guilds/G-0027');

    await page.getByRole('button', { name: '停權違規公會' }).click();
    await page.getByLabel('停權原因', { exact: false }).fill('測試用停權原因');
    await page.getByRole('button', { name: '確認停權' }).click();

    await expect(page.getByText('已停權').first()).toBeVisible();
    await expect(page.getByRole('button', { name: '解除停權' })).toBeVisible();

    await page.getByRole('button', { name: '解除停權' }).click();
    await page.getByRole('button', { name: '確認解除' }).click();

    await expect(page.getByRole('button', { name: '停權違規公會' })).toBeVisible();
  });

  test('找不到的公會編號會顯示提示', async ({ page }) => {
    await page.goto('admin/guilds/G-9999');

    await expect(page.getByRole('heading', { name: '找不到這個公會' })).toBeVisible();
    await page.getByRole('link', { name: '回公會列表' }).click();
    await expect(page).toHaveURL(/admin\/guilds$/);
  });
});
