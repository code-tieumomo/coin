<script setup lang="ts">
import { Button } from "@/components/ui/button";

definePageMeta({
  layout: false
});

useHead({
  title: "Login"
});

const { $auth } = useNuxtApp();

const handleLogin = async () => {
  await $auth.login();
};
</script>

<template>
  <main class="min-h-screen flex">
    <div class="w-1/2 bg-neutral-900 p-12 text-white flex flex-col justify-between">
      <h1 class="flex items-center gap-2">
        <Icon name="hugeicons:bitcoin-cpu" class="w-6 h-6"/>
        Intern
      </h1>

      <p class="text-sm w-5/6">
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad alias aliquam amet animi architecto consequuntur
        eligendi eos, eum ex iste numquam odio officia quis, recusandae rem sapiente soluta tempora voluptate.
      </p>
    </div>
    <div class="w-1/2 flex flex-col items-center justify-center">
      <h2 class="text-2xl font-bold mb-4 w-fit">Login</h2>
      <p class="text-sm">
        Login to our system using your Google account.
      </p>
      <Button class="my-8" @click="handleLogin">
        <Icon name="jam:google" class="w-6 h-6 mr-2"/>
        Continue with Google
      </Button>
      <p class="px-12 text-center text-xs text-gray-500 max-w-xs">
        By clicking continue, you agree to our
        <a
          href="/terms"
          class="underline underline-offset-4 hover:text-gray-700"
        >
          Terms of Service
        </a>
        and
        <a
          href="/privacy"
          class="underline underline-offset-4 hover:text-gray-700"
        >
          Privacy Policy
        </a>
        .
      </p>
    </div>
  </main>
</template>

<style scoped>

</style>
