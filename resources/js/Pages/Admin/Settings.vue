<template>
  <AppLayout>
    <div class="p-6 max-w-4xl mx-auto space-y-6">
      <h1 class="text-2xl font-bold mb-4">Settings</h1>

      <!-- Tabs -->
      <Tabs v-model:value="activeTab" class="space-y-4">
        <!-- Tab buttons -->
        <TabsList>
          <TabsTrigger value="school">
            <School2 class="w-4 h-4 mr-2" />
            School
          </TabsTrigger>

          <TabsTrigger value="account">
            <Settings class="w-4 h-4 mr-2" />
            Account
          </TabsTrigger>

          <TabsTrigger value="preference">
            <SlidersHorizontal class="w-4 h-4 mr-2" />
            Preference
          </TabsTrigger>

          <TabsTrigger value="users">
            <User class="w-4 h-4 mr-2" />
            Users
          </TabsTrigger>
        </TabsList>

        <!-- Tab contents -->

        <School />

        <Account />

        <Preference />

        <Users />
      </Tabs>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
// import { ref } from "vue";
import { useColorMode } from '@vueuse/core';

const mode = useColorMode();

import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { School2, User, Settings, SlidersHorizontal } from 'lucide-vue-next';
import Preference from '@/Pages/Admin/Settings/Preference.vue';
import School from '@/Pages/Admin/Settings/School.vue';
import Account from '@/Pages/Admin/Settings/Account.vue';
import Users from '@/Pages/Admin/Settings/Users.vue';

// Active tab
const activeTab = ref('school');
</script>
