<script setup lang="ts">
import { SidebarProvider, SidebarTrigger, SidebarInset } from '@/components/ui/sidebar';
import AppSidebar from '@/components/AppSidebar.vue';
import { useRoute } from 'vue-router';
import { computed } from 'vue';

const route = useRoute();
const pageTitle = computed(() => route.name || 'Default Title');
</script>

<template>
  <SidebarProvider>
    <AppSidebar />
    <SidebarInset>
      <header class="flex h-12 items-center px-3 border-b bg-card">
        <SidebarTrigger class="mr-2 w-6 h-6" />
        <h1 class="text-lg font-semibold truncate">{{ pageTitle }}</h1>
      </header>
      <div class="flex-1 p-3">
        <slot />
      </div>
    </SidebarInset>
  </SidebarProvider>
</template>
