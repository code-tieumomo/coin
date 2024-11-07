<script setup lang="ts">
import { useActivityTracking } from "~/composables/useActivityTracking";

const {
  isActive,
  formattedTotalTime
} = useActivityTracking();
</script>

<template>
  <div class="time-display flex items-center text-xs gap-2 border rounded-full px-2 py-0.5">
    <div class="w-2 h-2 rounded-full" :class="isActive ? 'bg-green-500 animate-pulse' : 'bg-red-500'"></div>
    <div class="value">{{ formattedTotalTime }}</div>
  </div>
</template>

<style scoped>

</style>
