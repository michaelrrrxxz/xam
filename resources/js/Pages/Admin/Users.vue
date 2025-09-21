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
import UserForm from '@/components/UserForm.vue';

import { useRouter } from 'vue-router';

import { Skeleton } from '@/components/ui/skeleton';

const router = useRouter();

// User interface
import { User } from '@/types';

// Reactive States
const users = ref<User[]>([]);
const isOpen = ref(false);
const isEditOpen = ref(false);
const isSaving = ref(false);
const errors = ref<{ name?: string }>({});
const isDesktop = useMediaQuery('(min-width: 768px)');
const searchQuery = ref('');
const isLoading = ref(true);
// Unified form state
const form = reactive<{ id: number | null; name: string; email: string }>({
  id: null,
  name: '',
  email: '',
});

// Fetch all users
const fetchUsers = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('admin/user');
    users.value = response.data;
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
  const response = await api.get(`/user/${id}`);
  const user = response.data;
  if (user) {
    form.id = user.id;
    form.name = user.name;
    form.email = user.email || '';
    isEditOpen.value = true;
  }
};

// Reset form
const resetForm = () => {
  form.id = null;
  form.name = '';
  form.email = '';
};

const openQuestionsPage = (id: number) => {
  router.push(`/questions/${id}`);
};

// Save User (Add or Update)
const saveUser = async () => {
  errors.value = {};
  isSaving.value = true;
  try {
    if (form.id) {
      // Update existing user
      await api.put(`admin/user/${form.id}`, {
        name: form.name,
        email: form.email,
      });
      toast.success('User updated successfully!');
    } else {
      // Create new user
      await api.post('admin/user', {
        name: form.name,
        email: form.email,
      });
      toast.success('User added successfully!');
    }
    await fetchUsers();
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

// Delete User
const deleteUser = async (id: number) => {
  toast('Are you sure?', {
    position: 'top-center',
    description: 'This will permanently delete the user.',
    action: {
      label: 'Confirm',
      onClick: async () => {
        try {
          await api.delete(`/user/${id}`);
          toast.success('User deleted successfully!');
          await fetchUsers();
        } catch {
          toast.error('Failed to delete user.');
        }
      },
    },
  });
};

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value;
  return users.value.filter((e) => e.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

// Fetch users on mount
onMounted(fetchUsers);
</script>
<template>
  <AppLayout>
    <section class="flex flex-col md:p-6">
      <div class="flex items-center justify-between gap-4 p-4 border-b bg-card rounded-t-lg">
        <!-- Left: Title -->
        <!-- <h2 class="text-2xl font-bold text-foreground">Users</h2> -->

        <!-- Center: Search -->
        <div class="flex-1 max-w-sm">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search users..."
            class="w-full px-3 py-2 text-sm border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary"
          />
        </div>

        <!-- Right: Add User Button -->
        <Button size="sm" variant="secondary" class="flex items-center gap-2" @click="openModal">
          <Plus class="w-4 h-4" />
          Add User
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
            <template v-if="isLoading">
              <TableRow v-for="n in 1" :key="n">
                <TableCell><Skeleton class="h-4 w-32 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-32 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-4 rounded" /></TableCell>
              </TableRow>
            </template>
            <template v-else>
              <TableRow v-for="e in filteredUsers" :key="e.id">
                <TableCell>{{ e.name }}</TableCell>
                <TableCell>{{ e.email }}</TableCell>
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

                      <DropdownMenuItem @click="deleteUser(e.id)" class="text-red-500">
                        <Trash class="w-4 h-4 mr-2" /> Delete
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </TableCell>
              </TableRow>
              <TableRow v-if="!users.length">
                <TableCell colspan="3" class="text-center"> No data available. </TableCell>
              </TableRow>
            </template>
          </TableBody>
        </Table>
      </div>

      <!-- Add User -->
      <Dialog v-if="isDesktop" v-model:open="isOpen">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Add User</DialogTitle>
            <DialogDescription>Fill out the form to create a new user.</DialogDescription>
          </DialogHeader>
          <!-- reuse your form -->
          <UserForm :form="form" :errors="errors" :isSaving="isSaving" @save="saveUser" />
        </DialogContent>
      </Dialog>

      <!-- Drawer for Mobile -->
      <Drawer v-else v-model:open="isOpen">
        <DrawerContent>
          <DrawerHeader>
            <DrawerTitle>Add User</DrawerTitle>
            <DrawerDescription>Fill out the form to create a new user.</DrawerDescription>
          </DrawerHeader>
          <!-- reuse the same form -->
          <UserForm :form="form" :errors="errors" :isSaving="isSaving" @save="saveUser" />
        </DrawerContent>
      </Drawer>

      <Dialog v-if="isDesktop" v-model:open="isEditOpen">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Edit User</DialogTitle>
            <DialogDescription>Update the details of the user.</DialogDescription>
          </DialogHeader>
          <UserForm :form="form" :errors="errors" :isSaving="isSaving" @save="saveUser" />
        </DialogContent>
      </Dialog>

      <Drawer v-else v-model:open="isEditOpen">
        <DrawerContent>
          <DrawerHeader>
            <DrawerTitle>Edit User</DrawerTitle>
            <DrawerDescription>Update the details of the user.</DrawerDescription>
          </DrawerHeader>
          <UserForm :form="form" :errors="errors" :isSaving="isSaving" @save="saveUser" />
        </DrawerContent>
      </Drawer>
    </section>
  </AppLayout>
</template>
