<script setup lang="ts">
import type { SidebarProps } from '@/components/ui/sidebar';
import {
  SquareTerminal,
  GalleryVerticalEnd,
  FileQuestionMark,
  GraduationCap,
  Paperclip,
} from 'lucide-vue-next';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import TeamSwitcher from '@/components/TeamSwitcher.vue';
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarRail,
} from '@/components/ui/sidebar';
import { onMounted, ref } from 'vue';
import api from '@/Api/Axios';

// Type for user object
interface User {
  name: string;
  email: string;
  avatar: string;
}

// Reactive user state with default values
const user = ref<User>({
  name: '',
  email: '',
  avatar: '/avatars/default.jpg',
});

// Props for Sidebar component
const props = defineProps<SidebarProps>();

// Fetch user data
const fetchUser = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) return;

    const response = await api.get('/user', {
      headers: { Authorization: `Bearer ${token}` },
    });

    user.value = {
      name: response.data?.name ?? '',
      email: response.data?.email ?? '',
      avatar: '/avatars/default.jpg', // Provide default avatar if none exists in response
    };
  } catch (error) {
    console.error('Failed to fetch user:', error);
  }
};

// Fetch user data on component mount
onMounted(() => {
  fetchUser();
});

// Data for the Sidebar
const data = ref({
  user: user.value,
  teams: [
    {
      name: 'Michael Angelo',
      logo: GalleryVerticalEnd,
      plan: 'Mangaoang',
    },
  ],
  navMain: [
    {
        title: 'Dashboard',
        url: '/dashboard',
        icon: GalleryVerticalEnd,
        isActive: true,
  },
    {
      title: 'Batch',
      url: '/batch',
      icon: SquareTerminal,
      isActive: true,
    },
    {
      title: 'Questions',
      url: '/questions',
      icon: FileQuestionMark,
      isActive: true,
    },
    {
      title: 'Enrolled Students',
      url: '/enrolled-students',
      icon: GraduationCap,
      isActive: true,
    },
    {
        title: 'Results',
        url: '/admin/results',
        icon: Paperclip,
        isActive: true
    }
  ],
});
</script>

<template>
  <Sidebar v-bind="props">
    <SidebarHeader>
      <TeamSwitcher :teams="data.teams" />
    </SidebarHeader>
    <SidebarContent>
      <NavMain :items="data.navMain" />
    </SidebarContent>
    <SidebarFooter>
      <!-- Debugging: Directly display user data here -->

      <NavUser :user="data.user" />
    </SidebarFooter>
    <SidebarRail />
  </Sidebar>
</template>
