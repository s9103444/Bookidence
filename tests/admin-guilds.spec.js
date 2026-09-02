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

  // AdminLayout 側邊欄一進後台就會打這支拿「待審數量」紅點，假 token 打到真後端會被判 401
  // 進而觸發全域的登出攔截器，把整頁踢回登入頁——所以跟 admin_me.php 一樣要攔截掉
  await page.route('**/admin_applications.php*', (route) =>
    route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({ success: true, data: [], total: 0, counts: { 待處理: 0, 已核准: 0, 已駁回: 0 } }),
    }),
  );

  // 側邊欄「檢舉管理」的紅點數字，同樣的坑，同樣要攔截
  await page.route('**/admin_reports.php*', (route) =>
    route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({
        success: true,
        data: [],
        total: 0,
        perPage: 10,
        counts: { 尚未處理: 0, 檢舉成立: 0, 檢舉不成立: 0 },
        typeCounts: { 心得: 0, 留言: 0 },
      }),
    }),
  );
});

// 公會列表／詳情頁現在打真的 API 了，這裡用假資料模擬
// admin_guilds.php / admin_guild_detail.php 等端點的回傳格式（snake_case，跟後端一致）
function createGuildFixtures() {
  const guilds = [
    {
      guild_id: 1,
      guild_code: 'G-0027',
      guild_name: '週三夜讀圈',
      founded_at: '2026-03-12',
      member_count: 2,
      guild_status: '正常',
      leader_nickname: '晨讀時光',
    },
    {
      guild_id: 2,
      guild_code: 'G-0041',
      guild_name: '口袋書局',
      founded_at: '2026-05-02',
      member_count: 1,
      guild_status: '停權',
      leader_nickname: '會員_0912',
    },
  ];

  const details = {
    1: {
      guild: {
        guild_id: 1,
        guild_code: 'G-0027',
        guild_name: '週三夜讀圈',
        intro: '每週三晚間視訊共讀',
        founded_at: '2026-03-12',
        member_count: 2,
        guild_status: '正常',
        current_book_title: '北歐時間：世界第一幸福國度教會我的事',
        completed_books_count: 3,
        suspend_log: null,
        delete_log: null,
      },
      members: [
        {
          user_id: 102,
          permission_level: '會長',
          joined_at: '2026-03-12',
          member_code: 'MKD00000102',
          nickname: '晨讀時光',
          message_count: 142,
          flagged: false,
        },
        {
          user_id: 244,
          permission_level: '副會長',
          joined_at: '2026-03-12',
          member_code: 'MKD00000244',
          nickname: '會員_0244',
          message_count: 118,
          flagged: false,
        },
      ],
      events: [
        {
          event_id: 24,
          event_type: '線上(Online)',
          event_date: '2026-07-13',
          event_time: '20:00:00',
          description: '第 8 章討論',
          max_participants: 10,
          registered_count: 8,
        },
      ],
      messages: [
        {
          report_id: 192,
          reason: '人身攻擊',
          reported_at: '2026-07-13 21:41',
          message_id: 302,
          posted_at: '2026-07-13 21:40',
          content: '這本書根本是垃圾，作者是〇〇……',
          author_user_id: 421,
          author_nickname: '會員_0421',
          author_permission_level: '一般',
        },
      ],
    },
    2: {
      guild: {
        guild_id: 2,
        guild_code: 'G-0041',
        guild_name: '口袋書局',
        intro: '不定期線上快閃讀書會，主題常常換',
        founded_at: '2026-05-02',
        member_count: 1,
        guild_status: '停權',
        current_book_title: null,
        completed_books_count: 1,
        suspend_log: {
          staff_account: 'shuyun',
          staff_name: '書芸',
          reason: '多名成員檢舉群組內大量垃圾廣告訊息，經查證屬實，予以公會停權。',
          created_at: '2026-07-18 11:20',
        },
        delete_log: null,
      },
      members: [
        {
          user_id: 912,
          permission_level: '會長',
          joined_at: '2026-05-02',
          member_code: 'MKD00000912',
          nickname: '會員_0912',
          message_count: 88,
          flagged: true,
        },
      ],
      events: [],
      messages: [],
    },
  };

  return { guilds, details };
}

// 幾支公會相關 API 一次攔截好；有寫入的端點會回頭改 fixtures，讓後續的重新查詢看得到變化，
// 比照真實後端「動作 → 重新整批拉資料」的行為
async function mockGuildApis(page, fixtures) {
  await page.route('**/admin_guilds.php*', async (route) => {
    const url = new URL(route.request().url());
    const status = url.searchParams.get('status') ?? '';
    const keyword = (url.searchParams.get('keyword') ?? '').trim();

    let rows = fixtures.guilds;
    if (status !== '') rows = rows.filter((g) => g.guild_status === status);
    if (keyword !== '') {
      rows = rows.filter((g) => `${g.guild_name}${g.guild_code}${g.leader_nickname}`.includes(keyword));
    }

    const statusCounts = { 正常: 0, 停權: 0, 已解散: 0 };
    for (const g of fixtures.guilds) statusCounts[g.guild_status] += 1;

    await route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({ success: true, data: rows, total: rows.length, perPage: 10, statusCounts }),
    });
  });

  await page.route('**/admin_guild_detail.php*', async (route) => {
    const url = new URL(route.request().url());
    const detail = fixtures.details[url.searchParams.get('id')];

    if (!detail) {
      await route.fulfill({
        status: 404,
        contentType: 'application/json',
        body: JSON.stringify({ success: false, message: '找不到這個公會。' }),
      });
      return;
    }

    await route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({ success: true, ...detail }),
    });
  });

  await page.route('**/admin_guild_update.php', async (route) => {
    const body = route.request().postDataJSON();
    const detail = fixtures.details[body.guild_id];
    detail.guild.guild_name = body.guild_name;
    detail.guild.intro = body.intro;
    await route.fulfill({ contentType: 'application/json', body: JSON.stringify({ success: true }) });
  });

  await page.route('**/admin_guild_assign.php', async (route) => {
    const body = route.request().postDataJSON();
    const detail = fixtures.details[body.guild_id];
    detail.members.forEach((member) => {
      if (member.user_id === body.leader_user_id) member.permission_level = '會長';
      else if (body.deputy_user_id && member.user_id === body.deputy_user_id) member.permission_level = '副會長';
      else member.permission_level = '一般';
    });
    await route.fulfill({ contentType: 'application/json', body: JSON.stringify({ success: true }) });
  });

  await page.route('**/admin_guild_suspend.php', async (route) => {
    const body = route.request().postDataJSON();
    const detail = fixtures.details[body.guild_id];
    detail.guild.guild_status = '停權';
    detail.guild.suspend_log = {
      staff_account: 'tester',
      staff_name: '測試管理員',
      reason: body.reason,
      created_at: '2026-09-02 12:00',
    };
    await route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({ success: true, staff_name: '測試管理員' }),
    });
  });

  await page.route('**/admin_guild_restore.php', async (route) => {
    const body = route.request().postDataJSON();
    const detail = fixtures.details[body.guild_id];
    detail.guild.guild_status = '正常';
    detail.guild.suspend_log = null;
    await route.fulfill({ contentType: 'application/json', body: JSON.stringify({ success: true }) });
  });

  await page.route('**/admin_guild_delete.php', async (route) => {
    const body = route.request().postDataJSON();
    const detail = fixtures.details[body.guild_id];
    detail.guild.guild_status = '已解散';
    detail.guild.delete_log = {
      staff_account: 'tester',
      staff_name: '測試管理員',
      reason: body.reason,
      created_at: '2026-09-02 12:00',
    };
    await route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({ success: true, staff_name: '測試管理員' }),
    });
  });

  await page.route('**/admin_guild_event_registrations.php*', async (route) => {
    await route.fulfill({ contentType: 'application/json', body: JSON.stringify({ success: true, data: [] }) });
  });
}

test.describe('公會列表 GuildsView', () => {
  test('顯示公會列表，並可以用狀態篩選', async ({ page }) => {
    await mockGuildApis(page, createGuildFixtures());
    await page.goto('admin/guilds');

    await expect(page.getByRole('heading', { name: '公會管理' })).toBeVisible();
    await expect(page.getByText('週三夜讀圈')).toBeVisible();
    await expect(page.getByText('口袋書局')).toBeVisible();

    await page.getByRole('button', { name: /已停權/ }).click();
    await expect(page.getByText('口袋書局')).toBeVisible();
    await expect(page.getByText('週三夜讀圈')).toHaveCount(0);
  });

  test('可以用關鍵字搜尋公會', async ({ page }) => {
    await mockGuildApis(page, createGuildFixtures());
    await page.goto('admin/guilds');

    await page.getByPlaceholder(/搜尋公會名稱/).fill('口袋');
    await expect(page.getByText('口袋書局')).toBeVisible();
    await expect(page.getByText('週三夜讀圈')).toHaveCount(0);
  });

  test('點檢視公會會進入詳情頁', async ({ page }) => {
    await mockGuildApis(page, createGuildFixtures());
    await page.goto('admin/guilds');

    await page.getByRole('link', { name: '檢視公會' }).first().click();
    await expect(page).toHaveURL(/admin\/guilds\/\d+$/);
  });
});

test.describe('公會詳情 GuildDetailView', () => {
  test('顯示公會摘要，分頁可以切換', async ({ page }) => {
    await mockGuildApis(page, createGuildFixtures());
    await page.goto('admin/guilds/1');

    await expect(page.locator('.guild__name')).toHaveText('週三夜讀圈');
    await expect(page.locator('.guild__stat', { hasText: '成員' }).first()).toContainText('2');

    await page.getByRole('button', { name: '活動場次' }).click();
    await expect(page.getByText('第 8 章討論')).toBeVisible();

    await page.getByRole('button', { name: '留言檢舉紀錄' }).click();
    await expect(page.getByText('前往檢舉案 192')).toBeVisible();
  });

  test('編輯公會資料會即時反映在畫面上', async ({ page }) => {
    await mockGuildApis(page, createGuildFixtures());
    await page.goto('admin/guilds/1');

    await page.getByRole('button', { name: '編輯公會資料' }).click();
    await page.getByLabel('公會名稱', { exact: true }).fill('週三夜讀圈（測試改名）');
    await page.getByRole('button', { name: '儲存修改' }).click();

    await expect(page.locator('.guild__name')).toHaveText('週三夜讀圈（測試改名）');
  });

  test('指派會長／副會長後成員角色會更新', async ({ page }) => {
    await mockGuildApis(page, createGuildFixtures());
    await page.goto('admin/guilds/1');

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
    await mockGuildApis(page, createGuildFixtures());
    await page.goto('admin/guilds/1');

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
    await mockGuildApis(page, createGuildFixtures());
    await page.goto('admin/guilds/9999');

    await expect(page.getByRole('heading', { name: '找不到這個公會' })).toBeVisible();
    await page.getByRole('link', { name: '回公會列表' }).click();
    await expect(page).toHaveURL(/admin\/guilds$/);
  });
});
