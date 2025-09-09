<script lang="ts" setup>
import { ref, onMounted, reactive, computed } from 'vue';
import api from '@/Api/Axios';
import { useMediaQuery } from '@vueuse/core';
import { useRoute } from 'vue-router';
import { Skeleton } from '@/components/ui/skeleton';
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
import { Button } from '@/components/ui/button';
import { toast } from 'vue-sonner';
import 'vue-sonner/style.css';

import { Pencil, Trash, Ellipsis, BadgeQuestionMark, Plus } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import EnrolledStudentForm from '@/components/EnrolledStudentForm.vue';
import UploadForm from '@/components/UploadForm.vue';

import { useRouter } from 'vue-router';

const router = useRouter();

// EnrolledStudent interface
interface EnrolledStudent {
  id: number;
  last_name: string;
  first_name: string;
  middle_name: string | null;
  id_number: string;
  course: string;
  birth_day: string;
  gender: string;
}

interface UploadForm {
  upload_file: File | null;
}

// Reactive States
const enrolledStudents = ref<EnrolledStudent[]>([]);

const isOpen = ref(false);
const isUploadOpen = ref(false);
const isEditOpen = ref(false);
const isSaving = ref(false);
const sortKey = ref<'id_number' | 'first_name' | 'last_name'>('id_number');
const sortOrder = ref<'asc' | 'desc'>('asc');
const errors = ref<
  Partial<
    Record<'id_number' | 'last_name' | 'first_name' | 'birth_day' | 'course' | 'gender', string>
  >
>({});

const isLoading = ref(false);

const isDesktop = useMediaQuery('(min-width: 768px)');
const searchQuery = ref('');

const uploadForm = reactive({
  upload_file: null as File | null,
});

const isUploadMode = ref(true);
const toggleMode = (val: boolean) => {
  isUploadMode.value = val;
  resetForm();
};

// Unified form state
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

// Fetch all enrolledStudents
const fetchEnrolledStudents = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('admin/enrolledStudent');
    enrolledStudents.value = response.data;
  } catch (error) {
    console.warn(error);
  } finally {
    isLoading.value = false;
  }
};

// Open Add Modal
const openModal = () => {
  resetForm();
  isOpen.value = true;
};
const openUploadModal = () => {
  resetForm();
  isUploadOpen.value = true;
};

// Open Edit Modal
const openEditModal = async (id: number) => {
  const response = await api.get(`admin/enrolledStudent/${id}`);
  const enrolledStudent = response.data;
  if (enrolledStudent) {
    form.id = enrolledStudent.id;
    form.id_number = enrolledStudent.id_number;
    form.course = enrolledStudent.course;
    isEditOpen.value = true;
  }
};

// Reset form
const resetForm = () => {
  form.id = null;
  form.id_number = '';
  form.course = '';
};

const openQuestionsPage = (id: number) => {
  router.push(`/questions/${id}`);
};

// Save EnrolledStudent (Add or Update)
const saveEnrolledStudent = async () => {
  errors.value = {};
  isSaving.value = true;
  try {
    if (form.id) {
      // Update existing enrolledStudent
      await api.put(`admin/enrolledStudent/${form.id}`, {
        id_number: form.id_number,
        course: form.course,
      });
      toast.success('EnrolledStudent updated successfully!');
    } else {
      // Create new enrolledStudent
      await api.post('admin/enrolledStudent', {
        id_number: form.id_number,
        course: form.course,
        first_name: form.first_name,
        last_name: form.last_name,
        middle_name: form.middle_name,
        birth_day: form.birth_day,
        gender: form.gender,
      });
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

const uploadEnrolledStudent = async (formData: FormData) => {
  isSaving.value = true;
  try {
    await api.post('admin/enrolled-students/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    toast.success('Students uploaded successfully!');
    await fetchEnrolledStudents();
    isUploadOpen.value = false;
  } catch (error) {
    toast.error('Failed to upload students.');
  } finally {
    isSaving.value = false;
  }
};

// Delete EnrolledStudent
const deleteEnrolledStudent = async (id: number) => {
  toast('Are you sure?', {
    position: 'top-center',
    description: 'This will permanently delete the enrolledStudent.',
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

const toggleSort = (key: typeof sortKey.value) => {
  if (sortKey.value === key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortKey.value = key;
    sortOrder.value = 'asc';
  }
};

const filteredEnrolledStudents = computed(() => {
  // start with search filtering
  let data = enrolledStudents.value;
  if (searchQuery.value) {
    data = data.filter((e) => e.id_number.toLowerCase().includes(searchQuery.value.toLowerCase()));
  }

  // then sort
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

// Fetch enrolledStudents on mount
onMounted(fetchEnrolledStudents);
</script>
<template>
  <AppLayout>
    <section class="flex flex-col md:p-6">
      <div class="flex items-center justify-between gap-4 p-4 border-b bg-card rounded-t-lg">
        <!-- Left: Title -->
        <!-- <h2 class="text-2xl font-bold text-foreground">EnrolledStudents</h2> -->

        <!-- Center: Search -->
        <div class="flex-1 max-w-sm">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search enrolledStudents..."
            class="w-full px-3 py-2 text-sm border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary"
          />
        </div>
        <div class="flex items-center gap-3">
          <!-- Right: Add EnrolledStudent Button -->
          <span class="text-sm">Form</span>
          <Switch :model-value="isUploadMode" @update:model-value="toggleMode" />
          <span class="text-sm">Upload</span>
          <Button
            size="sm"
            variant="secondary"
            class="flex items-center gap-2"
            @click="isUploadMode ? openUploadModal() : openModal()"
          >
            <Plus class="w-4 h-4" />
            {{ isUploadMode ? 'Upload Students' : 'Add Student' }}
          </Button>
        </div>
      </div>
      <div class="w-full overflow-x-auto rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead @click="() => toggleSort('id_number')" class="cursor-pointer">
                ID Number
                <span v-if="sortKey === 'id_number'">
                  {{ sortOrder === 'asc' ? '↑' : '↓' }}
                </span>
              </TableHead>
              <TableHead @click="() => toggleSort('last_name')" class="cursor-pointer">
                Name
                <span v-if="sortKey === 'last_name'">
                  {{ sortOrder === 'asc' ? '↑' : '↓' }}
                </span>
              </TableHead>
              <TableHead>Course</TableHead>
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
              <TableRow v-for="e in filteredEnrolledStudents" :key="e.id">
                <TableCell>{{ e.id_number }}</TableCell>
                <TableCell>{{ e.last_name }}, {{ e.first_name }} {{ e.middle_name }}</TableCell>
                <TableCell>{{ e.course }}</TableCell>
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

                      <DropdownMenuItem @click="deleteEnrolledStudent(e.id)" class="text-red-500">
                        <Trash class="w-4 h-4 mr-2" /> Delete
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </TableCell>
              </TableRow>
              <TableRow v-if="!enrolledStudents.length">
                <TableCell colspan="3" class="text-center"> No data available. </TableCell>
              </TableRow>
            </template>
          </TableBody>
        </Table>
      </div>

      <!-- Add EnrolledStudent -->
      <Dialog v-if="isDesktop" v-model:open="isOpen">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Add EnrolledStudent</DialogTitle>
            <DialogDescription
              >Fill out the form to create a new enrolledStudent.</DialogDescription
            >
          </DialogHeader>
          <!-- reuse your form -->
          <EnrolledStudentForm
            :form="form"
            :errors="errors"
            :isSaving="isSaving"
            @save="saveEnrolledStudent"
          />
        </DialogContent>
      </Dialog>
      <Dialog v-if="isDesktop" v-model:open="isUploadOpen">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Upload Enrolled Student</DialogTitle>
            <DialogDescription
              >Fill out the form to create a new enrolledStudent.</DialogDescription
            >
          </DialogHeader>
          <!-- reuse your form -->

          <UploadForm :errors="errors" :isSaving="isSaving" @save="uploadEnrolledStudent" />
        </DialogContent>
      </Dialog>

      <!-- Drawer for Mobile -->
      <Drawer v-else v-model:open="isOpen">
        <DrawerContent>
          <DrawerHeader>
            <DrawerTitle>Add EnrolledStudent</DrawerTitle>
            <DrawerDescription
              >Fill out the form to create a new enrolledStudent.</DrawerDescription
            >
          </DrawerHeader>
          <!-- reuse the same form -->
          <EnrolledStudentForm
            :form="form"
            :errors="errors"
            :isSaving="isSaving"
            @save="saveEnrolledStudent"
          />
        </DrawerContent>
      </Drawer>
      <Drawer v-else v-model:open="isUploadOpen">
        <DrawerContent>
          <DrawerHeader>
            <DrawerTitle>Add EnrolledStudent</DrawerTitle>
            <DrawerDescription
              >Fill out the form to create a new enrolledStudent.</DrawerDescription
            >
          </DrawerHeader>
          <!-- reuse the same form -->
          <UploadForm :errors="errors" :isSaving="isSaving" @save="uploadEnrolledStudent" />
        </DrawerContent>
      </Drawer>

      <Dialog v-if="isDesktop" v-model:open="isEditOpen">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Edit EnrolledStudent</DialogTitle>
            <DialogDescription>Update the details of the enrolledStudent.</DialogDescription>
          </DialogHeader>
          <EnrolledStudentForm
            :form="form"
            :errors="errors"
            :isSaving="isSaving"
            @save="saveEnrolledStudent"
          />
        </DialogContent>
      </Dialog>

      <Drawer v-else v-model:open="isEditOpen">
        <DrawerContent>
          <DrawerHeader>
            <DrawerTitle>Edit EnrolledStudent</DrawerTitle>
            <DrawerDescription>Update the details of the enrolledStudent.</DrawerDescription>
          </DrawerHeader>
          <EnrolledStudentForm
            :form="form"
            :errors="errors"
            :isSaving="isSaving"
            @save="saveEnrolledStudent"
          />
        </DrawerContent>
      </Drawer>
    </section>
  </AppLayout>
</template>
