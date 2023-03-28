<template>
  <teleport to="#app">
    <div class="overlay" @click="closeModal">
      <div class="modal">
        <slot name="header"></slot>
        <div class="content">
          <slot name="content"></slot>
        </div>
        <slot name="buttons"></slot>
      </div>
    </div>
  </teleport>
</template>

<script setup lang="ts">
const emit = defineEmits(["onClose"]);

function closeModal(e) {
  if (e.target.closest(".modal") === null) {
    emit("onClose");
  }
}
</script>

<style scoped>
.overlay {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: #00000050;
  z-index: 100;
}

.modal {
  display: flex;
  flex-direction: column;
  width: 450px;
  background: #fff;
  padding: 15px 30px;
  border-radius: 2px;
}

.content {
  position: relative;
  margin: 20px 0;
}
</style>