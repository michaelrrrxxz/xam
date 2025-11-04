<script setup lang="ts">
import { onMounted } from 'vue';
import { useSchoolStore } from '@/stores/useSchoolStore';

// Skeleton component
import { Skeleton } from '@/components/ui/skeleton';

// Props for customization
const props = defineProps<{
  logoSize?: number;
}>();

const school = useSchoolStore();

onMounted(() => {
  if (!school.name || !school.logo) {
    school.fetchSchool();
  }
});
</script>

<template>
  <div class="flex flex-col items-center justify-center text-center gap-3">
    <!-- Loading -->
    <div v-if="school.loading" class="flex flex-col items-center gap-2">
      <Skeleton :class="`h-${props.logoSize ?? 16} w-${props.logoSize ?? 16} rounded-full`" />
      <Skeleton class="w-40" />
    </div>

    <!-- Loaded -->
    <div v-else class="flex flex-col items-center gap-2">
      <div v-if="school.logo">
        <img
          :src="school.logo"
          alt="School Logo"
          :class="`h-${props.logoSize ?? 16} w-${props.logoSize ?? 16} object-cover rounded-full shadow`"
        />
      </div>
      <div
        v-else
        :class="`h-${props.logoSize ?? 16} w-${props.logoSize ?? 16} flex items-center justify-center bg-gray-300 rounded-full text-lg font-bold shadow`"
      >
        {{ school.name.charAt(0) }}
      </div>

      <span class="font-semibold text-lg truncate max-w-[200px]">
        {{ school.name }}
      </span>
    </div>
  </div>
</template>
