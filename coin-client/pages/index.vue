<script setup lang="ts">
import { Alert, AlertDescription, AlertTitle } from "~/components/ui/alert";
import { Terminal } from "lucide-vue-next";

definePageMeta({
  middleware: ["auth"]
});

const { $auth } = useNuxtApp();
const authStore = useAuthStore();

const handleLogout = async () => {
  await $auth.logout();
};
</script>

<template>
  <div class="container mx-auto">
    <Alert>
      <Terminal class="h-4 w-4"/>
      <AlertTitle>Your token</AlertTitle>
      <AlertDescription>
        {{ authStore.user.token }}
      </AlertDescription>
    </Alert>
  </div>
</template>

<style scoped>

</style>
