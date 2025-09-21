<script lang="ts" setup>
import { useMediaQuery } from '@vueuse/core';
import { useRouter } from 'vue-router';
import { Pencil, Trash, Ellipsis, Plus, Lock, Unlock, RatioIcon, Printer } from 'lucide-vue-next';

import AppLayout from '@/layouts/AppLayout.vue';
import BatchForm from '@/components/BatchForm.vue';

// Shadcn UI
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
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import { Button } from '@/components/ui/button';

// Composables
import { useBatch } from '@/composables/batch/useBatch';
import { useBatchFilters } from '@/composables/batch/useBatchFilters';
import { Batch } from '@/src/types';

// useBatch → CRUD + API
const {
  batch,
  isLoading,
  isSaving,
  errors,
  form,
  isOpen,
  isEditOpen,
  openModal,
  openEditModal,
  saveBatch,
  //   deleteBatch,
  lockBatch,
  activateBatch,
} = useBatch();

// useBatchFilters → search, sort, filter, grouping
const { searchQuery, yearFilter, availableYears, filteredBatch, groupedByYear, toggleSort } =
  useBatchFilters(batch);

const isDesktop = useMediaQuery('(min-width: 768px)');
const router = useRouter();

// Extra actions
const print = (id: number) => {
  window.open(`batch/results/${id}/print`, '_blank');
};

const goToResults = (id: number) => {
  router.push({ name: 'ResultsByBatch', params: { id } });
};
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

        <div class="flex items-center gap-2">
          <Select v-model="yearFilter">
            <SelectTrigger class="w-[180px]">
              <SelectValue placeholder="Select year" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectLabel>Filter by Year</SelectLabel>
                <SelectItem value="all">All Years</SelectItem>
                <SelectItem v-for="y in availableYears" :key="y" :value="y.toString()">
                  {{ y }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
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
              <TableHead @click="() => toggleSort('year')">Year</TableHead>
              <TableHead @click="() => toggleSort('name')">Name</TableHead>
              <TableHead>Description</TableHead>
              <TableHead>Access Key</TableHead>
              <TableHead>Duration</TableHead>
              <TableHead>Status</TableHead>
              <TableHead></TableHead>
            </TableRow>
          </TableHeader>

          <TableBody>
            <template v-if="isLoading">
              <TableRow v-for="n in 1" :key="n">
                <TableCell><Skeleton class="h-4 w-32 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-32 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-48 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-24 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-20 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-10 rounded" /></TableCell>
              </TableRow>
            </template>

            <template v-else>
              <template
                v-for="(group, year) in groupedByYear(filteredBatch as Batch[])"
                :key="year"
              >
                <TableRow v-for="(b, index) in group" :key="b.id">
                  <!-- Year cell only on first row of this group -->
                  <TableCell
                    v-if="index === 0"
                    :rowspan="group.length"
                    class="text-center font-bold"
                  >
                    {{ year }}
                  </TableCell>

                  <TableCell>{{ b.name }}</TableCell>
                  <TableCell>{{ b.description }}</TableCell>
                  <TableCell>{{ b.access_key }}</TableCell>
                  <TableCell>45 Minutes</TableCell>
                  <TableCell>
                    <Lock v-if="b.isLocked" class="w-5 h-5 text-red-500" />
                    <Unlock v-else class="w-5 h-5 text-green-500" />
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

                        <DropdownMenuItem @click="goToResults(b.id)">
                          <RatioIcon class="w-4 h-4 mr-2" /> Results
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="print(b.id)">
                          <Printer class="w-4 h-4 mr-2" /> Print
                        </DropdownMenuItem>
                        <DropdownMenuItem v-if="!b.isLocked" @click="lockBatch(b.id)">
                          <Lock class="w-4 h-4 mr-2" /> Lock
                        </DropdownMenuItem>
                        <DropdownMenuItem v-else @click="activateBatch(b.id)">
                          <Unlock class="w-4 h-4 mr-2" /> Activate
                        </DropdownMenuItem>

                        <DropdownMenuItem @click="openEditModal(b.id)">
                          <Pencil class="w-4 h-4 mr-2" /> Edit
                        </DropdownMenuItem>
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </TableCell>
                </TableRow>
              </template>

              <TableRow v-if="!filteredBatch.length">
                <TableCell colspan="7" class="text-center">No data available.</TableCell>
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
