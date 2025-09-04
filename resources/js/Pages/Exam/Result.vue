<template>
  <div class="min-h-screen flex justify-center items-center bg-background p-4">
    <Card class="w-full max-w-2xl shadow-md p-6">
      <CardHeader class="text-center">
        <CardTitle class="text-2xl font-bold">Exam Results</CardTitle>
        <CardDescription class="text-muted-foreground mt-1">
          Here's a summary of your performance
        </CardDescription>
      </CardHeader>

      <CardContent>
        <div v-if="loading" class="text-center text-gray-500 py-10">Loading your results...</div>

        <div v-else-if="result">
          <div class="grid grid-cols-2 gap-4">
            <div class="font-semibold">Total Score:</div>
            <div>{{ result.total_score }}</div>

            <div class="font-semibold">Verbal:</div>
            <div>{{ result.verbal }}</div>

            <div class="font-semibold">Non-Verbal:</div>
            <div>{{ result.non_verbal }}</div>

            <div class="font-semibold">Verbal Reasoning:</div>
            <div>{{ result.verbal_reasoning }}</div>

            <div class="font-semibold">Verbal Comprehension:</div>
            <div>{{ result.verbal_comprehension }}</div>

            <div class="font-semibold">Quantitative Reasoning:</div>
            <div>{{ result.quantitative_reasoning }}</div>

            <div class="font-semibold">Figural Reasoning:</div>
            <div>{{ result.figural_reasoning }}</div>
          </div>

          <div class="mt-6 text-center">
            <Button @click="restartExam" class="font-semibold">Close</Button>
          </div>
        </div>

        <div v-else class="text-center text-red-500">No results found.</div>
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

const loading = ref(true);
const result = ref<any | null>(null);
const router = useRouter();

onMounted(async () => {
  try {
    const storedStudent = localStorage.getItem('studentData');
    if (!storedStudent) {
      toast.error('No student data found. Please retake the exam.');
      return router.push('/');
    }

    const student = JSON.parse(storedStudent);
    const response = await api.get(`/result/${student.id}`);
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
