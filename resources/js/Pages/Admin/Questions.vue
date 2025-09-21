<script lang="ts" setup>
import { ref, onMounted, reactive, computed } from 'vue';
import api from '@/Api/Axios';
import { useMediaQuery } from '@vueuse/core';
import {
  Table,
  TableHeader,
  TableRow,
  TableHead,
  TableBody,
  TableCell,
} from '@/components/ui/table';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
} from '@/components/ui/dialog';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
  Drawer,
  DrawerContent,
  DrawerHeader,
  DrawerTitle,
  DrawerDescription,
} from '@/components/ui/drawer';
import { Switch } from '@/components/ui/switch';
import { Skeleton } from '@/components/ui/skeleton';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { toast } from 'vue-sonner';
import 'vue-sonner/style.css';

import { Pencil, Trash, Ellipsis, Plus } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import QuestionTextForm from '@/components/forms/QuestionTextForm.vue';
import QuestionImageForm from '@/components/forms/QuestionImageForm.vue';

import type { Question } from '@/src/types';

const question = ref<Question[]>([]);
const isLoading = ref(true);

const isOpen = ref(false);
const isEditOpen = ref(false);
const isSaving = ref(false);
const errors = ref<Record<string, string>>({});
const isDesktop = useMediaQuery('(min-width: 768px)');
const searchQuery = ref('');

const isImageMode = ref(false);
const toggleMode = (val: boolean) => {
  isImageMode.value = val;
  form.format = val ? 'photo' : 'text';
  resetForm();
};

// Unified form state
const form = reactive<Question>({
  id: null,
  test_type: '',
  question: '', // default text
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
  qtype: '',
  format: 'text',
});

// ---------- Helpers ----------
const isLikelyImageString = (s: any) => {
  if (typeof s !== 'string') return false;
  if (s.startsWith('data:')) return true;
  if (/\.(png|jpe?g|gif|webp|svg)(\?.*)?$/i.test(s)) return true;
  if (/^https?:\/\//i.test(s)) return true;
  return false;
};

// ---------- API / CRUD ----------
const fetchQuestion = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('admin/question');
    question.value = response.data;
  } catch (err) {
    console.warn(err);
  } finally {
    isLoading.value = false;
  }
};
onMounted(fetchQuestion);

const openModal = () => {
  resetForm();
  isEditOpen.value = false;
  isOpen.value = true;
};

// const normalize = (val: any) => {
//   if (!val) return null;
//   if (typeof val === 'string') {
//     return val.startsWith('http') ? val : `/storage/${val}`;
//   }
//   return val;
// };

const extractSrc = (html: string | null): string | null => {
  if (!html) return null;
  const match = html.match(/src=["']?([^"'>]+)["']?/);
  return match ? `/${match[1]}` : null; // prepend / for correct path
};

const openEditModal = async (id: number) => {
  try {
    const response = await api.get(`admin/question/${id}`);
    const q = response.data as any;

    const qIsImage = q.format === 'photo';
    isImageMode.value = qIsImage;

    form.id = q.id ?? null;
    form.test_type = q.test_type ?? '';
    form.answer = q.answer ?? 'ch1';
    form.qtype = q.qtype ?? 'verbal reasoning';
    form.format = q.format ?? (qIsImage ? 'photo' : 'text');

    if (qIsImage) {
      const normalize = (val: any) => (val ? extractSrc(val) : null);

      form.question_image = { file: null, preview: normalize(q.question) };
      form.ch1_image = { file: null, preview: normalize(q.ch1) };
      form.ch2_image = { file: null, preview: normalize(q.ch2) };
      form.ch3_image = { file: null, preview: normalize(q.ch3) };
      form.ch4_image = { file: null, preview: normalize(q.ch4) };
      form.ch5_image = { file: null, preview: normalize(q.ch5) };

      // Clear text fields
      form.question = '';
      form.ch1 = '';
      form.ch2 = '';
      form.ch3 = '';
      form.ch4 = '';
      form.ch5 = '';
    } else {
      form.question = q.question ?? '';
      form.ch1 = q.ch1 ?? '';
      form.ch2 = q.ch2 ?? '';
      form.ch3 = q.ch3 ?? '';
      form.ch4 = q.ch4 ?? '';
      form.ch5 = q.ch5 ?? '';

      form.question_image = null;
      form.ch1_image = null;
      form.ch2_image = null;
      form.ch3_image = null;
      form.ch4_image = null;
      form.ch5_image = null;
    }

    isEditOpen.value = true;
    isOpen.value = true;
  } catch (err) {
    console.warn(err);
    toast.error('Failed to load question for editing.');
  }
};

const resetForm = () => {
  form.id = null;
  form.test_type = '';
  form.qtype = 'verbal reasoning';
  form.answer = 'ch1';

  if (isImageMode.value) {
    form.question_image = { file: null, preview: null };
    form.ch1_image = { file: null, preview: null };
    form.ch2_image = { file: null, preview: null };
    form.ch3_image = { file: null, preview: null };
    form.ch4_image = { file: null, preview: null };
    form.ch5_image = { file: null, preview: null };

    form.question = '';
    form.ch1 = '';
    form.ch2 = '';
    form.ch3 = '';
    form.ch4 = '';
    form.ch5 = '';
  } else {
    form.question = '';
    form.ch1 = '';
    form.ch2 = '';
    form.ch3 = '';
    form.ch4 = '';
    form.ch5 = '';

    form.question_image = null;
    form.ch1_image = null;
    form.ch2_image = null;
    form.ch3_image = null;
    form.ch4_image = null;
    form.ch5_image = null;
  }
};

const saveQuestion = async () => {
  errors.value = {};
  isSaving.value = true;

  try {
    if (isImageMode.value) {
      const fd = new FormData();

      const fields = [
        'question_image',
        'ch1_image',
        'ch2_image',
        'ch3_image',
        'ch4_image',
        'ch5_image',
      ];
      fields.forEach((f) => {
        const v = (form as any)[f];
        if (v && v.file) {
          fd.append(f, v.file);
        } else if (v && v.preview) {
          fd.append(f, v.preview);
        }
      });

      fd.append('test_type', form.test_type ?? '');
      fd.append('answer', form.answer ?? 'ch1');
      fd.append('qtype', form.qtype ?? '');
      fd.append('format', 'photo');

      if (form.id) {
        fd.append('_method', 'PUT');
        await api.post(`admin/question/${form.id}`, fd, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
        toast.success('Question updated (image mode)!');
      } else {
        await api.post('admin/question', fd, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
        toast.success('Question added (image mode)!');
      }
    } else {
      const payload = {
        format: 'text',
        test_type: form.test_type,
        question: form.question ?? '',
        ch1: form.ch1 ?? '',
        ch2: form.ch2 ?? '',
        ch3: form.ch3 ?? '',
        ch4: form.ch4 ?? '',
        ch5: form.ch5 ?? '',
        answer: form.answer,
        qtype: form.qtype,
      };

      if (form.id) {
        await api.put(`admin/question/${form.id}`, payload);
        toast.success('Question updated!');
      } else {
        await api.post('admin/question', payload);
        toast.success('Question added!');
      }
    }

    await fetchQuestion();
    resetForm();
    isOpen.value = false;
    isEditOpen.value = false;
  } catch (err: any) {
    console.warn(err);
    if (err?.response?.data?.errors) {
      const backendErrors = err.response.data.errors;
      errors.value = Object.keys(backendErrors).reduce((acc: any, key) => {
        acc[key] = backendErrors[key][0];
        return acc;
      }, {});
    } else {
      toast.error(err?.response?.data?.message ?? 'Something went wrong.');
    }
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
          await fetchQuestion();
        } catch {
          toast.error('Failed to delete question.');
        }
      },
    },
  });
};

const filteredQuestion = computed(() => {
  if (!searchQuery.value) return question.value;
  return question.value.filter((e) => {
    const s =
      typeof e.question === 'string'
        ? e.question
        : ((e.question && (e.question as UploadableField).preview) ?? '');
    return s.toLowerCase().includes(searchQuery.value.toLowerCase());
  });
});
</script>

<template>
  <AppLayout>
    <section class="flex flex-col md:p-6">
      <!-- Header -->
      <div class="flex items-center justify-between gap-4 p-4 border-b bg-card rounded-t-lg">
        <div class="flex-1 max-w-sm">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search question..."
            class="w-full px-3 py-2 text-sm border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary"
          />
        </div>

        <div class="flex items-center gap-3">
          <span class="text-sm">Text</span>
          <Switch :model-value="isImageMode" @update:model-value="toggleMode" />
          <span class="text-sm">Image</span>

          <Button size="sm" variant="secondary" class="flex items-center gap-2" @click="openModal">
            <Plus class="w-4 h-4" />
            {{ isImageMode ? 'Add Image Question' : 'Add Text Question' }}
          </Button>
        </div>
      </div>

      <!-- Table -->
      <div class="w-full overflow-x-auto rounded-md border mt-4">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Question</TableHead>
              <TableHead>Test Type</TableHead>
              <TableHead>Answer</TableHead>
              <TableHead></TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="isLoading">
              <TableRow v-for="n in 10" :key="n">
                <TableCell><Skeleton class="h-4 w-32 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-48 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-24 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-10 rounded" /></TableCell>
              </TableRow>
            </template>
            <template v-else>
              <TableRow v-for="q in filteredQuestion" :key="q.id">
                <TableCell class="max-w-xs">
                  <div
                    v-if="typeof q.question === 'string' && q.question"
                    v-html="q.question"
                    class="prose max-h-32 overflow-hidden"
                  />
                  <div
                    v-else-if="q.question && typeof q.question === 'object' && q.question.preview"
                  >
                    <img :src="q.question.preview" class="max-h-24 object-contain rounded" />
                  </div>
                  <div v-else class="text-muted-foreground">No question</div>
                </TableCell>

                <TableCell>{{ q.test_type || '-' }}</TableCell>
                <TableCell>
                  {{
                    {
                      ch1: 'Choice 1',
                      ch2: 'Choice 2',
                      ch3: 'Choice 3',
                      ch4: 'Choice 4',
                      ch5: 'Choice 5',
                    }[q.answer] || 'Unknown'
                  }}
                </TableCell>
                <TableCell>
                  <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                      <Button variant="ghost" size="sm"><Ellipsis class="w-4 h-4" /></Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <DropdownMenuLabel>Actions</DropdownMenuLabel>
                      <DropdownMenuSeparator />
                      <DropdownMenuItem @click="openEditModal(q.id)">
                        <Pencil class="w-4 h-4 mr-2" /> Edit
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="deleteQuestion(q.id)" class="text-red-500">
                        <Trash class="w-4 h-4 mr-2" /> Delete
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </TableCell>
              </TableRow>
              <TableRow v-if="!question.length">
                <TableCell colspan="4" class="text-center">No data available.</TableCell>
              </TableRow>
            </template>
          </TableBody>
        </Table>
      </div>

      <!-- Add/Edit Modal (Desktop) -->
      <Dialog v-if="isDesktop" v-model:open="isOpen">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{{ isEditOpen ? 'Edit Question' : 'Add Question' }}</DialogTitle>
            <DialogDescription>
              Fill out the form to {{ isEditOpen ? 'update' : 'create' }} a question.
            </DialogDescription>
          </DialogHeader>

          <QuestionTextForm
            v-if="!isImageMode"
            :form="form"
            :errors="errors"
            :isSaving="isSaving"
            @save="saveQuestion"
          />
          <QuestionImageForm
            v-else
            :form="form"
            :errors="errors"
            :isSaving="isSaving"
            @save="saveQuestion"
          />
        </DialogContent>
      </Dialog>

      <!-- Add/Edit Drawer (Mobile) -->
      <Drawer v-else v-model:open="isOpen">
        <DrawerContent>
          <DrawerHeader>
            <DrawerTitle>{{ isEditOpen ? 'Edit Question' : 'Add Question' }}</DrawerTitle>
            <DrawerDescription>
              Fill out the form to {{ isEditOpen ? 'update' : 'create' }} a question.
            </DrawerDescription>
          </DrawerHeader>

          <QuestionTextForm
            v-if="!isImageMode"
            :form="form"
            :errors="errors"
            :isSaving="isSaving"
            @save="saveQuestion"
          />
          <QuestionImageForm
            v-else
            :form="form"
            :errors="errors"
            :isSaving="isSaving"
            @save="saveQuestion"
          />
        </DrawerContent>
      </Drawer>
    </section>
  </AppLayout>
</template>
