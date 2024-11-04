<script setup lang="ts">
import { useActivityTracking } from "~/composables/useActivityTracking";

const {
  isActive,
  formattedTotalTime,
  activeStatus,
  currentSession,
  formatTime
} = useActivityTracking();
</script>

<template>
  <div class="activity-tracker" :class="{ 'is-active': isActive }">
    <div class="status-indicator">
      <div class="status-dot" :class="{ 'active': isActive }"></div>
      <span class="status-text">{{ activeStatus }}</span>
    </div>

    <div class="time-display">
      <div class="label">Today's Active Time</div>
      <div class="value">{{ formattedTotalTime }}</div>
    </div>

    <div v-if="currentSession" class="current-session">
      <div class="label">Current Session</div>
      <div class="value">
        {{ formatTime(currentSession.duration) }}
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
