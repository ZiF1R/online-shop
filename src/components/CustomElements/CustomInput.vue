<template>
  <label>
    <span>{{label}}</span>
    <input
        :type="type || 'text'"
        :placeholder="placeholder"
        :name="name"
        :required="required"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
  </label>
</template>

<script setup lang="ts">
import {defineProps} from "vue";

defineEmits(['update:modelValue'])

const props = defineProps<{
  type?: string,
  placeholder?: string,
  name?: string,
  modelValue?: string,
  label?: string,
  required?: boolean,
}>();
</script>

<style scoped>
label {
  display: flex;
  flex-direction: column;
}

label span {
  margin-left: 5px;
}

input {
  outline: none;
  border: 1px solid #ddd;
  border-radius: 2px;
  padding: 10px;
  margin: 7px 0;
  width: 100%;
}
</style>