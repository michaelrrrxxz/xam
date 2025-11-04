<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useUsers } from '@/composables/user/useUsers';
import { useUserForm } from '@/composables/user/useUserForm';
import { Plus } from 'lucide-vue-next';
// ✅ shadcn/ui imports
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
import { Skeleton } from '@/components/ui/skeleton';

// ✅ icons
import { Pencil, Trash, Ellipsis, BadgeQuestionMark } from 'lucide-vue-next';

// ✅ layout & child components
import AppLayout from '@/layouts/AppLayout.vue';
import UserForm from '@/components/UserForm.vue';

import { useMediaQuery } from '@vueuse/core';

const isDesktop = useMediaQuery('(min-width: 768px)');
// ✅ composables
const { users, isLoading, searchQuery, filteredUsers, deleteUser } = useUsers();
const { form, errors, isSaving, isOpen, isEditOpen, openModal, openEditModal, saveUser } =
  useUserForm();

const router = useRouter();
const openQuestionsPage = (id: number) => router.push(`/questions/${id}`);
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
          <UserForm v-model:form="form" :errors="errors" :isSaving="isSaving" @save="saveUser" />
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
          <UserForm v-model:form="form" :errors="errors" :isSaving="isSaving" @save="saveUser" />
        </DrawerContent>
      </Drawer>

      <Dialog v-if="isDesktop" v-model:open="isEditOpen">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Edit User</DialogTitle>
            <DialogDescription>Update the details of the user.</DialogDescription>
          </DialogHeader>
          <UserForm v-model:form="form" :errors="errors" :isSaving="isSaving" @save="saveUser" />
        </DialogContent>
      </Dialog>

      <Drawer v-else v-model:open="isEditOpen">
        <DrawerContent>
          <DrawerHeader>
            <DrawerTitle>Edit User</DrawerTitle>
            <DrawerDescription>Update the details of the user.</DrawerDescription>
          </DrawerHeader>
          <UserForm v-model:form="form" :errors="errors" :isSaving="isSaving" @save="saveUser" />
        </DrawerContent>
      </Drawer>
    </section>
  </AppLayout>
</template>
