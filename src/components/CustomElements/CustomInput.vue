<template>
  <label>
    <span>{{label}}</span>
    <input
        :type="type"
        :placeholder="placeholder"
        :name="name"
        :required="required"
        :value="modelValue"
        @input="emitValue"
    >
  </label>
</template>

<script setup lang="ts">
import {defineProps} from "vue";

const emit = defineEmits(['update:modelValue'])

const props = defineProps({
  type: {
    type: String,
    default: "text",
  },
  placeholder: {
    type: String,
    default: "",
  },
  name: {
    type: String,
    default: "",
  },
  modelValue: {
    type: String,
  },
  label: {
    type: String,
    default: "",
  },
  required: {
    type: Boolean,
    default: false,
  },
  modelModifiers: { default: () => ({}) }
});

function emitValue(e) {
  let value = e.target.value;
  if (props.modelModifiers.trim) {
    value = value.trim();
  }
  emit('update:modelValue', value);
}
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