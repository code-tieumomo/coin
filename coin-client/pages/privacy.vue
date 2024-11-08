<script setup lang="ts">
definePageMeta({
  layout: false
});

const router = useRouter();

const goBack = () => {
  router.go(-1);
};
</script>

<template>
  <main class="container mx-auto py-12">
    <button @click="goBack" class="flex items-center gap-2">
      <Icon name="material-symbols:arrow-left-alt-rounded" class="w-5 h-5 text-gray-700"></Icon>
      Go back
    </button>

    <h1 class="text-2xl font-bold mt-8">
      Privacy Policy
    </h1>

    <p>
      ...
    </p>
  </main>
</template>

<style scoped>

</style>
