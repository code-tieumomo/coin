<script setup lang="ts">
import { Tabs, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Skeleton } from "~/components/ui/skeleton";

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

definePageMeta({
  middleware: ["auth"],
  meta: {
    permission: "subnet|read"
  }
});

const subnet = ref<Subnet | null>(null);
useHead({
  title: subnet ? subnet.value?.name : "Subnet"
});

const route = useRoute();
const subnetId = route.params.id;

const { $api } = useNuxtApp();
const role = ref<string | number | undefined>(undefined);

const fetchSubnet = async () => {
  const response = await $api.get(`/subnets/${ subnetId }`);
  subnet.value = response.data;
};

fetchSubnet();

const changeRole = (value: string | number | undefined) => {
  role.value = value;
};
</script>

<template>
  <div class="container mx-auto">
    <div class="flex items-center justify-between">
      <h1 class="flex items-center gap-2 text-2xl font-semibold">
        <Icon :name="subnet?.icon || 'hugeicons:database'" class="h-8 w-8"/>
        {{ subnet?.name }}
      </h1>

      <div class="flex items-center gap-2">
        <div class="flex items-center gap-1" v-if="!role">
          <span class="text-sm">
            Pick a role to continue
          </span>
          <Icon name="line-md:arrow-right" class="w-4 h-4 text-gray-500"/>
        </div>
        <Tabs :model-value="role" @update:modelValue="changeRole">
          <TabsList>
            <TabsTrigger value="provider">
              Provider
            </TabsTrigger>
            <TabsTrigger value="miner">
              Miner
            </TabsTrigger>
          </TabsList>
        </Tabs>
      </div>
    </div>

    <div class="mt-4">
      <div v-show="role === 'provider'" class="border rounded-xl overflow-hidden border-gray-300">
        <iframe :src="subnet?.provider_embed_url" class="w-full min-h-96 h-[calc(100vh-12rem)]"></iframe>
      </div>
      <div v-show="role === 'miner'" class="border rounded-xl overflow-hidden border-gray-300">
        <iframe :src="subnet?.miner_embed_url" class="w-full min-h-96 h-[calc(100vh-12rem)]"></iframe>
      </div>
      <div v-show="!role">
        <div class="space-y-2">
          <Skeleton class="h-4 w-[250px]"/>
          <Skeleton class="h-4 w-[200px]"/>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
