// src/stores/useSchoolStore.ts
import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/Api/Axios';

export const useSchoolStore = defineStore('school', () => {
  const name = ref('My School');
  const logo = ref<string | null>(null);
  const loading = ref(false);

  async function fetchSchool() {
    loading.value = true;
    try {
      const response = await api.get('/settings');
      const settings = response.data?.settings ?? {};

      name.value = settings.school_name ?? 'My School';
      logo.value = settings.school_logo
        ? `${import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'}/storage/${settings.school_logo}`
        : null;
    } catch (err) {
      console.error('Failed to load school logo:', err);
      name.value = 'My School';
      logo.value = null;
    } finally {
      loading.value = false;
    }
  }

  return { name, logo, loading, fetchSchool };
});
