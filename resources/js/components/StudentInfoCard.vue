<script setup lang="ts">
import { defineProps } from 'vue';

const props = defineProps<{
  studentData: {
    name: string;
    id_number: string;
    course: string;
    gender: string;
  } | null;
  schoolData: {
    school: string;
  } | null;
}>();
</script>

<template>
  <div
    v-if="studentData && schoolData"
    class="mb-6 rounded-xl bg-white dark:bg-gray-800 shadow-md text-gray-800 dark:text-gray-100 font-serif p-6"
  >
    <div class="text-center mb-4">
      <h2 class="text-2xl font-bold underline tracking-wide">Student Information</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-lg">
      <div class="flex flex-col">
        <span class="font-semibold text-gray-700 dark:text-gray-300">Name</span>
        <span
          class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600"
        >
          {{ studentData.name }}
        </span>
      </div>

      <div class="flex flex-col">
        <span class="font-semibold text-gray-700 dark:text-gray-300">ID Number</span>
        <span
          class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600"
        >
          {{ studentData.id_number }}
        </span>
      </div>

      <div class="flex flex-col">
        <span class="font-semibold text-gray-700 dark:text-gray-300">Course</span>
        <span
          class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600"
        >
          {{ studentData.course }}
        </span>
      </div>

      <div class="flex flex-col">
        <span class="font-semibold text-gray-700 dark:text-gray-300">Gender</span>
        <span
          class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600"
        >
          {{ studentData.gender }}
        </span>
      </div>

      <div class="flex flex-col sm:col-span-2">
        <span class="font-semibold text-gray-700 dark:text-gray-300">School</span>
        <span
          class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600"
        >
          {{ schoolData.school }}
        </span>
      </div>
    </div>
  </div>
</template>
