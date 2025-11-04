<template>
  <div class="min-h-screen bg-background flex justify-center p-4">
    <Card class="w-full max-w-4xl shadow-md">
      <!-- Exam Header -->
      <CardHeader class="flex flex-col items-center space-y-3">
        <Logo :logo-size="24" />
        <CardTitle class="text-2xl font-bold tracking-wide text-center"> Trial </CardTitle>
      </CardHeader>

      <!-- Student Info Card -->
      <StudentInfoCard :studentData="studentData" :schoolData="schoolData" />

      <!-- Exam Questions -->
      <CardContent>
        <div>
          <div
            v-for="(question, index) in questions"
            :key="question.id"
            :ref="setQuestionRef(question.id)"
            class="rounded-xl p-4 shadow-sm mb-6 border transition-colors"
            :class="
              (index + 1) % 2 === 0
                ? 'bg-yellow-100 border-yellow-400 dark:bg-yellow-900 dark:border-yellow-600'
                : 'bg-muted border-border dark:bg-muted-dark dark:border-border-dark'
            "
          >
            <!-- Question Number Badge + Text -->
            <div class="flex items-center gap-3 mb-3">
              <div
                class="w-10 h-10 flex items-center justify-center rounded-full bg-primary text-primary-foreground font-bold text-lg"
              >
                {{ index + 1 }}
              </div>
              <h2 class="font-semibold text-lg" v-html="question.question"></h2>
            </div>

            <!-- Choices -->
            <div class="space-y-2">
              <label
                v-for="(choice, choiceIndex) in getChoices(question)"
                :key="choiceIndex"
                class="flex items-center space-x-3 cursor-pointer p-3 border rounded-lg transition"
                :class="
                  answers[question.id] === choiceIndex
                    ? 'border-green-500 bg-green-100 dark:bg-green-900'
                    : 'border-border bg-background dark:border-border-dark dark:bg-background-dark hover:bg-accent dark:hover:bg-accent-dark'
                "
              >
                <!-- Choice Letter Badge -->
                <div
                  class="w-8 h-8 flex items-center justify-center rounded-full font-bold"
                  :class="
                    answers[question.id] === choiceIndex
                      ? 'bg-green-600 text-white'
                      : 'bg-primary text-primary-foreground'
                  "
                >
                  {{
                    (index % 2 === 0 ? ['A', 'B', 'C', 'D', 'E'] : ['F', 'G', 'H', 'I', 'J'])[
                      choiceIndex
                    ]
                  }}
                </div>

                <!-- Hidden Radio -->
                <input
                  type="radio"
                  class="sr-only peer"
                  :name="'question-' + question.id"
                  :value="choiceIndex"
                  v-model="answers[question.id]"
                />

                <!-- Choice Text/Image -->
                <div class="flex-1">
                  <span v-if="isImageUrl(choice)">
                    <img
                      :src="choice"
                      alt="choice image"
                      class="w-32 h-32 object-contain rounded border dark:border-border-dark"
                    />
                  </span>
                  <span v-else-if="hasHtml(choice)" v-html="choice"></span>
                  <span v-else>{{ choice }}</span>
                </div>
              </label>
            </div>
          </div>

          <!-- Submit Button -->
          <Button
            v-if="allAnswered && !submitted"
            class="w-full mt-4 font-semibold"
            @click="goToExam"
          >
            Submit Answers
          </Button>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { useRouter } from 'vue-router';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import api from '@/Api/Axios';
import { toast } from 'vue-sonner';
import type { ComponentPublicInstance } from 'vue';

import Logo from '@/components/Logo.vue';

import StudentInfoCard from '@/components/StudentInfoCard.vue';

const studentData = ref<any | null>(null);
const schoolData = ref<any | null>(null);
const questions = ref<any[]>([]);

// ✅ answers are stored by question.id → selected choice index (number)
const answers = ref<Record<number, number>>({});

const loading = ref(true);
const submitted = ref(false);

const isImageUrl = (str: string) => /\.(jpeg|jpg|gif|png|webp|svg)$/i.test(str);
const hasHtml = (str: string) => /<\/?[a-z][\s\S]*>/i.test(str);

const router = useRouter();

const allAnswered = computed(
  () =>
    questions.value.length > 0 && questions.value.every((q) => answers.value[q.id] !== undefined)
);


onMounted(async () => {


  const storedStudent = localStorage.getItem('studentData');
  const storedSchool = localStorage.getItem('schoolData');
  if (storedStudent) studentData.value = JSON.parse(storedStudent);
  if (storedSchool) schoolData.value = JSON.parse(storedSchool);

  try {
    const response = await api.get('examinee/trial');
    questions.value = response.data;
  } catch (error) {
    console.error('Error fetching questions:', error);
  } finally {
    loading.value = false;
  }
});

const questionRefs = ref<Record<number, Element | ComponentPublicInstance | null>>({});
const setQuestionRef = (id: number) => (el: Element | ComponentPublicInstance | null) => {
  if (el) questionRefs.value[id] = el;
};

// ✅ Choices now return an array (so choiceIndex is always a number)
const getChoices = (question: any): string[] => [
  question.ch1,
  question.ch2,
  question.ch3,
  question.ch4,
  question.ch5,
];

const goToExam = () => {
  toast.success('Trial Finished! Redirecting...');
  setTimeout(() => {
    router.push('/exam');
  }, 2000);
};
</script>
