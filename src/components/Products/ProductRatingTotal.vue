<template>
  <div v-if="rating" class="rating">
    <div class="rating__header">
      <h2>{{+rating.average}}</h2>
      <IconStar />
      ({{rating.count}})
    </div>
    <div class="rating__stars">
      <div class="stars__star" v-for="n in stars" :key="n">
        <span class="star__count">({{rating['rate_' + n]}})</span>
        <IconStar v-for="m in n" :key="m" />
        <IconStar v-for="m in (5 - n)" :key="m" color="#DDD" />
        <div class="percent-bar">
          <div class="bar__bg"></div>
          <div class="bar__filled" :style="{'width': getRateBarWidth(rating['rate_' + n], rating.count)}"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {onMounted, ref, watch} from "vue";
import {getProductTotalRating} from "services/products.service";
import IconStar from "components/Icons/IconStar.vue";

const props = defineProps<{
  code: number,
  feedback: number,
}>();
const rating = ref(null);
const stars = ref([5, 4, 3, 2, 1]);

onMounted(async () => {
  await fetchTotalRating();
});

async function fetchTotalRating() {
  rating.value = await getProductTotalRating(+props.code);
}

watch(
    () => props.feedback,
    async () => {
      await fetchTotalRating();
    }
);

function getRateBarWidth(rateCount: number, totalCount: number): string {
  return ((rateCount / totalCount) * 100) + "%";
}
</script>

<style scoped>
.rating {
  background: var(--background-primary);
  border-radius: 2px;
  padding: 20px 35px;
}

.rating__header {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.rating__header svg {
  width: 25px;
  height: 30px;
  margin-right: 10px;
}

h2 {
  font-size: 26px;
  color: var(--color-primary);
}

.star__count {
  display: block;
  width: 25px;
}

.stars__star {
  display: flex;
  align-items: center;
  position: relative;
  margin: 5px 0;
}

.percent-bar {
  position: relative;
  width: 70%;
  height: 8px;
  margin-left: 10px;
}

.bar__bg,
.bar__filled {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 5px;
  transition: .71s;
}

.bar__bg {
  background: #dfdfdf;
}

.bar__filled {
  background: var(--color-primary);
}
</style>