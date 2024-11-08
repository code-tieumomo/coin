<script setup lang="ts">
import {
  Popover,
  PopoverContent,
  PopoverTrigger
} from "@/components/ui/popover";
import { toast } from "vue-sonner";

interface SubnetResponse {
  data: Subnet[];
  message: string;
  status: number;
}

interface Subnet {
  id: number;
  name: string;
  icon: string;
  description: string;
  provider_embed_url: string;
  miner_embed_url: string;
  users_count: number;
  is_joined: boolean;
  created_at: string;
  updated_at: string;
}

interface JoinResponse {
  data: any;
  message: string;
  status: number;
}

definePageMeta({
  middleware: ["auth"]
});

useHead({
  title: "Subnets"
});

const { $api } = useNuxtApp();

const subnets = ref<Subnet[]>([]);
const isJoining = ref<boolean>(false);

const fetchSubnets = async () => {
  const response: SubnetResponse = await $api.get("/subnets");
  subnets.value = response.data;
};

fetchSubnets();

const joinSubnet = async (id: number) => {
  isJoining.value = true;
  try {
    const response: JoinResponse = await $api.post(`/subnets/${ id }/join`);
    if (response.status === 200) {
      toast.success(response.message);
      await navigateTo("/subnets/" + id);
    }
  } catch (e) {
    console.error(e);
    isJoining.value = false;
  }
};
</script>

<template>
  <div class="container mx-auto">
    <h1 class="flex items-center gap-2 text-2xl font-semibold">
      <Icon name="hugeicons:database" class="h-8 w-8"/>
      Subnets
    </h1>

    <div class="mt-8 grid grid-cols-4 gap-4">
      <div v-for="subnet in subnets" :key="subnet.id" class="col-span-1">
        <NuxtLink v-if="subnet.is_joined"
                  :to="`/subnets/${subnet.id}`"
                  class="block h-full w-full cursor-pointer rounded-xl border border-gray-300 bg-white p-4 shadow-sm transition-all hover:border-foreground hover:shadow-lg">
          <h3 class="mb-4 flex items-baseline gap-2 text-lg font-semibold">
            <Icon :name="subnet.icon || 'heroicons-solid:server'" class="h-5 w-5 shrink-0 translate-y-1"/>
            {{ subnet.name }}
          </h3>
          <p class="text-left text-sm text-gray-500">{{ subnet.description }}</p>
        </NuxtLink>
        <Popover v-else>
          <PopoverTrigger class="w-full h-full">
            <div
              class="h-full w-full cursor-pointer rounded-xl border border-gray-300 bg-white p-4 shadow-sm transition-all hover:border-foreground hover:shadow-lg">
              <h3 class="mb-4 flex items-baseline gap-2 text-lg font-semibold">
                <Icon :name="subnet.icon || 'heroicons-solid:server'" class="h-5 w-5 shrink-0 translate-y-1"/>
                {{ subnet.name }}
              </h3>
              <p class="text-left text-sm text-gray-500">{{ subnet.description }}</p>
            </div>
          </PopoverTrigger>
          <PopoverContent>
            <div class="flex items-center gap-2">
              Join this subnet?
              <Button size="xs" @click="joinSubnet(subnet.id)" :disabled="isJoining">
                <Icon v-if="isJoining" name="svg-spinners:90-ring-with-bg" class="w-5 h-5 mr-2"></Icon>
                Confirm
              </Button>
            </div>
          </PopoverContent>
        </Popover>
      </div>
    </div>
  </div>
</template>
