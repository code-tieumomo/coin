<script setup lang="ts">
import { Alert, AlertDescription, AlertTitle } from "~/components/ui/alert";
import { Terminal } from "lucide-vue-next";
import { Card, CardContent, CardHeader } from "~/components/ui/card";
import TimeTracker from "~/components/TimeTracker.vue";

definePageMeta({
  middleware: ["auth"]
});

const authStore = useAuthStore();
</script>

<template>
  <div class="container mx-auto">
    <Alert>
      <Terminal class="h-4 w-4"/>
      <AlertTitle>Your token</AlertTitle>
      <AlertDescription>
        {{ authStore.user?.token }}
      </AlertDescription>
    </Alert>

    <Card class="mt-8">
      <CardHeader class="font-semibold">
        Online ({{ authStore.online?.length }})
      </CardHeader>
      <CardContent>
        <div class="w-full flex gap-8 flex-wrap">
          <div class="flex items-center gap-2" v-for="user in authStore.online">
            <span class="text-sm">
              {{ user.name }}
            </span>
            <img class="w-8 h-8 rounded-full border-2 border-gray-300"
                 :src="`https://ui-avatars.com/api/?name=${ user.name }`" alt="">
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<style scoped>

</style>
