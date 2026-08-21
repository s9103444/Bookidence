import { test, expect } from '@playwright/test';

test('測試bookroom功能', async ({ page }) => {
    await page.goto('front/study');
    //確認panel一開始是隱藏起來的
    await expect(page.locator('.study-stage-setting-panel-inner')).not.toBeVisible();
    //確認右上按鈕初始有顯示並在點擊後有使panel出現
    await expect(page.locator('.study-stage-setting-btn')).toBeVisible();
    await page.locator('.study-stage-setting-btn').click();
    await expect(page.locator('.study-stage-setting-panel-inner')).toBeVisible();
    //確認點分頁按鈕，切換到『書籍專區』
    const bookTab = page.locator('.study-stage-setting-panel-tabs button',{ hasText:'書籍專區' });
    await bookTab.click();
    await expect(bookTab).toHaveClass(/tabActive/);
    //確認書籍專區點擊心得草稿區，切換到『心得草稿區』，並且來回切換
    const scriptBtn = page.locator('.btns button',{hasText:'心得草稿區'});
    const scriptBk = page.locator('.script-layout .btn',{hasText:'心得草稿區'});
    await scriptBtn.click();
    await expect(page.locator('.script-layout')).toBeVisible();
    await scriptBk.click();
    await expect(page.locator('.script-layout')).not.toBeVisible();
    await expect(bookTab).toHaveClass(/tabActive/);
    //確認書籍專區點擊新增藏書，切換到『新增藏書』，並且來回切換
    const addBook = page.locator('.btns button',{hasText:'新增藏書'});
    const addBookBk = page.locator('.add-layout .btn',{hasText:'新增藏書'});
    await addBook.click();
    await expect(page.locator('.add-layout')).toBeVisible();
    await addBookBk.click();
    await expect(page.locator('.add-layout')).not.toBeVisible();
    await expect(bookTab).toHaveClass(/tabActive/);
    //確認點分頁按鈕，切換到『個人資訊』
    const profileTab = page.locator('.study-stage-setting-panel-tabs button',{ hasText:'個人資訊' });
    await profileTab.click();
    await expect(profileTab).toHaveClass(/tabActive/);
    //確認點分頁按鈕，切換到『個人外觀』
    const appearaceTab = page.locator('.study-stage-setting-panel-tabs button',{ hasText:'個人外觀' });
    await appearaceTab.click();
    await expect(appearaceTab).toHaveClass(/tabActive/);
    //確認點分頁按鈕，切換到『撰寫心得』
    const writeTab = page.locator('.study-stage-setting-panel-tabs button',{ hasText:'撰寫心得' });
    await writeTab.click();
    await expect(writeTab).toHaveClass(/tabActive/);
});