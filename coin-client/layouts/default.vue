<script setup lang="ts">
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger
} from "@/components/ui/dropdown-menu";
import { Badge } from "@/components/ui/badge";
import { toast } from "vue-sonner";

const authStore = useAuthStore();
const user = authStore.getUser;
const { $auth, $echo } = useNuxtApp();

onMounted(() => {
  $echo.private("private-notification." + user.id)
    .listen("PrivateNotification", (e) => {
      toast[e.type || "info"](e.title, {
        description: e.content
      });
    });
});

const logout = async () => {
  await $auth.logout();
};
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <header class="py-3 border-b shadow-sm">
      <div class="container mx-auto flex items-center gap-4 justify-between">
        <NuxtLink to="/">
          <h1 class="flex items-center gap-2 font-semibold text-lg">
            <Icon name="hugeicons:bitcoin-cpu" class="w-6 h-6"/>
            Coin
          </h1>
        </NuxtLink>

        <div class="flex items-center gap-4">
          <nav class="text-sm">
            <ul class="flex items-center gap-4">
              <li>
                <NuxtLink class="hover:underline text-gray-700 hover:text-foreground" to="/">Home</NuxtLink>
              </li>
              <li>
                <NuxtLink class="hover:underline text-gray-700 hover:text-foreground" to="/subnets">Subnets</NuxtLink>
              </li>
            </ul>
          </nav>
          <span class="text-gray-300">|</span>
          <Badge>{{ user.role?.toUpperCase() || "???" }}</Badge>
          <span class="text-gray-300">|</span>
          <!--<Icon name="hugeicons:notification-03" class="w-5 h-5 text-gray-500"/>-->
          <!--<span class="text-gray-300">|</span>-->
          <DropdownMenu>
            <DropdownMenuTrigger>
              <div class="flex items-center gap-2">
                <span class="text-sm">
                  {{ user.name }}
                </span>
                <img class="w-8 h-8 rounded-full border-2 border-gray-300"
                     :src="`https://ui-avatars.com/api/?name=${ user.name }`" alt="">
              </div>
            </DropdownMenuTrigger>
            <DropdownMenuContent>
              <DropdownMenuLabel>My Account</DropdownMenuLabel>
              <DropdownMenuSeparator/>
              <DropdownMenuItem class="cursor-pointer" @click="logout">Logout</DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>
    </header>
    <main class="grow pt-12 pb-4">
      <slot></slot>
    </main>
    <footer></footer>
  </div>
</template>

<style scoped>

</style>
