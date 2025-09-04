<template>
  <router-view />
  <Toaster position="top-right" />
</template>

<script lang="ts" setup>
import { useColorMode } from '@vueuse/core';

const mode = useColorMode();
</script>
