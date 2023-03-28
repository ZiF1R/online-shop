<template>
  <div class="counter">
    <button
        class="counter__controller"
        :class="{'controller_disabled': !canDecrease}"
        @click="decrease"
        :disabled="!canDecrease"
    >
      <IconMinus />
    </button>
    <button>{{localCount}}</button>
    <button class="counter__controller" @click="increase">
      <IconPlus />
    </button>
  </div>
</template>

<script setup lang="ts">
import {computed, ref} from "vue";
import IconMinus from "components/Icons/IconMinus.vue";
import IconPlus from "components/Icons/IconPlus.vue";

const emit = defineEmits(["onChange"]);
const props = defineProps({
  count: {
    type: Number,
    default: 1,
  }
});
const localCount = ref<number>(props.count);
const canDecrease = computed(() => localCount.value > 1);

function increase() {
  localCount.value++;
  emit("onChange", localCount.value);
}

function decrease() {
  if (canDecrease.value) {
    localCount.value--;
    emit("onChange", localCount.value);
  }
}
</script>

<style scoped>
.counter {
  display: flex;
  align-items: center;
}

button {
  background: none;
  border: 1px solid #ccc;
  color: var(--color-text);
  padding: 10px 20px;
  height: 40px;
  cursor: default;
}

.counter__controller svg {
  width: 20px;
  height: 20px;
}

.counter__controller {
  padding: 10px;
  cursor: pointer;
}

.counter__controller:first-child {
  border-right: none;
}

.counter__controller:last-child {
  border-left: none;
}

.controller_disabled {
  background: #f3f3f3;
}
</style>