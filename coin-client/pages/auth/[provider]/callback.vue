<script setup lang="ts">
definePageMeta({
  layout: false
});

useHead({
  title: "Verifying"
});

const { $auth } = useNuxtApp();
const route = useRoute();
const router = useRouter();

let provider = route.params.provider as string;
provider = provider.charAt(0).toUpperCase() + provider.slice(1);

onMounted(async () => {
  if (route.query.code) {
    const success = await $auth.handleCallback(route.query.code as string);
    if (success) {
      await router.push("/");
    } else {
      await router.push("/login");
    }
  }
});
</script>

<template>
  <main class="min-h-screen flex flex-col gap-4 items-center justify-center">
    <Icon name="svg-spinners:270-ring-with-bg" class="w-8 h-8 text-gray-700"></Icon>
    <p>
      Fetching data from {{ provider }}.
    </p>
  </main>
</template>

<style scoped>

</style>
