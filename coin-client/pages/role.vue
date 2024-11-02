<script setup lang="ts">
import { toast } from "vue-sonner";

useHead({
  title: "Select Role"
});

definePageMeta({
  middleware: [
    function () {
      const authStore = useAuthStore();
      if (!authStore.user) return navigateTo("/login");
      if (authStore.user?.role) return navigateTo("/");
      return;
    }
  ],
  layout: "default"
});

const { $api } = useNuxtApp();
const runtimeConfig = useRuntimeConfig();
const authStore = useAuthStore();

const isSubmitting = ref<boolean>(false);
const role = ref<string | null>(null);

const submit = async () => {
  isSubmitting.value = true;
  try {
    if (!role.value) {
      toast.error("You must select a role to continue");
      return;
    }
    const response = await $api.post("/auth/role", { role: role.value });
    if (response.status === 200) {
      authStore.setRoleAndPermission(response.data.role, response.data.permissions);
      await navigateTo("/");
    }
  } catch (error) {
    console.error(error);
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <main class="flex items-center justify-center flex-col">
    <h3 class="mb-8 text-lg font-semibold text-gray-900">
      You must select a role to continue
    </h3>
    <ul class="grid w-full max-w-xl gap-6 md:grid-cols-2">
      <li>
        <input type="radio" id="provider" v-model="role" value="provider" class="hidden peer" required/>
        <label for="provider"
               class="inline-flex gap-4 items-center justify-between w-full p-5 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-gray-900 peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100">
          <div class="block">
            <div class="w-full text-lg font-semibold">Provider</div>
            <div class="w-full text-xs">
              Lorem ipsum dolor sit amet, consectetur adipisicing elit.
            </div>
          </div>
          <Icon name="solar:box-bold" class="w-8 h-8 shrink-0"></Icon>
        </label>
      </li>
      <li>
        <input type="radio" id="miner" v-model="role" value="miner" class="hidden peer">
        <label for="miner"
               class="inline-flex gap-4 items-center justify-between w-full p-5 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-gray-900 peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100">
          <div class="block">
            <div class="w-full text-lg font-semibold">Miner</div>
            <div class="w-full text-xs">
              Lorem ipsum dolor sit amet, consectetur adipisicing elit.
            </div>
          </div>
          <Icon name="mdi:mine" class="w-8 h-8 shrink-0"></Icon>
        </label>
      </li>
    </ul>
    <Button class="mt-4" :disabled="isSubmitting" @click="submit">
      <Icon v-if="isSubmitting" name="svg-spinners:90-ring-with-bg" class="w-5 h-5 mr-2"></Icon>
      Continue
    </Button>
  </main>
</template>

<style scoped>

</style>
