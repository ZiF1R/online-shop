<template>
  <teleport to="#header">
    <div class="container">
      <div class="content">
        <slot></slot>
      </div>
      <div class="overlay" @click="emit('onClose')"></div>
    </div>
  </teleport>
</template>

<script setup lang="ts">
const emit = defineEmits(["onClose"]);
</script>

<style scoped>
.container {
  position: fixed;
  background: #ffffff;
  padding: 40px 0;
  width: 100%;
  z-index: 21;
  top: 200px;
}

.content {
  overflow-y: scroll;
  height: fit-content;
  max-height: 60vh;
}

.overlay {
  position: absolute;
  width: 100%;
  height: 100vh;
  background: #00000050;
  z-index: 20;
  bottom: -100vh;
}
</style>