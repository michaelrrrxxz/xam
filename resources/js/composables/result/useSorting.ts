// src/composables/useSorting.ts
import { ref, computed } from 'vue';
import type { Result } from '@/src/types';

export function useSorting(results: () => Result[]) {
  const sortKey = ref<string>('');
  const sortOrder = ref<'asc' | 'desc'>('asc');

  const toggleSort = (key: string) => {
    if (sortKey.value === key) {
      sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
      sortKey.value = key;
      sortOrder.value = 'asc';
    }
  };

  const sortedResults = computed(() => {
    let r = [...results()];
    if (!sortKey.value) return r;

    return r.sort((a, b) => {
      let valA: string | number = '';
      let valB: string | number = '';

      switch (sortKey.value) {
        case 'name':
          valA = `${a.student.last_name} ${a.student.first_name}`.toLowerCase();
          valB = `${b.student.last_name} ${b.student.first_name}`.toLowerCase();
          break;
        case 'course':
          valA = a.student.course.toLowerCase();
          valB = b.student.course.toLowerCase();
          break;
        default:
          valA = (a as any)[sortKey.value] ?? (a.student as any)[sortKey.value] ?? '';
          valB = (b as any)[sortKey.value] ?? (b.student as any)[sortKey.value] ?? '';
      }

      if (typeof valA === 'string') valA = valA.toLowerCase();
      if (typeof valB === 'string') valB = valB.toLowerCase();

      if (valA < valB) return sortOrder.value === 'asc' ? -1 : 1;
      if (valA > valB) return sortOrder.value === 'asc' ? 1 : -1;
      return 0;
    });
  });

  return { sortKey, sortOrder, toggleSort, sortedResults };
}
