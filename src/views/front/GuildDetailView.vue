<script setup>
  import GuildEventCard from '../../components/front/GuildEventCard.vue'
  import SectionTitle from '../../components/front/SectionTitle.vue'

  // 假資料：同一本書（小王子）的四場讀書會，之後接後端 API 時會換掉。
  // 沒寫 bookName / author / coverImage，讓它們走元件的預設值。
  const events = [
    {
      id: 1,
      eventType: '線下活動',
      eventTime: '2026.10.10 (五) 19:00 - 21:30 (GMT+8)',
      location: '台灣台北市松山區復興北路1號6樓之3-603教室',
      locationNote: '亞細亞大樓六樓-小樹屋共享空間',
      participantCount: 5,
    },
    {
      id: 2,
      eventType: '線上活動',
      eventTime: '2026.10.17 (五) 20:00 - 22:00 (GMT+8)',
      location: 'Google Meet 線上會議室',
      locationNote: '報名後寄送連結',
      participantCount: 12,
    },
    {
      id: 3,
      eventType: '線下活動',
      eventTime: '2026.10.24 (五) 19:00 - 21:00 (GMT+8)',
      location: '台北市信義區松高路11號',
      locationNote: '',
      participantCount: 8,
    },
    {
      id: 4,
      eventType: '線下活動',
      eventTime: '2026.10.31 (五) 19:30 - 21:30 (GMT+8)',
      location: '台北市大安區羅斯福路四段1號',
      locationNote: '台大校友會館 3F',
      participantCount: 20,
    },
  ]
</script>
<template>
  <div class="guild-detail">
    <h1>公會主頁</h1>
    <p>這是前台「公會主頁」頁面，等功能開發時再把內容補上。</p>

    <SectionTitle>即將到來的活動</SectionTitle>

    <div class="guild-detail__events">
      <GuildEventCard
        v-for="event in events"
        :key="event.id"
        v-bind="event"
      ></GuildEventCard>
    </div>
  </div>
</template>
<style lang="scss" scoped>
@use '../../assets/scss/abstracts/variables' as *;

.guild-detail {
  max-width: 1000px;
  margin: 0 auto;
  padding: $spacing-lg;
}

.guild-detail__events {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: $spacing-lg;
}
</style>
