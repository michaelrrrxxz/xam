<script lang="ts" setup>
import { ref, onMounted, reactive, computed } from 'vue';
import api from '@/Api/Axios';
import { useMediaQuery } from '@vueuse/core';
import { useRoute } from 'vue-router';
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

import { Button } from '@/components/ui/button';
import { toast } from 'vue-sonner';
import 'vue-sonner/style.css';

import { Pencil, Trash, Ellipsis, BadgeQuestionMark } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import ExamForm from '@/components/ExamForm.vue';

import { useRouter } from 'vue-router';

const router = useRouter();

// Exam interface
interface Exam {
  id: number;
  name: string;
  description: string | null;
}

// Reactive States
const exams = ref<Exam[]>([]);
const isOpen = ref(false);
const isEditOpen = ref(false);
const isSaving = ref(false);
const errors = ref<{ name?: string }>({});
const isDesktop = useMediaQuery('(min-width: 768px)');
const searchQuery = ref('');

// Unified form state
const form = reactive<{ id: number | null; name: string; description: string }>({
  id: null,
  name: '',
  description: '',
});

// Fetch all exams
const fetchExams = async () => {
  try {
    const response = await api.get('/exam');
    exams.value = response.data;
  } catch (error) {
    console.warn(error);
  }
};

// Open Add Modal
const openModal = () => {
  resetForm();
  isOpen.value = true;
};

// Open Edit Modal
const openEditModal = async (id: number) => {
  const response = await api.get(`/exam/${id}`);
  const exam = response.data;
  if (exam) {
    form.id = exam.id;
    form.name = exam.name;
    form.description = exam.description || '';
    isEditOpen.value = true;
  }
};

// Reset form
const resetForm = () => {
  form.id = null;
  form.name = '';
  form.description = '';
};

const openQuestionsPage = (id: number) => {
  router.push(`/questions/${id}`);
};

// Save Exam (Add or Update)
const saveExam = async () => {
  errors.value = {};
  isSaving.value = true;
  try {
    if (form.id) {
      // Update existing exam
      await api.put(`/exam/${form.id}`, {
        name: form.name,
        description: form.description,
      });
      toast.success('Exam updated successfully!');
    } else {
      // Create new exam
      await api.post('/exam', {
        name: form.name,
        description: form.description,
      });
      toast.success('Exam added successfully!');
    }
    await fetchExams();
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

// Delete Exam
const deleteExam = async (id: number) => {
  toast('Are you sure?', {
    position: 'top-center',
    description: 'This will permanently delete the exam.',
    action: {
      label: 'Confirm',
      onClick: async () => {
        try {
          await api.delete(`/exam/${id}`);
          toast.success('Exam deleted successfully!');
          await fetchExams();
        } catch {
          toast.error('Failed to delete exam.');
        }
      },
    },
  });
};

const filteredExams = computed(() => {
  if (!searchQuery.value) return exams.value;
  return exams.value.filter((e) => e.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

// Fetch exams on mount
onMounted(fetchExams);
</script>
<template>
  <AppLayout>
    <section class="flex flex-col md:p-6">
      <div class="flex items-center justify-between gap-4 p-4 border-b bg-card rounded-t-lg">
        <!-- Left: Title -->
        <!-- <h2 class="text-2xl font-bold text-foreground">Exams</h2> -->

        <!-- Center: Search -->
        <div class="flex-1 max-w-sm">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search exams..."
            class="w-full px-3 py-2 text-sm border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary"
          />
        </div>

        <!-- Right: Add Exam Button -->
        <Button size="sm" variant="secondary" class="flex items-center gap-2" @click="openModal">
          <Plus class="w-4 h-4" />
          Add Exam
        </Button>
      </div>
      <div class="w-full overflow-x-auto rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Name</TableHead>
              <TableHead>Description</TableHead>
              <TableHead></TableHead>
            </TableRow>
          </TableHeader>

          <TableBody>
            <TableRow v-for="e in filteredExams" :key="e.id">
              <TableCell>{{ e.name }}</TableCell>
              <TableCell>{{ e.description }}</TableCell>
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

                    <DropdownMenuItem @click="openQuestionsPage(e.id)" m>
                      <BadgeQuestionMark class="w-4 h-4 mr-2" /> Questions
                    </DropdownMenuItem>

                    <DropdownMenuItem @click="openEditModal(e.id)">
                      <Pencil class="w-4 h-4 mr-2" /> Edit
                    </DropdownMenuItem>

                    <DropdownMenuItem @click="deleteExam(e.id)" class="text-red-500">
                      <Trash class="w-4 h-4 mr-2" /> Delete
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </TableCell>
            </TableRow>
            <TableRow v-if="!exams.length">
              <TableCell colspan="3" class="text-center"> No data available. </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- Add Exam -->
      <Dialog v-if="isDesktop" v-model:open="isOpen">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Add Exam</DialogTitle>
            <DialogDescription>Fill out the form to create a new exam.</DialogDescription>
          </DialogHeader>
          <!-- reuse your form -->
          <ExamForm :form="form" :errors="errors" :isSaving="isSaving" @save="saveExam" />
        </DialogContent>
      </Dialog>

      <!-- Drawer for Mobile -->
      <Drawer v-else v-model:open="isOpen">
        <DrawerContent>
          <DrawerHeader>
            <DrawerTitle>Add Exam</DrawerTitle>
            <DrawerDescription>Fill out the form to create a new exam.</DrawerDescription>
          </DrawerHeader>
          <!-- reuse the same form -->
          <ExamForm :form="form" :errors="errors" :isSaving="isSaving" @save="saveExam" />
        </DrawerContent>
      </Drawer>

      <Dialog v-if="isDesktop" v-model:open="isEditOpen">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Edit Exam</DialogTitle>
            <DialogDescription>Update the details of the exam.</DialogDescription>
          </DialogHeader>
          <ExamForm :form="form" :errors="errors" :isSaving="isSaving" @save="saveExam" />
        </DialogContent>
      </Dialog>

      <Drawer v-else v-model:open="isEditOpen">
        <DrawerContent>
          <DrawerHeader>
            <DrawerTitle>Edit Exam</DrawerTitle>
            <DrawerDescription>Update the details of the exam.</DrawerDescription>
          </DrawerHeader>
          <ExamForm :form="form" :errors="errors" :isSaving="isSaving" @save="saveExam" />
        </DrawerContent>
      </Drawer>
    </section>
  </AppLayout>
</template>
