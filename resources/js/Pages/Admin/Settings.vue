<template>
  <AppLayout>
    <div class="p-6 max-w-4xl mx-auto space-y-6">
      <h1 class="text-2xl font-bold mb-4">Settings</h1>

      <!-- Tabs -->
      <Tabs v-model:value="activeTab" class="space-y-4">
        <TabsList>
          <TabsTrigger value="school">School</TabsTrigger>
          <TabsTrigger value="account">Account</TabsTrigger>
          <TabsTrigger value="preference">Preference</TabsTrigger>
        </TabsList>

        <TabsContent value="preference">
          <Card class="p-6 space-y-4">
            <h2 class="text-lg font-semibold">Preferences</h2>

            <div class="flex items-center justify-between">
              <span class="font-medium">Theme</span>
              <div class="flex items-center gap-2">
                <Button
                  size="sm"
                  variant="outline"
                  :class="{ 'bg-gray-200 dark:bg-gray-700': mode === 'light' }"
                  @click="mode = 'light'"
                >
                  Light
                </Button>
                <Button
                  size="sm"
                  variant="outline"
                  :class="{ 'bg-gray-200 dark:bg-gray-700': mode === 'dark' }"
                  @click="mode = 'dark'"
                >
                  Dark
                </Button>
                <Button
                  size="sm"
                  variant="outline"
                  :class="{ 'bg-gray-200 dark:bg-gray-700': mode === 'auto' }"
                  @click="mode = 'auto'"
                >
                  System
                </Button>
              </div>
            </div>
          </Card>
        </TabsContent>

        <!-- School Tab -->
        <TabsContent value="school">
          <Card class="p-6 space-y-4">
            <h2 class="text-lg font-semibold">School Settings</h2>
            <form @submit.prevent="saveSchoolSettings" class="space-y-4">
              <div>
                <Label>School Name</Label>
                <Input v-model="schoolForm.school_name" placeholder="Enter school name" />
              </div>

              <div>
                <Label>School Logo</Label>
                <Input type="file" @change="onFileChange" />
                <div v-if="preview" class="mt-2">
                  <img :src="preview" class="h-16 object-contain rounded" />
                </div>
              </div>

              <Button type="submit" class="mt-4 w-full">Save School Settings</Button>
            </form>
          </Card>
        </TabsContent>

        <!-- Account Tab -->
        <TabsContent value="account">
          <Card class="p-6 space-y-4">
            <h2 class="text-lg font-semibold">Account Settings</h2>
            <form @submit.prevent="saveAccountSettings" class="space-y-4">
              <div>
                <Label>Email</Label>
                <Input v-model="accountForm.email" type="email" placeholder="you@example.com" />
              </div>

              <div>
                <Label>Password</Label>
                <Input v-model="accountForm.password" type="password" placeholder="********" />
              </div>

              <Button type="submit" class="mt-4 w-full">Save Account Settings</Button>
            </form>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
// import { ref } from "vue";
import { useColorMode } from '@vueuse/core'; // 👈 import VueUse color mode

const mode = useColorMode(); // reactive: "light" | "dark" | "auto"

import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Card } from '@/components/ui/card';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs';
import api from '@/Api/Axios';

// Active tab
const activeTab = ref('school');

// School form state
const schoolForm = ref<{
  school_name: string;
  school_logo: string | File;
}>({
  school_name: '',
  school_logo: '',
});
const preview = ref<string | null>(null);

function onFileChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (file) {
    schoolForm.value.school_logo = file;
    preview.value = URL.createObjectURL(file);
  }
}

async function saveSchoolSettings() {
  const data = new FormData();
  data.append('school_name', schoolForm.value.school_name);
  if (schoolForm.value.school_logo) {
    data.append('school_logo', schoolForm.value.school_logo);
  }
  await api.post('/admin/settings', data);
  alert('School settings saved!');
}

// Account form state
const accountForm = ref({
  email: '',
  password: '',
});

async function saveAccountSettings() {
  await api.post('/user/update', accountForm.value, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`,
    },
  });
  alert('Account settings saved!');
}
</script>
