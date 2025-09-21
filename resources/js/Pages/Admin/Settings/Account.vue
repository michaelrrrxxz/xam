<template>
  <TabsContent value="account">
    <Card class="p-6 space-y-6">
      <h2 class="text-lg font-semibold">Account Settings</h2>

      <!-- Profile Photo Section -->
      <div class="flex flex-col items-center space-y-3">
        <div class="relative">
          <!-- Avatar with Fallback -->
          <Avatar class="w-28 h-28 border shadow">
            <AvatarImage :src="user.avatar" :alt="user.name" />
            <AvatarFallback class="text-lg font-semibold">
              {{ getInitials(user.name) }}
            </AvatarFallback>
          </Avatar>

          <!-- Edit Icon -->
          <button
            type="button"
            class="absolute bottom-1 right-1 bg-white rounded-full p-1 shadow hover:bg-gray-100"
            @click="triggerFileInput"
          >
            <Camera class="w-5 h-5 text-gray-600" />
          </button>
        </div>

        <input
          ref="fileInput"
          type="file"
          accept="image/*"
          class="hidden"
          @change="handleFileUpload"
        />
      </div>

      <!-- Account Form -->
      <form @submit.prevent="saveAccountSettings" class="space-y-4">
        <div>
          <Label>Name</Label>
          <Input v-model="user.name" type="text" placeholder="Enter your name" />
        </div>
        <div>
          <Label>Email</Label>
          <Input v-model="user.email" type="email" placeholder="you@example.com" />
        </div>

        <Button type="submit" class="mt-4 w-full">Save Account Settings</Button>
      </form>

      <DeleteUser :user-id="user.id" />
    </Card>
  </TabsContent>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { TabsContent } from '@/components/ui/tabs';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Card } from '@/components/ui/card';
import { Camera } from 'lucide-vue-next';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useUserStore } from '@/stores/useUserStore';
import api from '@/Api/Axios';
import DeleteUser from './DeleteUser.vue';

const user = useUserStore();
const fileInput = ref<HTMLInputElement | null>(null);
const password = ref('');

function triggerFileInput() {
  fileInput.value?.click();
}

function handleFileUpload(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    const file = target.files[0];
    // Preview avatar
    user.avatar = URL.createObjectURL(file);

    // TODO: Upload to API
    // const formData = new FormData();
    // formData.append('avatar', file);
    // await api.post('/user/avatar', formData, {
    //   headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    // });
  }
}

async function saveAccountSettings() {
  await api.post(
    '/user/update',
    {
      email: user.email,
      password: password.value || undefined,
    },
    {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    }
  );
}

function getInitials(name: string) {
  if (!name) return 'U';
  return name
    .split(' ')
    .map((n) => n[0]?.toUpperCase())
    .join('')
    .slice(0, 2);
}
</script>
