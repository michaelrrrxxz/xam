<template>
  <div class="min-h-screen bg-background flex justify-center p-4">
    <Card class="w-full max-w-4xl shadow-md">
      <CardHeader>
        <CardTitle class="text-xl font-bold text-center">Trial Exam</CardTitle>
      </CardHeader>

      <CardContent>
        <!-- Student Info -->
        <div
          v-if="studentData"
          class="mb-6 p-4 rounded-lg border bg-card shadow-sm text-card-foreground"
        >
          <h3 class="font-semibold text-lg mb-2">Student Information</h3>
          <p><strong>Name:</strong> {{ studentData.name }}</p>
          <p><strong>ID Number:</strong> {{ studentData.id_number }}</p>
          <p><strong>Course:</strong> {{ studentData.course }}</p>
          <p><strong>Gender:</strong> {{ studentData.gender }}</p>
          <p><strong>School:</strong> {{ schoolData.school }}</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center text-muted-foreground py-10">
          Loading questions...
        </div>

        <!-- Exam Questions -->
        <div v-else-if="!submitted">
          <div
            v-for="(question, index) in questions"
            :key="question.id"
            class="rounded-xl p-4 shadow-sm mb-4 transition-colors"
            :class="[
              'border',
              answers[question.id]
                ? 'border-green-500 bg-green-100 dark:bg-green-900'
                : 'border-border bg-muted',
            ]"
          >
            <!-- Question Number -->
            <h2
              class="font-semibold text-lg mb-3"
              v-html="index + 1 + '. ' + question.question"
            ></h2>

            <!-- Choices -->
            <div class="space-y-2">
              <label
                v-for="(choice, key) in getChoices(question)"
                :key="key"
                class="flex items-center space-x-2 cursor-pointer"
              >
                <input
                  type="radio"
                  class="w-4 h-4"
                  :name="'question-' + question.id"
                  :value="key"
                  v-model="answers[question.id]"
                />

                <!-- ✅ Render choice -->
                <span v-if="isImageUrl(choice)">
                  <img :src="choice" alt="choice image" class="w-20 h-20 object-cover rounded" />
                </span>
                <span v-else-if="hasHtml(choice)" v-html="choice"></span>
                <span v-else>{{ choice }}</span>
              </label>
            </div>
          </div>

          <!-- Submit Button -->
          <Button v-if="allAnswered" class="w-full mt-4 font-semibold" @click="goToExam">
            Submit and Proceed
          </Button>
        </div>

        <!-- Results -->
        <div v-else class="text-center py-10">
          <h2 class="text-2xl font-bold mb-4">Exam Finished!</h2>
          <p class="text-muted-foreground mb-6">
            You got
            <strong>{{ score }}</strong>
            out of
            <strong>{{ questions.length }}</strong>
            correct.
          </p>
          <Button @click="restartExam" class="w-full"> Retake Exam </Button>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import api from '@/Api/Axios';
import { toast } from 'vue-sonner';

// State
const studentData = ref<any | null>(null);
const schoolData = ref<any | null>(null);
const questions = ref<any[]>([]);
const answers = ref<Record<number, string>>({});
const score = ref(0);
const loading = ref(true);
const submitted = ref(false);

const router = useRouter();

const isImageUrl = (str: string) => {
  return /\.(jpeg|jpg|gif|png|webp|svg)$/i.test(str);
};

const hasHtml = (str: string) => {
  return /<\/?[a-z][\s\S]*>/i.test(str);
};

// Computed to check if all questions are answered
const allAnswered = computed(() => {
  return questions.value.length > 0 && questions.value.every((q) => answers.value[q.id]);
});

// Load data and questions
onMounted(async () => {
  // Get student and school data from localStorage
  const storedStudent = localStorage.getItem('studentData');
  const storedSchool = localStorage.getItem('schoolData');

  if (storedStudent) {
    studentData.value = JSON.parse(storedStudent);
  }

  if (storedSchool) {
    schoolData.value = JSON.parse(storedSchool);
  }

  // Fetch trial exam questions
  try {
    const response = await api.get('examinee/trial');
    questions.value = response.data;
  } catch (error) {
    console.error('Error fetching questions:', error);
  } finally {
    loading.value = false;
  }
});

// Get choices dynamically for a question
const getChoices = (question: any) => ({
  ch1: question.ch1,
  ch2: question.ch2,
  ch3: question.ch3,
  ch4: question.ch4,
  ch5: question.ch5,
});

// Submit Exam
const goToExam = () => {
  toast.success('Trial Finished! Redirecting...');
  setTimeout(() => {
    router.push('/exam');
  }, 2000);
};

// Restart Exam
const restartExam = () => {
  answers.value = {};
  score.value = 0;
  submitted.value = false;
};
</script>
