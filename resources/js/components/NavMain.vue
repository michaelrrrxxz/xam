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
            :class="
              isActive(item.url)
                ? 'bg-sidebar-primary text-sidebar-primary-foreground font-medium hover:bg-sidebar-primary hover:text-sidebar-primary-foreground'
                : 'hover:bg-sidebar-accent hover:text-sidebar-accent-foreground'
            "
          >
            <component :is="item.icon" v-if="item.icon" class="mr-2" />
            <span>{{ item.title }}</span>
          </SidebarMenuButton>
        </RouterLink>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>
