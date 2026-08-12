<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.card {
  padding: 12px;
  display: flex;
  gap: 24px;
  align-items: center;
  position: relative;
  cursor: pointer;

  &::before {
    content: "";
    display: block;
    position: absolute;
    left: 0;
    bottom: -2px;
    height: 1px;
    width: 100%;
    background-color: $neutral-300;
  }
}

.img-cover {
  padding: 4px;
  width: 80px;
  min-width: 80px;
  aspect-ratio: unquote($book-cover-ratio);
  overflow: hidden;

  & img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.content {
  display: flex;
  flex: 1;
  flex-direction: column;
  justify-content: space-between;
  gap: $spacing-xs;
}

.title {
  display: block;
  color: $primary;
  font-weight: $heading-weight;
  font-size: $p-md-size;
}

hr {
  border: 0.5px solid $neutral-400;
  margin-block:6px;
}

.info {
  color: $neutral-500;
  display: flex;
  gap: 10px;

  font-weight: $text-weight;
  font-size: $label-xs-size;
}

.separator::after {
  content: "";
  display: inline-block;
  height: 14px;
  width: 1px;
  background-color: $primary;
  margin-left: 10px;
  vertical-align: middle;
}

@media (max-width: 960px) {
  .card {
    width: 100%;
  }
  .content{
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
  }
  .separator::after {
    display: none;
  }
  .info {
    flex-direction: column;
    gap: 0;
  }
}
</style>

<template>
  <div
    class="card"
    @click="$router.push({ name: 'book-detail', params: { id: book.id } })"
  >
    <div class="img-cover">
      <img :src="book.cover" :alt="book.title" />
    </div>
    <div class="content">
      <div class="infos">
        <div>
          <span class="title">{{ book.title }}</span>
        </div>
        <hr />
        <div class="info">
          <span class="separator">{{ book.author }}</span>
          <span class="separator">{{ book.category }}</span>
          <span class="separator">{{ book.publisher }}</span>
          <span>{{ book.publishDate }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: { book: Object },
};
</script>
