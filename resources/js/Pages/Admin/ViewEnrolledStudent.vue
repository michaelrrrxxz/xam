<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/Api/Axios';
import { Skeleton } from '@/components/ui/skeleton';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
  Table,
  TableHeader,
  TableRow,
  TableHead,
  TableBody,
  TableCell,
} from '@/components/ui/table';
import { ArrowLeft, Printer } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface EnrolledStudent {
  id: number;
  id_number: string;
  last_name: string;
  first_name: string;
  middle_name: string | null;
  course: string;
  birth_day: string;
  gender: string;
}

interface Batch {
  id: number;
  name: string;
}

interface Result {
  id: number;
  batch: Batch;
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
  total_performance_category: string;

  created_at: string;
}

const route = useRoute();
const router = useRouter();
const student = ref<EnrolledStudent | null>(null);
const results = ref<Result[]>([]);
const isLoading = ref(false);

const fetchStudentData = async () => {
  const id = route.params.id;
  if (!id) return;

  isLoading.value = true;
  try {
    // fetch student profile
    const studentRes = await api.get(`admin/enrolledStudent/${id}`);
    student.value = studentRes.data;

    // fetch all exam results for student/enrolledStudent/results
    const resultsRes = await api.get(`admin/enrolledStudent/results/${id}`);
    results.value = resultsRes.data.results;
  } catch (e) {
    console.error(e);
  } finally {
    isLoading.value = false;
  }
};

const print = async (id: number) => {
  window.open(`/results/${id}/print`, '_blank');
};

onMounted(fetchStudentData);
</script>

<template>
  <AppLayout>
    <section class="p-6 space-y-6">
      <!-- Back Button -->
      <div>
        <Button variant="ghost" size="sm" @click="router.back()">
          <ArrowLeft class="w-4 h-4 mr-2" /> Back
        </Button>
      </div>

      <!-- Student Info -->
      <Card>
        <CardHeader>
          <CardTitle>Student Information</CardTitle>
        </CardHeader>
        <CardContent>
          <template v-if="isLoading">
            <div class="space-y-3">
              <Skeleton class="h-4 w-1/2" />
              <Skeleton class="h-4 w-1/3" />
              <Skeleton class="h-4 w-1/4" />
            </div>
          </template>

          <template v-else-if="student">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-muted-foreground">ID Number</p>
                <p class="font-medium">{{ student.id_number }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Name</p>
                <p class="font-medium">
                  {{ student.last_name }}, {{ student.first_name }} {{ student.middle_name }}
                </p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Course</p>
                <p class="font-medium">{{ student.course }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Gender</p>
                <p class="font-medium capitalize">{{ student.gender }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Birth Day</p>
                <p class="font-medium">{{ student.birth_day }}</p>
              </div>
            </div>
          </template>
        </CardContent>
      </Card>

      <!-- Exam Results -->
      <!-- Exam Results -->
      <Card>
        <CardHeader>
          <CardTitle>Exam Results</CardTitle>
        </CardHeader>
        <CardContent>
          <template v-if="isLoading">
            <Skeleton class="h-6 w-full" />
            <Skeleton class="h-6 w-full" />
          </template>

          <template v-else-if="results.length > 0">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead rowspan="3">Date Taken</TableHead>
                  <TableHead rowspan="3">Batch</TableHead>
                  <TableHead rowspan="3" class="align-middle" title="Raw Score"
                    >Raw Score</TableHead
                  >
                  <TableHead rowspan="3" class="align-middle" title="Student Ability Index"
                    >SAI</TableHead
                  >
                  <TableHead colspan="2" title="Performance By Age">PBA</TableHead>
                  <TableHead colspan="2" title="Performance By Grade">PBG</TableHead>
                  <TableHead colspan="5">Verbal</TableHead>
                  <TableHead colspan="5">Non-Verbal</TableHead>
                  <TableHead rowspan="3" class="align-middle"></TableHead>
                </TableRow>
                <TableRow>
                  <TableHead rowspan="2" class="align-middle">PR</TableHead>
                  <TableHead rowspan="2" class="align-middle">S</TableHead>
                  <TableHead rowspan="2" class="align-middle">PR</TableHead>
                  <TableHead rowspan="2" class="align-middle">S</TableHead>
                  <TableHead colspan="2">VC</TableHead>
                  <TableHead colspan="2">VR</TableHead>
                  <TableHead rowspan="2" class="align-middle">Total</TableHead>
                  <TableHead colspan="2">QR</TableHead>
                  <TableHead colspan="2">FR</TableHead>
                  <TableHead rowspan="2" class="align-middle">Total</TableHead>
                </TableRow>
                <TableRow>
                  <TableHead>S</TableHead>
                  <TableHead>PC</TableHead>
                  <TableHead>S</TableHead>
                  <TableHead>PC</TableHead>
                  <TableHead>S</TableHead>
                  <TableHead>PC</TableHead>
                  <TableHead>S</TableHead>
                  <TableHead>PC</TableHead>
                </TableRow>
              </TableHeader>

              <TableBody>
                <TableRow v-for="r in results" :key="r.id">
                  <TableCell>{{ new Date(r.created_at).toLocaleDateString() }}</TableCell>
                  <TableCell>{{ r.batch.name }}</TableCell>
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

                  <!-- <TableCell>
              <Button @click="print(r.id)" class="flex items-center gap-2">
                <Printer class="w-4 h-4" />
                Print
              </Button>
            </TableCell> -->
                </TableRow>

                <TableRow v-if="!results.length">
                  <TableCell colspan="20" class="text-center py-4 text-muted-foreground">
                    No exam results found for this student.
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </template>

          <template v-else>
            <p class="text-sm text-muted-foreground">No exam results found for this student.</p>
          </template>
        </CardContent>
      </Card>
    </section>
  </AppLayout>
</template>
