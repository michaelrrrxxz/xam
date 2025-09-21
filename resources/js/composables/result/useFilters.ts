// src/composables/useFilters.ts
import { ref, computed } from 'vue';
import type { Student, Result } from '@/src/types';

export function useFilters(result: () => Result[]) {
  const searchQuery = ref('');
  const ageFilter = ref<'all' | '16below' | '17' | '18above'>('all');
  const courseFilter = ref<Student['course'] | 'all'>('all');

  const calculateAgeAtExam = (birthDay: string, examDate: string): number => {
    const birthDate = new Date(birthDay);
    const exam = new Date(examDate);

    let age = exam.getFullYear() - birthDate.getFullYear();
    const m = exam.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && exam.getDate() < birthDate.getDate())) {
      age--;
    }
    return age;
  };

  const filteredResults = computed(() => {
    let results = result();

    // Search filter
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase();
      results = results.filter(
        (r) =>
          `${r.student.last_name} ${r.student.first_name} ${r.student.middle_name}`
            .toLowerCase()
            .includes(query) || r.student.id_number.toLowerCase().includes(query)
      );
    }

    // Age filter
    if (ageFilter.value !== 'all') {
      results = results.filter((r) => {
        const age = calculateAgeAtExam(r.student.birth_day, r.created_at);
        if (ageFilter.value === '16below') return age <= 16;
        if (ageFilter.value === '17') return age === 17;
        if (ageFilter.value === '18above') return age >= 18;
        return true;
      });
    }

    // Course filter
    if (courseFilter.value !== 'all') {
      results = results.filter((r) => r.student.course === courseFilter.value);
    }

    return results;
  });

  const uniqueCourses = computed(() => {
    const courses = result().map((r) => r.student.course);
    return Array.from(new Set(courses));
  });

  return { searchQuery, ageFilter, courseFilter, filteredResults, uniqueCourses };
}
