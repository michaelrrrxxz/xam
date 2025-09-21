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

        <Preference />
        <School />
        <Account />

        <!-- School Tab -->

        <!-- Account Tab -->
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
import api from '@/Api/Axios';
import Preference from '@/Pages/Admin/Settings/Preference.vue';
import School from '@/Pages/Admin/Settings/School.vue';
import Account from '@/Pages/Admin/Settings/Account.vue';

// Active tab
const activeTab = ref('school');
</script>
