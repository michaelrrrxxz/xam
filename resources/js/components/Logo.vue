<script setup lang="ts">
import { onMounted } from 'vue';
import { useSchoolStore } from '@/stores/useSchoolStore';

// Replace these imports if your skeleton components have different names/paths
import { Skeleton } from '@/components/ui/skeleton';

const school = useSchoolStore();

onMounted(() => {
  // fetch once when component mounts; store handles loading flag
  if (!school.name || !school.logo) {
    school.fetchSchool();
  }
});
</script>

<template>
  <div class="flex flex-col items-center justify-center text-center gap-3">
    <!-- Logo: show circular skeleton while loading -->
    <div v-if="school.loading" class="flex flex-col items-center gap-2">
      <!-- circular skeleton for logo -->
      <Skeleton class="h-16 w-16 rounded-full" />

      <!-- skeleton for name -->
      <Skeleton class="w-40" />
    </div>

    <!-- When not loading show actual logo OR fallback -->
    <div v-else class="flex flex-col items-center gap-2">
      <div v-if="school.logo">
        <img
          :src="school.logo"
          alt="School Logo"
          class="h-16 w-16 object-cover rounded-full shadow"
        />
      </div>

      <div
        v-else
        class="h-16 w-16 flex items-center justify-center bg-gray-300 rounded-full text-lg font-bold shadow"
      >
        {{ school.name.charAt(0) }}
      </div>

      <span class="font-semibold text-lg truncate max-w-[200px]">
        {{ school.name }}
      </span>
    </div>
  </div>
</template>
