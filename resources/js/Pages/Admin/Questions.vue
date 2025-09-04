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
const formData = new FormData();
import { useRouter } from 'vue-router';
const router = useRouter();
const isLoading = ref(true);
interface UploadableField {
  file: File | null;
  preview: string | null;
}

// NOTE: question and choices can be either string (text / stored URL) or UploadableField
export interface Question {
  id: number;
  test_type: string;

  // Question
  question: string | null; // for text format
  question_image: UploadableField | null; // for photo format

  // Choices
  ch1: string | null;
  ch1_image: UploadableField | null;

  ch2: string | null;
  ch2_image: UploadableField | null;

  ch3: string | null;
  ch3_image: UploadableField | null;

  ch4: string | null;
  ch4_image: UploadableField | null;

  ch5: string | null;
  ch5_image: UploadableField | null;

  answer: 'ch1' | 'ch2' | 'ch3' | 'ch4' | 'ch5';

  qtype:
    | 'verbal reasoning'
    | 'verbal comprehension'
    | 'quantitative reasoning'
    | 'figural reasoning';

  format: 'text' | 'photo';
}

// Reactive States
const question = ref<Question[]>([]); // list of questions
const isOpen = ref(false);
const isFormChoicesOpen = ref(false);
const isEditOpen = ref(false);
const isSaving = ref(false);
const errors = ref<Record<string, string>>({});
const isDesktop = useMediaQuery('(min-width: 768px)');
const searchQuery = ref('');

// mode switch
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
  question: '',           // text mode by default
  question_image: null,   // image mode fallback
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




// ---------- Helpers ----------
const isUploadable = (v: any): v is UploadableField => v && typeof v === 'object' && 'preview' in v;

const isLikelyImageString = (s: any) => {
  if (typeof s !== 'string') return false;
  if (s.startsWith('data:')) return true;
  if (/\.(png|jpe?g|gif|webp|svg)(\?.*)?$/i.test(s)) return true;
  // If it's an absolute http(s) URL it's probably an image for your app usage
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
  }finally {
    isLoading.value = false;
  }
};

onMounted(fetchQuestion);

const openModal = () => {
  resetForm();
  isEditOpen.value = false;
  isOpen.value = true;
};

// Open Edit Modal and auto-detect image/text mode
const openEditModal = async (id: number) => {
  try {
    const response = await api.get(`admin/question/${id}`);
    const q = response.data as any;

    // Helper: normalize DB path to full URL
    const normalize = (val: any) => {
      if (!val) return null;
      if (typeof val === 'string') {
        return val.startsWith('http') ? val : `/storage/${val}`;
      }
      return val;
    };

form.question_image = { file: null, preview: normalize(q.question) };


    // Detect if this question is an image (by checking extension or preview field)
    const qIsImage =
      isLikelyImageString(q.question) ||
      (q.question && typeof q.question === 'object' && 'preview' in q.question);

    isImageMode.value = qIsImage;

    // Map common fields
    form.id = q.id ?? null;
    form.test_type = q.test_type ?? '';
    form.answer = q.answer ?? 'ch1';
    form.qtype = q.qtype ?? 'verbal reasoning';
    form.format = q.format ?? (qIsImage ? 'photo' : 'text');

    if (qIsImage) {
      // Image mode → convert DB paths to preview objects
    form.question_image = { file: null, preview: normalize(q.question) };
      form.ch1_image = { file: null, preview: normalize(q.ch1) };
form.ch2_image = { file: null, preview: normalize(q.ch2) };
form.ch3_image = { file: null, preview: normalize(q.ch3) };
form.ch4_image = { file: null, preview: normalize(q.ch4) };
form.ch5_image = { file: null, preview: normalize(q.ch5) };

      // Text mode
      form.question = q.question ?? '';
      form.ch1 = q.ch1 ?? '';
      form.ch2 = q.ch2 ?? '';
      form.ch3 = q.ch3 ?? '';
      form.ch4 = q.ch4 ?? '';
      form.ch5 = q.ch5 ?? '';
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
    // Reset image fields properly
    form.question_image = { file: null, preview: null };
    form.ch1_image = { file: null, preview: null };
    form.ch2_image = { file: null, preview: null };
    form.ch3_image = { file: null, preview: null };
    form.ch4_image = { file: null, preview: null };
    form.ch5_image = { file: null, preview: null };

    // Also clear text fields in image mode (to avoid stale values)
    form.question = null;
    form.ch1 = null;
    form.ch2 = null;
    form.ch3 = null;
    form.ch4 = null;
    form.ch5 = null;
  } else {
    // Reset text fields
    form.question = '';
    form.ch1 = '';
    form.ch2 = '';
    form.ch3 = '';
    form.ch4 = '';
    form.ch5 = '';

    // Clear image fields
    form.question_image = null;
    form.ch1_image = null;
    form.ch2_image = null;
    form.ch3_image = null;
    form.ch4_image = null;
    form.ch5_image = null;
  }
};


// File upload handler (works with unified form)
const handleFileUpload = (e: Event, field: keyof Question) => {
  const target = e.target as HTMLInputElement;
  const files = target.files;
  if (files && files[0]) {
    const f = files[0];
    // assign UploadableField
    (form as any)[field] = {
      file: f,
      preview: URL.createObjectURL(f),
    } as UploadableField;
  }
};

// Save (create or update) — auto chooses JSON or FormData
const saveQuestion = async () => {
  errors.value = {};
  isSaving.value = true;

  try {
    const fields = isImageMode.value
      ? [
          'question_image',
          'ch1_image',
          'ch2_image',
          'ch3_image',
          'ch4_image',
          'ch5_image',
          'format',
        ]
      : ['question', 'ch1', 'ch2', 'ch3', 'ch4', 'ch5', 'format'];

    const isMultipart = fields.some((f) => {
      const v = (form as any)[f];
      return !!(v && typeof v === 'object' && v.file instanceof File);
    });

    if (isMultipart) {
      const fd = new FormData();
      fields.forEach((f) => {
        const v = (form as any)[f];
        if (v && typeof v === 'object' && v.file) {
          fd.append(f, v.file);
        } else {
          // if preview exists but no file (editing existing image url), send preview as existing url
          if (v && typeof v === 'object' && v.preview) {
            fd.append(f, v.preview as string);
          } else {
            fd.append(f, typeof v === 'string' ? v : '');
          }
        }
      });

      fd.append('test_type', form.test_type ?? '');
      fd.append('answer', form.answer ?? 'ch1');
      fd.append('qtype', form.qtype ?? '');

      if (form.id) {
        // many backends accept POST + _method=PUT for multipart updates
        fd.append('_method', 'PUT');
        await api.post(`admin/question/${form.id}`, fd, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
        toast.success('Question updated (images)!');
      } else {
        await api.post('admin/question', fd, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
        toast.success('Question added (images)!');
      }
    } else {
      // regular JSON payload (text-only)
      const payload = {
        format: form.format,

        test_type: form.test_type,
        question: typeof form.question === 'string' ? form.question : '',
        ch1: typeof form.ch1 === 'string' ? form.ch1 : '',
        ch2: typeof form.ch2 === 'string' ? form.ch2 : '',
        ch3: typeof form.ch3 === 'string' ? form.ch3 : '',
        ch4: typeof form.ch4 === 'string' ? form.ch4 : '',
        ch5: typeof form.ch5 === 'string' ? form.ch5 : '',
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
    isFormChoicesOpen.value = false;
    isEditOpen.value = false;
  } catch (err: any) {
    console.warn(err);
    if (err?.response?.data?.errors) {
      // map backend validation errors (Laravel)
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

// Delete
const deleteQuestion = async (id: number) => {
  toast('Are you sure?', {
    position: 'top-center',
    description: 'This will permanently delete the question.',
    action: {
      label: 'Confirm',
      onClick: async () => {
        try {
          await api.delete(`admin/question/${id}`);
          toast.success('Question deleted successfully!');
          await fetchQuestion();
        } catch {
          toast.error('Failed to delete question.');
        }
      },
    },
  });
};

// Search-safe computed: get string from question whether text or image preview
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
      <div class="flex items-center justify-between gap-4 p-4 border-b bg-card rounded-t-lg">
        <!-- Search -->
        <div class="flex-1 max-w-sm">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search question..."
            class="w-full px-3 py-2 text-sm border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary"
          />
        </div>

        <!-- Mode & Add -->
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
              <TableRow v-for="n in 1" :key="n">
                <TableCell><Skeleton class="h-4 w-32 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-48 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-24 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-10 rounded" /></TableCell>
              </TableRow>
            </template>
            <template v-else>


            <TableRow v-for="q in filteredQuestion" :key="q.id">
              <TableCell class="max-w-xs">
                <!-- show image thumbnail when image, else text -->

                <!-- If it's an image -->
                <div v-if="isUploadable(q.question) || isLikelyImageString(q.question)">
                  <img
                    v-if="
                      isUploadable(q.question) ||
                      (typeof q.question === 'string' && q.question !== '')
                    "
                    :src="
                      isUploadable(q.question)
                        ? q.question.preview
                        : q.question.startsWith('http')
                          ? q.question
                          : `/storage/${q.question}`
                    "
                    class="max-h-16 rounded"
                    alt="question"
                  />
                </div>

                <!-- If it's plain text -->
                <div v-else>
                  {{ q.question }}
                </div>
              </TableCell>
              <TableCell>{{
                (q.test_type || '').charAt(0).toUpperCase() + (q.test_type || '').slice(1)
              }}</TableCell>
              <TableCell>
                {{
                  {
                    ch1: 'Choice 1',
                    ch2: 'Choice 2',
                    ch3: 'Choice 3',
                    ch4: 'Choice 4',
                    ch5: 'Choice 5',
                  }[q.answer] || 'Unknown choice'
                }}
              </TableCell>
              <TableCell>
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="sm" class="flex items-center gap-1">
                      <Ellipsis class="w-4 h-4 mr-2" />
                    </Button>
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
              <TableCell colspan="4" class="text-center"> No data available. </TableCell>
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

          <!-- Toggle -->
          <div class="flex items-center justify-between py-2">
            <Label for="mode">Use Image Mode</Label>
            <Switch :model-value="isImageMode" @update:model-value="toggleMode" />
          </div>

          <!-- Conditional form -->
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
            <DrawerDescription>Fill out the form to create a new question.</DrawerDescription>
          </DrawerHeader>

          <!-- Toggle -->
          <div class="flex items-center justify-between py-2">
            <Label for="mode">Use Image Mode</Label>
            <Switch :model-value="isImageMode" @update:model-value="toggleMode" />
          </div>

          <!-- Conditional form -->
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
