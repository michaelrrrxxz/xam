<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <header class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
        <Button variant="outline" class="rounded-xl" @click="fetchCount"> Refresh </Button>
      </header>

      <!-- Cards Section -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Batch Card -->
        <Card class="rounded-2xl shadow-sm">
          <CardHeader>
            <template v-if="loading">
              <Skeleton class="h-5 w-24 mb-1 rounded-md" />
              <!-- Title Skeleton -->
              <Skeleton class="h-4 w-16 rounded-md" />
              <!-- Description Skeleton -->
            </template>
            <template v-else>
              <CardTitle class="text-lg font-medium">Batch</CardTitle>
              <CardDescription>Total count</CardDescription>
            </template>
          </CardHeader>
          <CardContent>
            <template v-if="loading">
              <Skeleton class="h-8 w-16 rounded-md" />
            </template>
            <template v-else>
              <h2 class="text-2xl font-semibold tracking-tight">{{ count.batch }}</h2>
            </template>
          </CardContent>
        </Card>

        <!-- Enrolled Students Card -->
        <Card class="rounded-2xl shadow-sm">
          <CardHeader>
            <template v-if="loading">
              <Skeleton class="h-5 w-32 mb-1 rounded-md" />
              <!-- Title Skeleton -->
              <Skeleton class="h-4 w-20 rounded-md" />
              <!-- Description Skeleton -->
            </template>
            <template v-else>
              <CardTitle class="text-lg font-medium">Enrolled Students</CardTitle>
              <CardDescription>Total count</CardDescription>
            </template>
          </CardHeader>
          <CardContent>
            <template v-if="loading">
              <Skeleton class="h-8 w-16 rounded-md" />
            </template>
            <template v-else>
              <h2 class="text-2xl font-semibold tracking-tight">{{ count.enrolledstudents }}</h2>
            </template>
          </CardContent>
        </Card>

        <!-- Evaluated Students Card -->
        <Card class="rounded-2xl shadow-sm">
          <CardHeader>
            <template v-if="loading">
              <Skeleton class="h-5 w-36 mb-1 rounded-md" />
              <!-- Title Skeleton -->
              <Skeleton class="h-4 w-20 rounded-md" />
              <!-- Description Skeleton -->
            </template>
            <template v-else>
              <CardTitle class="text-lg font-medium">Evaluated Students</CardTitle>
              <CardDescription>Total count</CardDescription>
            </template>
          </CardHeader>
          <CardContent>
            <template v-if="loading">
              <Skeleton class="h-8 w-16 rounded-md" />
            </template>
            <template v-else>
              <h2 class="text-2xl font-semibold tracking-tight">{{ count.finishedexams }}</h2>
            </template>
          </CardContent>
        </Card>
      </div>

      <!-- Table Section -->
      <div class="rounded-2xl shadow-sm p-4">
        <template v-if="loading">
          <Skeleton class="h-8 w-16 rounded-md text-lg font-medium mb-4" />
        </template>
        <template v-else>
          <h2 class="text-lg font-medium mb-4">Recent Activity</h2>
        </template>

        <template v-if="loading">
          <div class="space-y-3">
            <Skeleton v-for="n in 3" :key="n" class="h-12 w-full rounded-lg" />
          </div>
        </template>

        <template v-else>
          <div v-if="count.recent.length > 0" class="space-y-3">
            <div
              v-for="item in count.recent"
              :key="item.id"
              class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700"
            >
              <div class="flex flex-col">
                <span class="font-medium">
                  {{ item.student.first_name }} {{ item.student.last_name }}
                </span>
                <span class="text-sm text-gray-500">
                  {{ item.batch.name }} • Score: {{ item.total_score }}
                </span>
              </div>
              <span class="text-xs text-gray-400">
                {{ formatRelative(item.created_at) }}
              </span>
            </div>
          </div>
          <div v-else class="text-sm text-gray-500">No recent activity</div>
        </template>
      </div>
    </div>
  </AppLayout>
</template>

<script lang="ts" setup>
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { onMounted, ref } from 'vue';
import api from '@/Api/Axios';
import { Student, Batch, Count, Recent } from '@/src/types';
dayjs.extend(relativeTime);

// --- State ---
const count = ref<Count>({
  batch: 0,
  enrolledstudents: 0,
  finishedexams: 0,
  recent: [],
});

const loading = ref(true);

function formatRelative(time: string) {
  return dayjs(time).fromNow();
}

// --- Fetch data ---
const fetchCount = async () => {
  loading.value = true;
  try {
    const response = await api.get('/admin/dashboard');
    count.value = response.data;
  } catch (error) {
    console.error('Failed to fetch dashboard count:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchCount);
</script>
