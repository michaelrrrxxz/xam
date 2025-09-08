<script setup lang="ts">
import type { LucideIcon } from 'lucide-vue-next';
import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar';
import { RouterLink, useRoute } from 'vue-router';

const props = defineProps<{
  items: {
    title: string;
    url: string;
    icon?: LucideIcon;
  }[];
}>();

const route = useRoute();

const isActive = (url: string) => {
  return route.path === url || route.path.startsWith(url + '/');
};
</script>

<template>
  <SidebarGroup>
    <SidebarGroupLabel>Navigation</SidebarGroupLabel>
    <SidebarMenu>
      <SidebarMenuItem v-for="item in props.items" :key="item.title">
        <RouterLink :to="item.url">
          <SidebarMenuButton
            :tooltip="item.title"
            :class="{
              'bg-gray-200 text-black': isActive(item.url),
              'hover:bg-gray-100': !isActive(item.url),
            }"
          >
            <component :is="item.icon" v-if="item.icon" class="mr-2" />
            <span>{{ item.title }}</span>
          </SidebarMenuButton>
        </RouterLink>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>
