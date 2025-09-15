<template>
  <router-view />
  <Toaster position="top-right" />
</template>

<script lang="ts" setup>
import { useColorMode } from '@vueuse/core';
import { useUserStore } from '@/stores/useUserStore';
import { onMounted } from 'vue';
const user = useUserStore();

onMounted(async () => {
  if (!user.name) {
    // fetch only if user is not loaded
    await user.fetchUser();
  }
});

const mode = useColorMode();
</script>
