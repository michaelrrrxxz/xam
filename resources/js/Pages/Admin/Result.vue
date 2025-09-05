<template>
  <AppLayout>
    <section class="flex flex-col md:p-6">
      <!-- Header -->
      <div class="flex items-center justify-between gap-4 p-4 border-b bg-card rounded-t-lg">
        <!-- Search Box -->
        <div class="flex-1 max-w-sm">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by student name or ID..."
            class="w-full px-3 py-2 text-sm border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary"
          />
        </div>

        <!-- Add Button -->
        <Button size="sm" variant="secondary" class="flex items-center gap-2">
          <Plus class="w-4 h-4" />
          Add Result
        </Button>
      </div>

      <!-- Results Table -->
      <div class="w-full overflow-x-auto rounded-md border mt-4">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead rowspan="3" class="align-middle">ID No.</TableHead>
              <TableHead rowspan="3" class="align-middle">Course</TableHead>
              <TableHead rowspan="3" style="width: 200px" class="align-middle">Name</TableHead>
              <TableHead rowspan="3" class="align-middle" title="Raw Score">Raw Score</TableHead>
              <TableHead rowspan="3" class="align-middle" title="Student Ability Index"
                >SAI</TableHead
              >
              <TableHead colspan="2" title="Performance By Age">PBA</TableHead>
              <TableHead colspan="2" title="Performance By Grade">PBG</TableHead>
              <TableHead colspan="5">Verbal</TableHead>
              <TableHead colspan="5">Non-Verbal</TableHead>
            </TableRow>
            <TableRow>
              <TableHead rowspan="2" class="align-middle" title="Percentile Rank">PR</TableHead>
              <TableHead rowspan="2" class="align-middle" title="Stanine">S</TableHead>
              <TableHead rowspan="2" class="align-middle" title="Percentile Rank">PR</TableHead>
              <TableHead rowspan="2" class="align-middle" title="Stanine">S</TableHead>
              <TableHead colspan="2" title="Verbal Comprehension">VC</TableHead>
              <TableHead colspan="2" title="Verbal Reasoning">VR</TableHead>
              <TableHead rowspan="2" class="align-middle">Total</TableHead>
              <TableHead colspan="2" title="Quantitative Reasoning">QR</TableHead>
              <TableHead colspan="2" title="Figural Reasoning">FR</TableHead>
              <TableHead rowspan="2" class="align-middle">Total</TableHead>
            </TableRow>
            <TableRow>
              <TableHead class="align-middle" title="Score">S</TableHead>
              <TableHead class="align-middle" title="Performance Category">PC</TableHead>
              <TableHead class="align-middle" title="Score">S</TableHead>
              <TableHead class="align-middle" title="Performance Category">PC</TableHead>
              <TableHead class="align-middle" title="Score">S</TableHead>
              <TableHead class="align-middle" title="Performance Category">PC</TableHead>
              <TableHead class="align-middle" title="Score">S</TableHead>
              <TableHead class="align-middle" title="Performance Category">PC</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody>
            <!-- Loading State -->
            <template v-if="isLoading">
              <TableRow v-for="n in 3" :key="n">
                <TableCell><Skeleton class="h-4 w-16 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-12 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-36 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-6 rounded" /></TableCell>
              </TableRow>
            </template>

            <!-- Loaded State -->
            <template v-else>
              <TableRow v-for="r in filteredResults" :key="r.id">
                <TableCell>{{ r.student.id_number }}</TableCell>
                <TableCell>{{ r.student.course }}</TableCell>
                <TableCell>
                  {{ r.student.last_name }}, {{ r.student.first_name }} {{ r.student.middle_name }}
                </TableCell>
                <TableCell>{{ r.total_score }}</TableCell>
                <TableCell>{{ r.sai_t }}</TableCell>
                <TableCell>{{ r.pba_pr_t }}</TableCell>
                <TableCell>{{ r.pba_s_t }}</TableCell>
                <TableCell>{{ r.pbg_pr_t }}</TableCell>
                <TableCell>{{ r.pbg_s_t }}</TableCell>
                <TableCell>{{ r.verbal_comprehension }}</TableCell>
                <TableCell>{{ r.verbal_comprehension_category }}</TableCell>
                <TableCell>{{ r.verbal_reasoning }}</TableCell>
                <TableCell>{{ r.verbal_reasoning_category }}</TableCell>
                <TableCell>{{ r.verbal }}</TableCell>
                <TableCell>{{ r.quantitative_reasoning }}</TableCell>
                <TableCell>{{ r.quantitative_reasoning_category }}</TableCell>
                <TableCell>{{ r.figural_reasoning }}</TableCell>
                <TableCell>{{ r.figural_reasoning_category }}</TableCell>
                <TableCell>{{ r.non_verbal }}</TableCell>
              </TableRow>

              <!-- No Data -->
              <TableRow v-if="!filteredResults.length">
                <TableCell colspan="4" class="text-center py-4 text-muted">
                  No data available.
                </TableCell>
              </TableRow>
            </template>
          </TableBody>
        </Table>
      </div>
    </section>
  </AppLayout>
</template>

<script lang="ts" setup>
import { ref, onMounted, computed } from 'vue';
import api from '@/Api/Axios';
import { Button } from '@/components/ui/button';
import {
  Table,
  TableHeader,
  TableRow,
  TableHead,
  TableBody,
  TableCell,
} from '@/components/ui/table';
import { Skeleton } from '@/components/ui/skeleton';
import AppLayout from '@/layouts/AppLayout.vue';
import { Plus } from 'lucide-vue-next';

/* ---------------------- Types ---------------------- */
interface Student {
  id: number;
  id_number: string;
  last_name: string;
  first_name: string;
  middle_name: string;
  birth_day: string;
  course: string;
  gender: string;
}

interface Batch {
  id: number;
  name: string;
}

interface Result {
  // Student Information
  student: Student;
  batch: Batch;
  id: number;

  total_score: number;
  sai_t: number;
  pba_pr_t: number;
  pba_s_t: number;
  pbg_pr_t: number;
  pbg_s_t: number;
  verbal: number;
  non_verbal: number;

  verbal_comprehension: number;
  verbal_comprehension_category: string;

  verbal_reasoning: number;
  verbal_reasoning_category: string;

  quantitative_reasoning: number;
  quantitative_reasoning_category: string;

  figural_reasoning: number;
  figural_reasoning_category: string;
}

/* ---------------------- State ---------------------- */
const result = ref<Result[]>([]);
const searchQuery = ref('');
const isLoading = ref(true);

/* ---------------------- Fetch Data ---------------------- */
const fetchResult = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('/admin/result');
    result.value = response.data;
  } catch (error) {
    console.warn('Error fetching results:', error);
  } finally {
    isLoading.value = false;
  }
};

/* ---------------------- Computed ---------------------- */
const filteredResults = computed(() => {
  if (!searchQuery.value) return result.value;
  const query = searchQuery.value.toLowerCase();
  return result.value.filter(
    (r) =>
      `${r.student.last_name} ${r.student.first_name} ${r.student.middle_name}`
        .toLowerCase()
        .includes(query) || r.student.id_number.toLowerCase().includes(query)
  );
});

/* ---------------------- Lifecycle ---------------------- */
onMounted(fetchResult);
</script>

<style scoped>
/* Smooth pulsing animation for placeholders */
@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.6;
  }
}
.animate-pulse {
  animation: pulse 1.5s ease-in-out infinite;
}
</style>
