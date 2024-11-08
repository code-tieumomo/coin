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
import NotificationDrawer from "~/components/NotificationDrawer.vue";
import { format } from "date-fns";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Terminal } from "lucide-vue-next";
import TimeTracker from "~/components/TimeTracker.vue";
import { ScrollArea } from "~/components/ui/scroll-area";

const authStore = useAuthStore();
const user = authStore.getUser;
const { $auth, $echo, $api } = useNuxtApp();

const isOpenDrawer = ref(false);
const notifications = ref([]);

const closeDrawer = () => {
  isOpenDrawer.value = false;
};

const fetchNotifications = async () => {
  const response = await $api.get("/notifications") as { data: [] };
  notifications.value = response.data;
};

onMounted(async () => {
  await fetchNotifications();

  $echo.private("private-notification." + user.id)
    .listen("PrivateNotification", (e) => {
      toast[e.type || "info"](e.title, {
        description: e.content,
        closeButton: true,
        duration: 1000000
      });

      notifications.value.unshift(e);
    });

  $echo.join("online")
    .here(users => {
      authStore.online = users;
    })
    .joining(user => {
      authStore.online.push(user);
    })
    .leaving(user => {
      authStore.online = authStore.online.filter(u => (u.id !== user.id));
    });
});

const logout = async () => {
  await $auth.logout();
};
</script>

<template>
  <ScrollArea class="h-screen flex flex-col">
    <header class="py-3 border-b shadow-sm">
      <div class="container mx-auto flex items-center gap-4 justify-between">
        <NuxtLink to="/">
          <h1 class="flex items-center gap-2 font-semibold text-lg">
            <Icon name="hugeicons:bitcoin-cpu" class="w-6 h-6"/>
            Intern
          </h1>
        </NuxtLink>

        <div class="flex items-center gap-4">
          <!--<TimeTracker/>-->
          <!--<span class="text-gray-300">|</span>-->
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
          <Icon name="hugeicons:notification-03" class="w-5 h-5 text-gray-500 cursor-pointer"
                @click="isOpenDrawer = !isOpenDrawer;"/>
          <span class="text-gray-300">|</span>
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
              <DropdownMenuLabel>{{ user.email }}</DropdownMenuLabel>
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

    <NotificationDrawer :is-open="isOpenDrawer" @close="closeDrawer">
      <div class="flex items-center justify-between">
        <h2 class="font-semibold">Notifications</h2>
        <Icon name="mdi:close" class="w-4 h-4 cursor-pointer" @click="closeDrawer"/>
      </div>
      <div v-if="notifications.length > 0" class="mt-8 space-y-4">
        <Alert v-for="notification in notifications" :key="notification.id"
               :variant="['error', 'warning'].includes(notification.type) ? 'destructive': 'default'">
          <Terminal class="h-4 w-4"/>
          <AlertTitle>{{ notification.title }}</AlertTitle>
          <AlertDescription>
            <p class="text-xs mb-2">{{ format(new Date(notification.created_at), "dd/MM/yyyy") }}</p>
            <p>{{ notification.content }}</p>
          </AlertDescription>
        </Alert>
        <div class="text-center">...</div>
      </div>
    </NotificationDrawer>
  </ScrollArea>
</template>

<style scoped>

</style>
