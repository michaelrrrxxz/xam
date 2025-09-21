// composables/useEnrolledStudents.ts
import { ref, reactive, computed, onMounted } from 'vue';
import api from '@/Api/Axios';

import { EnrolledStudent } from '@/src/types';
export function useEnrolledStudents(router: any, toast: any) {
  const enrolledStudents = ref<EnrolledStudent[]>([]);
  const isLoading = ref(false);
  const isSaving = ref(false);

  const isOpen = ref(false);
  const isUploadOpen = ref(false);
  const isEditOpen = ref(false);

  const sortKey = ref<'id_number' | 'first_name' | 'last_name'>('id_number');
  const sortOrder = ref<'asc' | 'desc'>('asc');

  const searchQuery = ref('');
  const courseFilter = ref<EnrolledStudent['course'] | 'all'>('all');
  const genderFilter = ref<'all' | 'male' | 'female'>('all');
  const ageFilter = ref<'all' | '16below' | '17' | '18above'>('all');

  const errors = ref<
    Partial<
      Record<'id_number' | 'last_name' | 'first_name' | 'birth_day' | 'course' | 'gender', string>
    >
  >({});

  const form = reactive<{
    id: number | null;
    id_number: string;
    last_name: string;
    first_name: string;
    middle_name: string;
    course: string;
    birth_day: string;
    gender: string;
  }>({
    id: null,
    id_number: '',
    first_name: '',
    last_name: '',
    middle_name: '',
    course: '',
    birth_day: '',
    gender: '',
  });

  // 🔹 Fetch Students
  const fetchEnrolledStudents = async () => {
    isLoading.value = true;
    try {
      const response = await api.get('admin/enrolledStudent');
      enrolledStudents.value = response.data;
    } catch (err) {
      console.warn(err);
    } finally {
      isLoading.value = false;
    }
  };

  // 🔹 Modal handlers
  const openModal = () => {
    resetForm();
    isOpen.value = true;
  };

  const openUploadModal = () => {
    resetForm();
    isUploadOpen.value = true;
  };

  const openEditModal = async (id: number) => {
    const response = await api.get(`admin/enrolledStudent/${id}`);
    const enrolledStudent = response.data;
    if (enrolledStudent) {
      form.id = enrolledStudent.id;
      form.last_name = enrolledStudent.last_name;
      form.first_name = enrolledStudent.first_name;
      form.middle_name = enrolledStudent.middle_name;
      form.id_number = enrolledStudent.id_number;
      form.course = enrolledStudent.course;
      form.birth_day = enrolledStudent.birth_day;
      form.gender = enrolledStudent.gender;
      isEditOpen.value = true;
    }
  };

  // 🔹 Reset form
  const resetForm = () => {
    form.id = null;
    form.id_number = '';
    form.course = '';
    form.first_name = '';
    form.last_name = '';
    form.middle_name = '';
    form.birth_day = '';
    form.gender = '';
  };

  // 🔹 Save (Add/Update)
  const saveEnrolledStudent = async () => {
    errors.value = {};
    isSaving.value = true;
    try {
      if (form.id) {
        await api.put(`admin/enrolledStudent/${form.id}`, form);
        toast.success('EnrolledStudent updated successfully!');
      } else {
        await api.post('admin/enrolledStudent', form);
        toast.success('EnrolledStudent added successfully!');
      }
      await fetchEnrolledStudents();
      resetForm();
      isOpen.value = false;
      isEditOpen.value = false;
    } catch (error: any) {
      if (error.response?.data?.errors) {
        const backendErrors = error.response.data.errors;
        errors.value = Object.keys(backendErrors).reduce(
          (acc, key) => {
            acc[key] = backendErrors[key][0];
            return acc;
          },
          {} as { [key: string]: string }
        );
      } else {
        toast.error('Something went wrong.');
      }
    } finally {
      isSaving.value = false;
    }
  };

  // 🔹 Upload Students
  const uploadEnrolledStudent = async (formData: FormData) => {
    isSaving.value = true;
    try {
      await api.post('admin/enrolled-students/upload', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      toast.success('Students uploaded successfully!');
      await fetchEnrolledStudents();
      isUploadOpen.value = false;
    } catch {
      toast.error('Failed to upload students.');
    } finally {
      isSaving.value = false;
    }
  };

  // 🔹 View Student
  const viewEnrolledStudent = (id: number) => {
    router.push({ name: 'ViewEnrolledStudent', params: { id } });
  };

  // 🔹 Delete Student
  const deleteEnrolledStudent = async (id: number) => {
    toast('Are you sure?', {
      position: 'top-center',
      description: 'This will permanently delete the enrolled student.',
      action: {
        label: 'Confirm',
        onClick: async () => {
          try {
            await api.delete(`admin/enrolledStudent/${id}`);
            toast.success('EnrolledStudent deleted successfully!');
            await fetchEnrolledStudents();
          } catch {
            toast.error('Failed to delete enrolledStudent.');
          }
        },
      },
    });
  };

  // 🔹 Sorting
  const toggleSort = (key: typeof sortKey.value) => {
    if (sortKey.value === key) {
      sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
      sortKey.value = key;
      sortOrder.value = 'asc';
    }
  };

  // 🔹 Helpers
  function calculateAge(birthDay: string, addedDate: string): number {
    const birthDate = new Date(birthDay);
    const added = new Date(addedDate);
    let age = added.getFullYear() - birthDate.getFullYear();
    const m = added.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && added.getDate() < birthDate.getDate())) {
      age--;
    }
    return age;
  }

  const uniqueCourses = computed(() => {
    const courses = enrolledStudents.value.map((e) => e.course);
    return Array.from(new Set(courses));
  });

  const filteredEnrolledStudents = computed(() => {
    let data = enrolledStudents.value;

    if (searchQuery.value) {
      data = data.filter((e) =>
        e.id_number.toLowerCase().includes(searchQuery.value.toLowerCase())
      );
    }

    if (ageFilter.value !== 'all') {
      data = data.filter((r) => {
        const age = calculateAge(r.birth_day, r.created_at);
        if (ageFilter.value === '16below') return age <= 16;
        if (ageFilter.value === '17') return age === 17;
        if (ageFilter.value === '18above') return age >= 18;
        return true;
      });
    }

    if (courseFilter.value !== 'all') {
      data = data.filter((e) => e.course === courseFilter.value);
    }

    if (genderFilter.value !== 'all') {
      data = data.filter((e) => e.gender.toLowerCase() === genderFilter.value);
    }

    return [...data].sort((a, b) => {
      let valA = '';
      let valB = '';

      if (sortKey.value === 'id_number') {
        valA = a.id_number.toLowerCase();
        valB = b.id_number.toLowerCase();
      } else if (sortKey.value === 'last_name') {
        valA = a.last_name.toLowerCase();
        valB = b.last_name.toLowerCase();
      } else if (sortKey.value === 'first_name') {
        valA = a.first_name.toLowerCase();
        valB = b.first_name.toLowerCase();
      }

      if (valA < valB) return sortOrder.value === 'asc' ? -1 : 1;
      if (valA > valB) return sortOrder.value === 'asc' ? 1 : -1;
      return 0;
    });
  });

  // Auto-fetch
  onMounted(fetchEnrolledStudents);

  return {
    enrolledStudents,
    filteredEnrolledStudents,
    uniqueCourses,
    form,
    errors,
    isLoading,
    isSaving,
    isOpen,
    isUploadOpen,
    isEditOpen,
    sortKey,
    sortOrder,
    searchQuery,
    courseFilter,
    genderFilter,
    ageFilter,
    openModal,
    openUploadModal,
    openEditModal,
    saveEnrolledStudent,
    uploadEnrolledStudent,
    deleteEnrolledStudent,
    viewEnrolledStudent,
    toggleSort,
    calculateAge,
  };
}
