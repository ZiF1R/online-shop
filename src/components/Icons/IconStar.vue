<template>
  <svg
    width="18"
    height="18"
    viewBox="0 0 18 18"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <g clip-path="url(#clip0_76_153)">
      <path
        d="M9.75843 2.54691L11.0264 5.03381C11.1994 5.38003 11.656 5.70982 12.0395 5.77065L14.3051 6.12877C15.754 6.35854 16.1027 7.40562 15.0682 8.4494L13.323 10.2223C13.0274 10.5225 12.8688 11.0984 12.9642 11.509L13.485 13.6826C13.8957 15.4031 12.986 16.077 11.4539 15.1882L9.32325 13.9494C8.93845 13.7255 8.30717 13.7304 7.91885 13.9605L5.80793 15.2326C4.29712 16.1454 3.36974 15.4788 3.75344 13.7592L4.2399 11.5777C4.32887 11.1656 4.16121 10.5923 3.86096 10.2968L2.08807 8.55154C1.04435 7.52412 1.36945 6.47174 2.81452 6.21919L5.07422 5.82546C5.44964 5.75866 5.90097 5.42172 6.06846 5.07283L7.29718 2.56628C7.96744 1.20617 9.07394 1.19746 9.75843 2.54691Z"
        :fill="color"
        :stroke="color"
        stroke-linecap="round"
        stroke-linejoin="round"
      />
    </g>
    <defs>
      <clipPath id="clip0_76_153">
        <rect
          width="17.0236"
          height="17.0236"
          fill="white"
          transform="translate(0 0.133911) rotate(-0.450866)"
        />
      </clipPath>
    </defs>
  </svg>
</template>

<script setup lang="ts">
import {defineProps} from "vue";

const props = defineProps({
  color: {
    default: '#FFA726',
    type: String,
  }
});
</script>