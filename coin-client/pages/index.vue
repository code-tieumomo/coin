<script setup lang="ts">
import { Alert, AlertDescription, AlertTitle } from "~/components/ui/alert";
import { Terminal } from "lucide-vue-next";
import { Card, CardContent, CardHeader } from "~/components/ui/card";
import { useClipboard } from "@vueuse/core";

definePageMeta({
  middleware: ["auth"]
});

const authStore = useAuthStore();
const source = ref(authStore.user?.token);
const { text, copy, copied, isSupported } = useClipboard({ source });
</script>

<template>
  <div class="container mx-auto">
    <Alert>
      <Terminal class="h-4 w-4"/>
      <AlertTitle>Your token</AlertTitle>
      <AlertDescription>
        <div class="flex items-center gap-4">
          <span>{{ source }}</span>
          <div v-if="isSupported">
            <button @click="copy(source)"
                    class="flex items-center justify-center rounded-sm border border-gray-300 p-1 text-gray-400">
              <Icon v-if="!copied" name="mdi:clipboard-outline" class="h-4 w-4"/>
              <div v-else class="flex items-center gap-2 text-xs">
                <Icon name="mdi:clipboard-check-outline" class="h-4 w-4"/>
                Copied!
              </div>
            </button>
          </div>
          <p v-else>
            Your browser does not support Clipboard API
          </p>
        </div>
      </AlertDescription>
    </Alert>

    <Card class="mt-8">
      <CardHeader class="font-semibold">
        Online ({{ authStore.online?.length }})
      </CardHeader>
      <CardContent>
        <div class="flex w-full flex-wrap gap-8">
          <div class="flex items-center gap-2" v-for="user in authStore.online">
            <span class="text-sm">
              {{ user.name }}
            </span>
            <img class="h-8 w-8 rounded-full border-2 border-gray-300"
                 :src="`https://ui-avatars.com/api/?name=${ user.name }`" alt="">
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<style scoped>

</style>
