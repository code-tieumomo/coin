<script setup lang="ts">
import { Toaster } from "@/components/ui/sonner";

useHead({
  titleTemplate: (titleChunk) => {
    return titleChunk ? `${ titleChunk } - Intern` : "Intern";
  },
  link: [
    {
      rel: "icon",
      type: "image/png",
      href: "https://img.icons8.com/cotton/64/graduation-cap--v2.png"
    }
  ]
});
</script>

<template>
  <div>
    <NuxtLayout>
      <NuxtPage/>
    </NuxtLayout>
    <Toaster position="top-right" rich-colors :duration="5000"/>
  </div>
</template>

<style>
.page-enter-active {
  transition: all 0.3s ease-out;
}

.page-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}

.page-enter-from,
.page-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
</style>
