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

        <Select v-model="ageFilter">
          <SelectTrigger class="w-[180px]">
            <SelectValue placeholder="Filter by age" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem value="all">All Ages</SelectItem>
              <SelectItem value="16below">16 or below</SelectItem>
              <SelectItem value="17">17</SelectItem>
              <SelectItem value="18above">18 and above</SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>

        <Select v-model="courseFilter">
          <SelectTrigger class="w-[180px]">
            <SelectValue placeholder="Filter by course" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem value="all">All Courses</SelectItem>
              <SelectItem v-for="course in uniqueCourses" :key="course" :value="course">
                {{ course }}
              </SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>

        <!-- Add Button -->
        <Button @click="fetchResult" size="sm" variant="secondary" class="flex items-center gap-2">
          Refresh
        </Button>
      </div>

      <!-- Results Table -->
      <div class="w-full overflow-x-auto rounded-md border mt-4">
        <Table>
          <TableHeader>
            <TableRow>
              <!-- Not sortable -->
              <TableHead rowspan="3" class="align-middle">ID No.</TableHead>

              <!-- Sortable: Name -->
              <TableHead
                rowspan="3"
                style="width: 200px"
                class="align-middle cursor-pointer"
                @click="() => toggleSort('name')"
              >
                Name
                <span v-if="sortKey === 'name'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </TableHead>

              <!-- Sortable: Course -->
              <TableHead
                rowspan="3"
                class="align-middle cursor-pointer"
                @click="() => toggleSort('course')"
              >
                Course
                <span v-if="sortKey === 'course'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </TableHead>

              <!-- Sortable: Raw Score -->
              <TableHead
                rowspan="3"
                class="align-middle cursor-pointer"
                @click="() => toggleSort('total_score')"
              >
                Raw Score
                <span v-if="sortKey === 'total_score'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </TableHead>

              <!-- Sortable: SAI -->
              <TableHead
                rowspan="3"
                class="align-middle cursor-pointer"
                title="Student Ability Index"
                @click="() => toggleSort('sai_t')"
              >
                SAI
                <span v-if="sortKey === 'sai_t'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </TableHead>

              <TableHead colspan="2" title="Performance By Age">PBA</TableHead>
              <TableHead colspan="2" title="Performance By Grade">PBG</TableHead>
              <TableHead colspan="5">Verbal</TableHead>
              <TableHead colspan="5">Non-Verbal</TableHead>
              <TableHead rowspan="3" class="align-middle"></TableHead>
            </TableRow>

            <TableRow>
              <!-- PBA -->
              <TableHead
                rowspan="2"
                class="align-middle cursor-pointer"
                title="Percentile Rank"
                @click="() => toggleSort('pba_pr_t')"
              >
                PR
                <span v-if="sortKey === 'pba_pr_t'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </TableHead>
              <TableHead
                rowspan="2"
                class="align-middle cursor-pointer"
                title="Stanine"
                @click="() => toggleSort('pba_s_t')"
              >
                S
                <span v-if="sortKey === 'pba_s_t'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </TableHead>

              <!-- PBG -->
              <TableHead
                rowspan="2"
                class="align-middle cursor-pointer"
                title="Percentile Rank"
                @click="() => toggleSort('pbg_pr_t')"
              >
                PR
                <span v-if="sortKey === 'pbg_pr_t'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </TableHead>
              <TableHead
                rowspan="2"
                class="align-middle cursor-pointer"
                title="Stanine"
                @click="() => toggleSort('pbg_s_t')"
              >
                S
                <span v-if="sortKey === 'pbg_s_t'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </TableHead>

              <!-- Verbal -->
              <TableHead colspan="2" title="Verbal Comprehension">VC</TableHead>
              <TableHead colspan="2" title="Verbal Reasoning">VR</TableHead>
              <TableHead
                rowspan="2"
                class="align-middle cursor-pointer"
                @click="() => toggleSort('verbal')"
              >
                Total
                <span v-if="sortKey === 'verbal'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </TableHead>

              <!-- Non-Verbal -->
              <TableHead colspan="2" title="Quantitative Reasoning">QR</TableHead>
              <TableHead colspan="2" title="Figural Reasoning">FR</TableHead>
              <TableHead
                rowspan="2"
                class="align-middle cursor-pointer"
                @click="() => toggleSort('non_verbal')"
              >
                Total
                <span v-if="sortKey === 'non_verbal'">{{ sortOrder === 'asc' ? '↑' : '↓' }}</span>
              </TableHead>
            </TableRow>

            <TableRow>
              <!-- VC -->
              <TableHead
                class="align-middle cursor-pointer"
                title="Score"
                @click="() => toggleSort('verbal_comprehension')"
              >
                S
                <span v-if="sortKey === 'verbal_comprehension'">{{
                  sortOrder === 'asc' ? '↑' : '↓'
                }}</span>
              </TableHead>
              <TableHead
                class="align-middle cursor-pointer"
                title="Performance Category"
                @click="() => toggleSort('verbal_comprehension_category')"
              >
                PC
                <span v-if="sortKey === 'verbal_comprehension_category'">{{
                  sortOrder === 'asc' ? '↑' : '↓'
                }}</span>
              </TableHead>

              <!-- VR -->
              <TableHead
                class="align-middle cursor-pointer"
                title="Score"
                @click="() => toggleSort('verbal_reasoning')"
              >
                S
                <span v-if="sortKey === 'verbal_reasoning'">{{
                  sortOrder === 'asc' ? '↑' : '↓'
                }}</span>
              </TableHead>
              <TableHead
                class="align-middle cursor-pointer"
                title="Performance Category"
                @click="() => toggleSort('verbal_reasoning_category')"
              >
                PC
                <span v-if="sortKey === 'verbal_reasoning_category'">{{
                  sortOrder === 'asc' ? '↑' : '↓'
                }}</span>
              </TableHead>

              <!-- QR -->
              <TableHead
                class="align-middle cursor-pointer"
                title="Score"
                @click="() => toggleSort('quantitative_reasoning')"
              >
                S
                <span v-if="sortKey === 'quantitative_reasoning'">{{
                  sortOrder === 'asc' ? '↑' : '↓'
                }}</span>
              </TableHead>
              <TableHead
                class="align-middle cursor-pointer"
                title="Performance Category"
                @click="() => toggleSort('quantitative_reasoning_category')"
              >
                PC
                <span v-if="sortKey === 'quantitative_reasoning_category'">{{
                  sortOrder === 'asc' ? '↑' : '↓'
                }}</span>
              </TableHead>

              <!-- FR -->
              <TableHead
                class="align-middle cursor-pointer"
                title="Score"
                @click="() => toggleSort('figural_reasoning')"
              >
                S
                <span v-if="sortKey === 'figural_reasoning'">{{
                  sortOrder === 'asc' ? '↑' : '↓'
                }}</span>
              </TableHead>
              <TableHead
                class="align-middle cursor-pointer"
                title="Performance Category"
                @click="() => toggleSort('figural_reasoning_category')"
              >
                PC
                <span v-if="sortKey === 'figural_reasoning_category'">{{
                  sortOrder === 'asc' ? '↑' : '↓'
                }}</span>
              </TableHead>
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
                <TableCell><Skeleton class="h-4 w-12 rounded" /></TableCell>
              </TableRow>
            </template>

            <!-- Loaded State -->
            <template v-else>
              <TableRow v-for="r in filteredResults" :key="r.id">
                <!-- <TableCell>
                  {{ calculateAgeAtExam(r.student.birth_day, r.created_at) }}
                </TableCell> -->

                <TableCell>{{ r.student.id_number }}</TableCell>
                <TableCell>
                  {{ r.student.last_name }}, {{ r.student.first_name }} {{ r.student.middle_name }}
                </TableCell>
                <TableCell>{{ r.student.course }}</TableCell>
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
                <TableCell>
                  <Button @click="print(r.id)" class="flex items-center gap-2">
                    <Printer class="w-4 h-4" />
                    Print
                  </Button></TableCell
                >
              </TableRow>

              <!-- No Data -->
              <TableRow v-if="!filteredResults.length">
                <TableCell colspan="20" class="text-center py-4 text-muted">
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
import {
  Select,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectGroup,
  SelectItem,
} from '@/components/ui/select';

import { Skeleton } from '@/components/ui/skeleton';
import AppLayout from '@/layouts/AppLayout.vue';
import { Printer } from 'lucide-vue-next';
import dayjs from 'dayjs';
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
  created_at: string;
}

/* ---------------------- State ---------------------- */
const result = ref<Result[]>([]);
const searchQuery = ref('');
const isLoading = ref(true);
const ageFilter = ref<'all' | '16below' | '17' | '18above'>('all');
const courseFilter = ref<Student['course'] | 'all'>('all');
const sortKey = ref<string>(''); // empty = no sorting
const sortOrder = ref<'asc' | 'desc'>('asc');

const toggleSort = (key: string) => {
  if (sortKey.value === key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortKey.value = key;
    sortOrder.value = 'asc';
  }
};

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

const print = async (id: number) => {
  window.open(`/results/${id}/print`, '_blank');
};

/* ---------------------- Computed ---------------------- */
const filteredResults = computed(() => {
  let results = result.value;

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    results = results.filter(
      (r) =>
        `${r.student.last_name} ${r.student.first_name} ${r.student.middle_name}`
          .toLowerCase()
          .includes(query) || r.student.id_number.toLowerCase().includes(query)
    );
  }

  // Age filter
  if (ageFilter.value !== 'all') {
    results = results.filter((r) => {
      const age = calculateAgeAtExam(r.student.birth_day, r.created_at);
      if (ageFilter.value === '16below') return age <= 16;
      if (ageFilter.value === '17') return age === 17;
      if (ageFilter.value === '18above') return age >= 18;
      return true;
    });
  }

  // Course filter
  if (courseFilter.value !== 'all') {
    results = results.filter((r) => r.student.course === courseFilter.value);
  }

  // Sorting
  if (sortKey.value) {
    results = [...results].sort((a, b) => {
      let valA: string | number = '';
      let valB: string | number = '';

      switch (sortKey.value) {
        case 'name':
          valA = `${a.student.last_name} ${a.student.first_name}`.toLowerCase();
          valB = `${b.student.last_name} ${b.student.first_name}`.toLowerCase();
          break;
        case 'course':
          valA = a.student.course.toLowerCase();
          valB = b.student.course.toLowerCase();
          break;
        default:
          // direct property match
          valA = (a as any)[sortKey.value] ?? (a.student as any)[sortKey.value] ?? '';
          valB = (b as any)[sortKey.value] ?? (b.student as any)[sortKey.value] ?? '';
      }

      // Normalize strings
      if (typeof valA === 'string') valA = valA.toLowerCase();
      if (typeof valB === 'string') valB = valB.toLowerCase();

      if (valA < valB) return sortOrder.value === 'asc' ? -1 : 1;
      if (valA > valB) return sortOrder.value === 'asc' ? 1 : -1;
      return 0;
    });
  }

  return results;
});

const uniqueCourses = computed(() => {
  const courses = result.value.map((r) => r.student.course);
  return Array.from(new Set(courses));
});

function calculateAgeAtExam(birthDay: string, examDate: string): number {
  const birthDate = new Date(birthDay);
  const exam = new Date(examDate);

  let age = exam.getFullYear() - birthDate.getFullYear();
  const m = exam.getMonth() - birthDate.getMonth();
  if (m < 0 || (m === 0 && exam.getDate() < birthDate.getDate())) {
    age--;
  }
  return age;
}

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
