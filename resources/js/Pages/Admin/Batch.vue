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

import { Skeleton } from '@/components/ui/skeleton';

import { Button } from '@/components/ui/button';
import { toast } from 'vue-sonner';
import 'vue-sonner/style.css';

import { Pencil, Trash, Ellipsis, Plus, Lock, Unlock } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import BatchForm from '@/components/BatchForm.vue';

import { useRouter } from 'vue-router';

const router = useRouter();

// Batch interface
interface Batch {
  id: number;
  name: string;
  description: string | null;
  access_key: number;
  isLocked: boolean;
}

// Reactive States
const batch = ref<Batch[]>([]);
const isOpen = ref(false);
const isEditOpen = ref(false);
const isSaving = ref(false);
const errors = ref<{ name?: string }>({});
const isDesktop = useMediaQuery('(min-width: 768px)');
const searchQuery = ref('');
const isLoading = ref(true);

// Unified form state
const form = reactive<{ id: number | null; name: string; description: string }>({
  id: null,
  name: '',
  description: '',
});

// Fetch all batch
const fetchBatch = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('admin/batch');
    batch.value = response.data;
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

// Open Edit Modal
const openEditModal = async (id: number) => {
  const response = await api.get(`admin/batch/${id}`);
  const batch = response.data;
  if (batch) {
    form.id = batch.id;
    form.name = batch.name;
    form.description = batch.description || '';
    isEditOpen.value = true;
  }
};

// Reset form
const resetForm = () => {
  form.id = null;
  form.name = '';
  form.description = '';
};

const lockBatch = (id: number) => {
  toast('Are you sure?', {
    position: 'top-center',
    description: 'Lock this Batch.',
    action: {
      label: 'Confirm',
      onClick: async () => {
        try {
          await api.put(`admin/batch/${id}/lock`);
          toast.success('Batch locked successfully!');
          await fetchBatch();
        } catch {
          toast.error('Failed to locked batch.');
        }
      },
    },
  });
};
const activateBatch = (id: number) => {
  toast('Are you sure?', {
    position: 'top-center',
    description: 'Activate this batch.',
    action: {
      label: 'Confirm',
      onClick: async () => {
        try {
          await api.put(`admin/batch/${id}/activate`);
          toast.success('Batch activated successfully!');
          await fetchBatch();
        } catch (error: any) {
          if (error.response?.status === 422) {
            toast.error(error.response.data?.message || 'An active batch already exists.');
          } else {
            toast.error('Failed to activate batch. Please try again.');
          }
        }
      },
    },
  });
};

// Save Batch (Add or Update)
const saveBatch = async () => {
  errors.value = {};
  isSaving.value = true;
  try {
    if (form.id) {
      // Update existing batch
      await api.put(`admin/batch/${form.id}`, {
        name: form.name,
        description: form.description,
      });
      toast.success('Batch updated successfully!');
    } else {
      // Create new batch
      await api.post('admin/batch', {
        name: form.name,
        description: form.description,
      });
      toast.success('Batch added successfully!');
    }

    // Refresh the batch list after saving
    await fetchBatch();
    resetForm();
    isOpen.value = false;
    isEditOpen.value = false;
  } catch (error: any) {
    if (error.response?.status === 422) {
      // Check if backend sent a validation or logic error
      const message = error.response.data?.message;

      if (message?.includes('Cannot create a new batch')) {
        // Specific message for active batch
        toast.error('You cannot add a new batch while there is an active batch.');
      } else if (error.response.data?.errors) {
        // Handle Laravel validation errors
        const backendErrors = error.response.data.errors;
        errors.value = Object.keys(backendErrors).reduce(
          (acc, key) => {
            acc[key] = backendErrors[key][0];
            return acc;
          },
          {} as { [key: string]: string }
        );
      } else {
        toast.error(message || 'Validation failed.');
      }
    } else {
      toast.error('Something went wrong.');
    }
  } finally {
    isSaving.value = false;
  }
};

// Delete Batch
const deleteBatch = async (id: number) => {
  toast('Are you sure?', {
    position: 'top-center',
    description: 'This will permanently delete the batch.',
    action: {
      label: 'Confirm',
      onClick: async () => {
        try {
          await api.delete(`admin/batch/${id}`);
          toast.success('Batch deleted successfully!');
          await fetchBatch();
        } catch {
          toast.error('Failed to delete batch.');
        }
      },
    },
  });
};

const filteredBatch = computed(() => {
  if (!searchQuery.value) return batch.value;
  return batch.value.filter((e) => e.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

// Fetch batch on mount
onMounted(fetchBatch);
</script>
<template>
  <AppLayout>
    <section class="flex flex-col md:p-6">
      <div class="flex items-center justify-between gap-4 p-4 border-b bg-card rounded-t-lg">
        <!-- Left: Title -->
        <!-- <h2 class="text-2xl font-bold text-foreground">Batch</h2> -->

        <!-- Center: Search -->
        <div class="flex-1 max-w-sm">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search batch..."
            class="w-full px-3 py-2 text-sm border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary"
          />
        </div>

        <!-- Right: Add Batch Button -->
        <Button size="sm" variant="secondary" class="flex items-center gap-2" @click="openModal">
          <Plus class="w-4 h-4" />
          Add Batch
        </Button>
      </div>
      <div class="w-full overflow-x-auto rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Name</TableHead>
              <TableHead>Description</TableHead>
              <TableHead>Access Key</TableHead>
              <TableHead>Status</TableHead>
              <TableHead></TableHead>
            </TableRow>
          </TableHeader>

          <TableBody>
            <template v-if="isLoading">
              <TableRow v-for="n in 1" :key="n">
                <TableCell><Skeleton class="h-4 w-32 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-48 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-24 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-20 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-10 rounded" /></TableCell>
              </TableRow>
            </template>
            <template v-else>
              <TableRow v-for="b in filteredBatch" :key="b.id">
                <TableCell>{{ b.name }}</TableCell>
                <TableCell>{{ b.description }}</TableCell>
                <TableCell>{{ b.access_key }}</TableCell>
                <TableCell>
                  <Lock v-if="b.isLocked" class="w-5 h-5 text-red-500" />
                  <Unlock v-else class="w-5 h-5 text-green-500" />
                  <!-- {{ b.isLocked ? 'Locked' : 'Active' }} -->
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

                      <DropdownMenuItem v-if="!b.isLocked" @click="lockBatch(b.id)">
                        <Lock class="w-4 h-4 mr-2" /> Lock
                      </DropdownMenuItem>
                      <DropdownMenuItem v-else @click="activateBatch(b.id)">
                        <Unlock class="w-4 h-4 mr-2" /> Activate
                      </DropdownMenuItem>

                      <DropdownMenuItem @click="openEditModal(b.id)">
                        <Pencil class="w-4 h-4 mr-2" /> Edit
                      </DropdownMenuItem>

                      <DropdownMenuItem @click="deleteBatch(b.id)" class="text-red-500">
                        <Trash class="w-4 h-4 mr-2" /> Delete
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </TableCell>
              </TableRow>

              <TableRow v-if="!batch.length">
                <TableCell colspan="3" class="text-center"> No data available. </TableCell>
              </TableRow>
            </template>
          </TableBody>
        </Table>
      </div>

      <!-- Add Batch -->
      <Dialog v-if="isDesktop" v-model:open="isOpen">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Add Batch</DialogTitle>
            <DialogDescription>Fill out the form to create a new batch.</DialogDescription>
          </DialogHeader>
          <!-- reuse your form -->
          <BatchForm :form="form" :errors="errors" :isSaving="isSaving" @save="saveBatch" />
        </DialogContent>
      </Dialog>

      <!-- Drawer for Mobile -->
      <Drawer v-else v-model:open="isOpen">
        <DrawerContent>
          <DrawerHeader>
            <DrawerTitle>Add Batch</DrawerTitle>
            <DrawerDescription>Fill out the form to create a new batch.</DrawerDescription>
          </DrawerHeader>
          <!-- reuse the same form -->
          <BatchForm :form="form" :errors="errors" :isSaving="isSaving" @save="saveBatch" />
        </DrawerContent>
      </Drawer>

      <Dialog v-if="isDesktop" v-model:open="isEditOpen">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Edit Batch</DialogTitle>
            <DialogDescription>Update the details of the batch.</DialogDescription>
          </DialogHeader>
          <BatchForm :form="form" :errors="errors" :isSaving="isSaving" @save="saveBatch" />
        </DialogContent>
      </Dialog>

      <Drawer v-else v-model:open="isEditOpen">
        <DrawerContent>
          <DrawerHeader>
            <DrawerTitle>Edit Batch</DrawerTitle>
            <DrawerDescription>Update the details of the batch.</DrawerDescription>
          </DrawerHeader>
          <BatchForm :form="form" :errors="errors" :isSaving="isSaving" @save="saveBatch" />
        </DrawerContent>
      </Drawer>
    </section>
  </AppLayout>
</template>
