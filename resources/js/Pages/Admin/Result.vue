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
                <TableHead>ID Number</TableHead>
              <TableHead>Batch</TableHead>
              <TableHead>Student Name</TableHead>
              <TableHead>Verbal Comprehension</TableHead>
              <TableHead>Verbal Reasoning</TableHead>
              <TableHead>Figural Reasoning</TableHead>
              <TableHead>Quantitative Reasoning</TableHead>
              <TableHead>Raw Score</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody>
            <!-- Loading State -->
            <template v-if="isLoading">
              <TableRow v-for="n in 3" :key="n">
                <TableCell><Skeleton class="h-4 w-32 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-48 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-24 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-16 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-16 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-16 rounded" /></TableCell>
                <TableCell><Skeleton class="h-4 w-16 rounded" /></TableCell>
              </TableRow>
            </template>

            <!-- Loaded State -->
            <template v-else>
              <TableRow v-for="r in filteredResults" :key="r.id">
                  <TableCell>{{ r.student.id_number }}</TableCell>
                <TableCell>{{ r.batch.name }}</TableCell>
                <TableCell>
                  {{ r.student.last_name }}, {{ r.student.first_name }} {{ r.student.middle_name }}
                </TableCell>
                <TableCell>{{ r.verbal_comprehension }}</TableCell>
                <TableCell>{{ r.verbal_reasoning }}</TableCell>
                <TableCell>{{ r.figural_reasoning }}</TableCell>
                <TableCell>{{ r.quantitative_reasoning }}</TableCell>
                <TableCell>{{ r.total_score }}</TableCell>
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
import { ref, onMounted, computed } from 'vue'
import api from '@/Api/Axios'
import { Button } from '@/components/ui/button'
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/components/ui/table'
import { Skeleton } from '@/components/ui/skeleton'
import AppLayout from '@/layouts/AppLayout.vue'
import { Plus } from 'lucide-vue-next'

/* ---------------------- Types ---------------------- */
interface Student {
  id: number
  id_number: string
  last_name: string
  first_name: string
  middle_name: string
  birth_day: string
  course: string
  gender: string
}

interface Batch {
  id: number
  name: string
}

interface Result {
  id: number
  total_score: number
  verbal_reasoning: number
  verbal_comprehension: number
  quantitative_reasoning: number
  figural_reasoning: number
  batch: Batch
  student: Student
}

/* ---------------------- State ---------------------- */
const result = ref<Result[]>([])
const searchQuery = ref('')
const isLoading = ref(true)

/* ---------------------- Fetch Data ---------------------- */
const fetchResult = async () => {
  isLoading.value = true
  try {
    const response = await api.get('/admin/result')
    result.value = response.data
  } catch (error) {
    console.warn('Error fetching results:', error)
  } finally {
    isLoading.value = false
  }
}

/* ---------------------- Computed ---------------------- */
const filteredResults = computed(() => {
  if (!searchQuery.value) return result.value
  const query = searchQuery.value.toLowerCase()
  return result.value.filter(r =>
    `${r.student.last_name} ${r.student.first_name} ${r.student.middle_name}`.toLowerCase().includes(query) ||
    r.student.id_number.toLowerCase().includes(query)
  )
})

/* ---------------------- Lifecycle ---------------------- */
onMounted(fetchResult)
</script>

<style scoped>
/* Smooth pulsing animation for placeholders */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.6; }
}
.animate-pulse {
  animation: pulse 1.5s ease-in-out infinite;
}
</style>
