// src/composables/useResults.ts
import { ref, onMounted } from 'vue';
import api from '@/Api/Axios';
import type { Result } from '@/src/types';

export function useResults() {
  const result = ref<Result[]>([]);
  const isLoading = ref(true);

  const fetchResult = async () => {
    isLoading.value = true;
    try {
      const response = await api.get('/admin/result');
      result.value = response.data;
    } catch (error) {
      console.warn('Error fetching results:', error);
    } finally {
      isLoading.value = false;
    }
  };

  onMounted(fetchResult);

  return { result, isLoading, fetchResult };
}
