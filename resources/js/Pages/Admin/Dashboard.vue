<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <header class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
        <Button variant="outline" class="rounded-xl">Refresh</Button>
      </header>

      <!-- Cards Section -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Batch Card -->
        <Card class="rounded-2xl shadow-sm">
          <CardHeader>
            <CardTitle class="text-lg font-medium">Batch</CardTitle>
            <CardDescription>Total count</CardDescription>
          </CardHeader>
          <CardContent>
            <h2 class="text-2xl font-semibold tracking-tight">{{ count.batch }}</h2>
          </CardContent>
        </Card>

        <!-- Enrolled Students Card -->
        <Card class="rounded-2xl shadow-sm">
          <CardHeader>
            <CardTitle class="text-lg font-medium">Enrolled Students</CardTitle>
            <CardDescription>Total count</CardDescription>
          </CardHeader>
          <CardContent>
            <h2 class="text-2xl font-semibold tracking-tight">{{ count.enrolledstudents }}</h2>
          </CardContent>
        </Card>
        <!-- Finished Exams Card -->
        <Card class="rounded-2xl shadow-sm">
        <CardHeader>
            <CardTitle class="text-lg font-medium">Finished Exams</CardTitle>
            <CardDescription>Total count</CardDescription>
        </CardHeader>
        <CardContent>
            <h2 class="text-2xl font-semibold tracking-tight">{{ count.finishedexams }}</h2>
        </CardContent>
        </Card>

      </div>


      <!-- Table Section -->
      <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm p-4">
        <h2 class="text-lg font-medium mb-4">Recent Activity</h2>
        <div class="space-y-3">
          <div
            v-for="i in 5"
            :key="i"
            class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div class="flex flex-col space-y-1">
              <div class="h-4 w-48 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
              <div class="h-3 w-32 bg-gray-100 dark:bg-gray-800 rounded animate-pulse"></div>
            </div>
            <div class="h-6 w-16 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script lang="ts" setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { onMounted, ref } from 'vue';
import api from '@/Api/Axios';

interface Count {
  batch: number;
  enrolledstudents: number;
    finishedexams: number;
}

const count = ref<Count>({ batch: 0, enrolledstudents: 0, finishedexams: 0 });

const fetchCount = async () => {
  try {
    const response = await api.get('/admin/dashboard');
    count.value = response.data;
  } catch (error) {
    console.error('Failed to fetch dashboard count:', error);
  }
};

onMounted(fetchCount);
</script>
