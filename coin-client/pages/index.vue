<script setup lang="ts">
import { Alert, AlertDescription, AlertTitle } from "~/components/ui/alert";
import { Terminal } from "lucide-vue-next";
import { Card, CardContent, CardHeader } from "~/components/ui/card";
import { useClipboard } from "@vueuse/core";
import rehypeStringify from "rehype-stringify";
import remarkGfm from "remark-gfm";
import remarkParse from "remark-parse";
import remarkRehype from "remark-rehype";
import { unified } from "unified";
import rehypeShiki from "@shikijs/rehype";
import { Progress } from "~/components/ui/progress";

const processor = unified()
  .use(remarkParse)
  .use(remarkGfm)
  .use(remarkRehype, { allowDangerousHtml: true })
  .use(rehypeShiki, {
    theme: "github-dark-high-contrast"
  })
  .use(rehypeStringify);

definePageMeta({
  middleware: ["auth"]
});

const authStore = useAuthStore();
const source = ref(authStore.user?.token);
const { text, copy, copied, isSupported } = useClipboard({ source });
const { $api } = useNuxtApp();

const assignments = ref([]);
const fetchingAssignments = ref(false);

const fetchAssignments = async () => {
  fetchingAssignments.value = true;
  try {
    const response = await $api.get("/assignments") as { data: [] };
    assignments.value = await Promise.all(response.data.map(async (item) => {
      item.description = await processor.process(item.description);
      item.subnets.forEach(subnet => {
        const totalGrades = subnet.grades.reduce((acc, grade) => acc + Number(grade.grade), 0);
        subnet.progress = totalGrades / subnet.needed * 100;
        subnet.is_completed = totalGrades >= subnet.needed;
      });
      return item;
    }));
  } finally {
    fetchingAssignments.value = false;
  }
};

fetchAssignments();
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

    <h3 class="text-xl font-semibold mt-8">Assignments</h3>
    <Transition name="page" mode="out-in">
      <div v-if="fetchingAssignments" class="mt-4">
        <Skeleton class="h-4 w-[300px]"/>
        <Skeleton class="h-4 w-[250px] mt-2"/>
        <Skeleton class="h-4 w-[200px] mt-2"/>
      </div>
      <div class="mt-4 space-y-4" v-else>
        <Card v-for="assignment in assignments" :key="assignment.id">
          <CardHeader>
            <span class="font-semibold">{{ assignment.title }}</span>
          </CardHeader>
          <CardContent>
            <article v-html="assignment.description" class="prose w-full max-w-full"/>
            <hr class="my-4">
            <div v-if="assignment.subnets.length > 0" class="mt-4 space-y-4">
              <div v-for="subnet in assignment.subnets" :key="subnet.id" :class="{ 'opacity-20': subnet.is_completed }">
                <div class="flex items-center gap-2">
                  <Icon :name="subnet.icon || 'heroicons-solid:server'" class="h-5 w-5"/>
                  <NuxtLink :to="'/subnets/' + subnet.id" class="font-medium">{{ subnet.name }}</NuxtLink>
                  <Badge v-if="subnet.is_completed" variant="success">Completed</Badge>
                  <Badge v-else>{{ subnet.progress }}%</Badge>
                </div>
                <Progress class="mt-2" :model-value="subnet.progress"></Progress>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </Transition>
  </div>
</template>

<style scoped>

</style>
