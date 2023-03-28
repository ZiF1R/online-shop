<template>
  <div class="select" @mouseleave="showOptions = false">
    <span class="selected-value" @mouseenter="showOptions = true">
      {{ selected.name }}
    </span>
    <div class="options" v-if="showOptions">
      <span
          @click.left="selectOption(option)"
          v-for="option in options"
          :key="option.value"
          :class="{ option__active: option === selected }"
      >
        {{ option.name }}
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import {defineProps, reactive, ref} from "vue";
import type {SelectOption} from "types/main.types";

const props = defineProps<{
  name?: string,
  activeItem?: SelectOption,
  options: Array<SelectOption>
}>();
const emit = defineEmits(['onSelect'])

let selected = reactive(props.activeItem || props.options[0]);
const showOptions = ref(false);

function selectOption(option: SelectOption) {
  selected = option;
  showOptions.value = false;
  emit("onSelect", selected);
}
</script>

<style scoped lang="scss">
.select {
  margin-left: 8px;
  position: relative;

  user-select: none;
  z-index: 10;
}

.selected-value {
  padding-bottom: 2px;
  color: var(--color-primary);
  font-size: 1em;
  border: none;
  outline: none;
  border-bottom: 1px dashed var(--color-primary);
  cursor: pointer;
}

.options {
  display: flex;
  flex-direction: column;
  position: absolute;

  right: 0;
  top: 0;
  transform: translateY(1.5em);

  box-shadow: var(--shadow);
  padding: 10px 0;
  z-index: 10;
  background: #ffffff;

  span {
    padding: 10px 15px;
    cursor: pointer;

    &:hover {
       background: rgba(255, 177, 71, 0.1);
    }
  }

  .option__active {
    background: rgba(255, 177, 71, 0.1);
    color: var(--color-primary);
  }
}
</style>