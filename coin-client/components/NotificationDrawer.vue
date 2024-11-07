<script setup lang="ts">
import { onClickOutside } from "@vueuse/core";

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(["close"]);

const isVisible = ref(false);
const isTransitioning = ref(false);

const toggleBackgroundScrolling = (enable: Boolean) => {
  const body = document.querySelector("body");
  if (body) {
    body.style.overflow = enable ? "hidden" : null;
  }
};

const closeDrawer = () => {
  if (!isTransitioning.value) {
    emit("close");
  }
};

const drawerRef = useTemplateRef("drawer");
onClickOutside(drawerRef, () => closeDrawer());

watch(() => props.isOpen, (newVal) => {
  isTransitioning.value = true;

  if (newVal) {
    toggleBackgroundScrolling(true);
    isVisible.value = true;
  } else {
    toggleBackgroundScrolling(false);
    setTimeout(() => (isVisible.value = false), 500);
  }

  setTimeout(() => (isTransitioning.value = false), 500);
});

onMounted(() => {
  isVisible.value = props.isOpen;
});
</script>

<template>
  <div>
    <div class="drawer" :class="{ 'is-open': isOpen, 'is-visible': isVisible }">
      <div
        class="drawer__overlay duration-500"
      ></div>
      <div
        ref="drawer"
        class="drawer__content max-w-96 duration-500 bg-white p-4"
      >
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.drawer {
  visibility: hidden;

  &.is-visible {
    visibility: visible;
  }

  &.is-open {
    .drawer__overlay {
      opacity: 0.5;
    }

    .drawer__content {
      transform: translateX(0);
    }
  }

  &__overlay {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 200;
    opacity: 0;
    transition-property: opacity;
    background-color: #000000;
    user-select: none;
  }

  &__content {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    height: 100%;
    width: 100%;
    z-index: 9999;
    overflow: auto;
    transition-property: transform;
    display: flex;
    flex-direction: column;
    transform: translateX(100%);
    box-shadow: 0 2px 6px #777;
  }
}
</style>
