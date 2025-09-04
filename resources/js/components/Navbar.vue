<script setup lang="ts">
import { useRouter } from 'vue-router';

const router = useRouter();

const goHome = () => {
  router.push('/');
};
</script>

<template>
  <div class="flex items-center justify-between w-full">
    <!-- Left: Logo -->
    <div @click="goHome" class="cursor-pointer text-lg font-bold">My App</div>

    <!-- Right: User or Settings -->
    <div class="flex items-center space-x-4">
      <button class="text-sm px-3 py-1 rounded bg-gray-100 hover:bg-gray-200">Profile</button>
    </div>
  </div>
</template>
