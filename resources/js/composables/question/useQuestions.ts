import { ref, reactive, computed, onMounted } from 'vue';
import api from '@/Api/Axios';
import { toast } from 'vue-sonner';
import type { Question } from '@/src/types';

export interface UploadableField {
  file: File | null;
  preview: string | null;
}

export function useQuestions() {
  const questions = ref<Question[]>([]);
  const isLoading = ref(true);
  const isSaving = ref(false);
  const errors = ref<Record<string, string>>({});
  const isOpen = ref(false);
  const isEditOpen = ref(false);
  const isImageMode = ref(false);
  const searchQuery = ref('');

  const form = reactive<Question>({
    id: null,
    test_type: '',
    question: '',
    question_image: null,
    ch1: '',
    ch1_image: null,
    ch2: '',
    ch2_image: null,
    ch3: '',
    ch3_image: null,
    ch4: '',
    ch4_image: null,
    ch5: '',
    ch5_image: null,
    answer: 'ch1',
    qtype: 'verbal reasoning',
    format: 'text',
  });

  const fetchQuestions = async () => {
    isLoading.value = true;
    try {
      const { data } = await api.get('admin/question');
      questions.value = data;
    } catch (err) {
      console.error(err);
      toast.error('Failed to fetch questions');
    } finally {
      isLoading.value = false;
    }
  };

  onMounted(fetchQuestions);

  const toggleMode = (val: boolean) => {
    isImageMode.value = val;
    form.format = val ? 'photo' : 'text';
    resetForm();
  };

  const resetForm = () => {
    Object.assign(form, {
      id: null,
      test_type: '',
      qtype: 'verbal reasoning',
      answer: 'ch1',
      format: isImageMode.value ? 'photo' : 'text',
      question: '',
      ch1: '',
      ch2: '',
      ch3: '',
      ch4: '',
      ch5: '',
      question_image: isImageMode.value ? { file: null, preview: null } : null,
      ch1_image: isImageMode.value ? { file: null, preview: null } : null,
      ch2_image: isImageMode.value ? { file: null, preview: null } : null,
      ch3_image: isImageMode.value ? { file: null, preview: null } : null,
      ch4_image: isImageMode.value ? { file: null, preview: null } : null,
      ch5_image: isImageMode.value ? { file: null, preview: null } : null,
    });
  };

  const openModal = () => {
    resetForm();
    isEditOpen.value = false;
    isOpen.value = true;
  };

  const openEditModal = async (id: number) => {
    try {
      const { data } = await api.get(`admin/question/${id}`);
      Object.assign(form, data);
      isImageMode.value = data.format === 'photo';
      isEditOpen.value = true;
      isOpen.value = true;
    } catch (err) {
      console.error(err);
      toast.error('Failed to load question for editing.');
    }
  };

  const saveQuestion = async () => {
    errors.value = {};
    isSaving.value = true;
    try {
      if (isImageMode.value) {
        const fd = new FormData();
        ['question_image', 'ch1_image', 'ch2_image', 'ch3_image', 'ch4_image', 'ch5_image'].forEach(
          (f) => {
            const v = (form as any)[f];
            if (v && v.file) fd.append(f, v.file);
          }
        );
        fd.append('test_type', form.test_type);
        fd.append('answer', form.answer);
        fd.append('qtype', form.qtype);
        fd.append('format', 'photo');

        if (form.id) {
          fd.append('_method', 'PUT');
          await api.post(`admin/question/${form.id}`, fd);
          toast.success('Question updated!');
        } else {
          await api.post('admin/question', fd);
          toast.success('Question created!');
        }
      } else {
        const payload = {
          format: 'text',
          test_type: form.test_type,
          question: form.question,
          ch1: form.ch1,
          ch2: form.ch2,
          ch3: form.ch3,
          ch4: form.ch4,
          ch5: form.ch5,
          answer: form.answer,
          qtype: form.qtype,
        };
        if (form.id) {
          await api.put(`admin/question/${form.id}`, payload);
          toast.success('Question updated!');
        } else {
          await api.post('admin/question', payload);
          toast.success('Question created!');
        }
      }

      await fetchQuestions();
      resetForm();
      isOpen.value = false;
    } catch (err: any) {
      console.error(err);
      errors.value = err?.response?.data?.errors ?? {};
      toast.error('Failed to save question.');
    } finally {
      isSaving.value = false;
    }
  };

  const deleteQuestion = async (id: number) => {
    toast('Are you sure?', {
      description: 'This will permanently delete the question.',
      action: {
        label: 'Confirm',
        onClick: async () => {
          try {
            await api.delete(`admin/question/${id}`);
            toast.success('Question deleted!');
            await fetchQuestions();
          } catch {
            toast.error('Failed to delete question.');
          }
        },
      },
    });
  };

  const filteredQuestions = computed(() => {
    if (!searchQuery.value) return questions.value;
    return questions.value.filter((q) => {
      const s = typeof q.question === 'string' ? q.question : '';
      return s.toLowerCase().includes(searchQuery.value.toLowerCase());
    });
  });

  return {
    questions,
    form,
    errors,
    isLoading,
    isSaving,
    isOpen,
    isEditOpen,
    isImageMode,
    searchQuery,
    filteredQuestions,
    fetchQuestions,
    openModal,
    openEditModal,
    resetForm,
    toggleMode,
    saveQuestion,
    deleteQuestion,
  };
}
