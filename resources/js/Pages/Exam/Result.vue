<template>
  <div class="min-h-screen flex justify-center items-center bg-background p-4">
    <Card class="w-full max-w-3xl shadow-md p-6">
      <CardHeader class="text-center">
        <CardTitle class="text-2xl font-bold">Exam Results</CardTitle>
        <CardDescription> Here's a summary of your performance </CardDescription>
      </CardHeader>

      <CardContent>
        <!-- Loading -->
        <div v-if="loading" class="text-center text-muted-foreground py-10">
          Loading your results...
        </div>

        <!-- Loaded -->
        <div v-else-if="result" class="space-y-6">
          <!-- Student Info -->
          <div class="border-b pb-3">
            <div class="text-lg font-semibold">
              Name: {{ result.student.last_name }}, {{ result.student.first_name }}
              {{ result.student.middle_name }}
            </div>
            <p class="text-sm text-muted-foreground">Batch: {{ result.batch.name }}</p>
            <p class="text-sm text-muted-foreground">Group: {{ information?.group_abc }}</p>
          </div>

          <!-- Overall Scores -->
          <div>
            <h3 class="text-md font-semibold mb-2">Overall Performance</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="p-3 rounded bg-muted">
                <p class="text-xs text-muted-foreground">Total Score</p>
                <p class="font-semibold">{{ result.total_score }}</p>
              </div>
              <div class="p-3 rounded bg-muted">
                <p class="text-xs text-muted-foreground">SAI</p>
                <p class="font-semibold">{{ result.sai_t }}</p>
              </div>
              <div class="p-3 rounded bg-muted">
                <p class="text-xs text-muted-foreground">Verbal</p>
                <p class="font-semibold">{{ result.verbal }}</p>
              </div>
              <div class="p-3 rounded bg-muted">
                <p class="text-xs text-muted-foreground">Non-Verbal</p>
                <p class="font-semibold">{{ result.non_verbal }}</p>
              </div>
            </div>
          </div>

          <!-- Subtests -->
          <div>
            <h3 class="text-md font-semibold mb-2">Subtests</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="p-3 border rounded">
                <p class="text-xs text-muted-foreground">PBA (PR)</p>
                <p class="font-medium">{{ result.pba_pr_t }}</p>
              </div>
              <div class="p-3 border rounded">
                <p class="text-xs text-muted-foreground">PBA (S)</p>
                <p class="font-medium">{{ result.pba_s_t }}</p>
              </div>
              <div class="p-3 border rounded">
                <p class="text-xs text-muted-foreground">PBG (PR)</p>
                <p class="font-medium">{{ result.pbg_pr_t }}</p>
              </div>
              <div class="p-3 border rounded">
                <p class="text-xs text-muted-foreground">PBG (S)</p>
                <p class="font-medium">{{ result.pbg_s_t }}</p>
              </div>
            </div>
          </div>

          <!-- Cognitive Domains -->
          <div>
            <h3 class="text-md font-semibold mb-2">Cognitive Domains</h3>
            <div class="grid gap-3">
              <div class="flex justify-between items-center p-3 rounded bg-muted">
                <span>Verbal Comprehension</span>
                <span
                  >{{ result.verbal_comprehension }} ({{
                    result.verbal_comprehension_category
                  }})</span
                >
              </div>
              <div class="flex justify-between items-center p-3 rounded bg-muted">
                <span>Verbal Reasoning</span>
                <span>{{ result.verbal_reasoning }} ({{ result.verbal_reasoning_category }})</span>
              </div>
              <div class="flex justify-between items-center p-3 rounded bg-muted">
                <span>Quantitative Reasoning</span>
                <span
                  >{{ result.quantitative_reasoning }} ({{
                    result.quantitative_reasoning_category
                  }})</span
                >
              </div>
              <div class="flex justify-between items-center p-3 rounded bg-muted">
                <span>Figural Reasoning</span>
                <span
                  >{{ result.figural_reasoning }} ({{ result.figural_reasoning_category }})</span
                >
              </div>
            </div>
          </div>

          <!-- Performance Categories -->
          <div>
            <h3 class="text-md font-semibold mb-2">Performance Categories</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="p-3 border rounded">
                <p class="text-xs text-muted-foreground">Total Performance</p>
                <p class="font-medium">{{ result.total_performance_category }}</p>
              </div>
              <div class="p-3 border rounded">
                <p class="text-xs text-muted-foreground">Verbal Performance</p>
                <p class="font-medium">{{ result.verbal_performance_category }}</p>
              </div>
              <div class="p-3 border rounded">
                <p class="text-xs text-muted-foreground">Non-Verbal Performance</p>
                <p class="font-medium">{{ result.non_verbal_performance_category }}</p>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-6 text-center">
            <Button @click="restartExam" class="font-semibold">Close</Button>
          </div>
        </div>

        <!-- No Results -->
        <div v-else class="text-center text-destructive">No results found.</div>
      </CardContent>
    </Card>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import api from '@/Api/Axios';
import { toast } from 'vue-sonner';

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
  id: number;
  student: Student;
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
  verbal_performance_category: string;
  non_verbal_performance_category: string;
}

const loading = ref(true);
const result = ref<Result | null>(null);
const information = ref<any>(null);
const router = useRouter();

onMounted(async () => {
  try {
    const storedInformation = localStorage.getItem('info');
    const storedStudent = localStorage.getItem('studentData');
    if (!storedStudent) {
      toast.error('No student data found. Please retake the exam.');
      return router.push('/');
    }
    const student = JSON.parse(storedStudent);
    information.value = storedInformation ? JSON.parse(storedInformation) : null;
    const response = await api.get(`examinee/result/${student.id}`);
    result.value = response.data.result;
    result.value = response.data.result;
  } catch (error) {
    console.error(error);
    toast.error('Failed to load results.');
  } finally {
    loading.value = false;
  }
});

const restartExam = () => {
  localStorage.removeItem('studentData');
  localStorage.removeItem('schoolData');
  router.push('/');
};
</script>
