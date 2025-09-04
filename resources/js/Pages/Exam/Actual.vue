<template>
  <div class="min-h-screen bg-background flex justify-center p-4">
    <Card class="w-full max-w-4xl shadow-md">
      <CardHeader>
        <CardTitle class="text-xl font-bold text-center">Exam</CardTitle>
      </CardHeader>

      <CardContent>
   <!-- Timer + Unanswered Tracker -->
<div
  class="sticky top-0 z-50 bg-background border-b mb-6 p-3 flex justify-between items-center"
>
  <!-- Timer -->
  <div class="w-full max-w-xs">
    <Label>Time Remaining</Label>
    <div class="flex items-center gap-2">
      <Progress :value="progressPercent" :class="progressColor" class="flex-1 h-3" />
      <span class="font-mono text-sm">{{ formattedTime }}</span>
    </div>
  </div>

  <!-- Unanswered Counter -->
  <Button
    variant="outline"
    class="ml-4"
    @click="scrollToNearestUnanswered"
  >
    {{ unansweredCount }} Unanswered
  </Button>
</div>


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
        <div>
          <div
            v-for="(question, index) in questions"
            :key="question.id"
            :ref="setQuestionRef(question.id)"
            class="rounded-xl p-4 shadow-sm mb-4 transition-colors"
            :class="[
              'border',
              answers[question.id]
                ? 'border-green-500 bg-green-100 dark:bg-green-900'
                : 'border-border bg-muted',
            ]"
          >
            <!-- Question Number -->
            <h2 class="font-semibold text-lg mb-3">
              {{ index + 1 }}. {{ question.question }}
            </h2>

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
                <span>{{ choice }}</span>
              </label>
            </div>
          </div>

          <!-- Submit Button -->
          <Button
            v-if="allAnswered && !submitted"
            class="w-full mt-4 font-semibold"
            @click="examResult"
          >
            Submit Answers
          </Button>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Progress } from '@/components/ui/progress';
import api from '@/Api/Axios';
import { toast } from 'vue-sonner';

const studentData = ref<any | null>(null);
const schoolData = ref<any | null>(null);
const questions = ref<any[]>([]);
const answers = ref<Record<number, string>>({});
const loading = ref(true);
const submitted = ref(false);

const router = useRouter();

const allAnswered = computed(() => {
  return questions.value.length > 0 && questions.value.every((q) => answers.value[q.id]);
});

// ✅ Timer (45 minutes)
const totalTime = 45 * 60; // 2700 seconds
const timeLeft = ref(totalTime);
let timerInterval: number | null = null;

const formattedTime = computed(() => {
  const minutes = Math.floor(timeLeft.value / 60)
    .toString()
    .padStart(2, '0');
  const seconds = (timeLeft.value % 60).toString().padStart(2, '0');
  return `${minutes}:${seconds}`;
});

const progressPercent = computed(() => (timeLeft.value / totalTime) * 100);
const progressColor = computed(() => {
  if (progressPercent.value > 50) return 'bg-green-500';
  if (progressPercent.value > 20) return 'bg-yellow-500';
  return 'bg-red-500';
});

onMounted(async () => {
  const storedStudent = localStorage.getItem('studentData');
  const storedSchool = localStorage.getItem('schoolData');

  if (storedStudent) studentData.value = JSON.parse(storedStudent);
  if (storedSchool) schoolData.value = JSON.parse(storedSchool);

  try {
    const response = await api.get('examinee/question');
    questions.value = response.data;
  } catch (error) {
    console.error('Error fetching questions:', error);
  } finally {
    loading.value = false;
  }

  // Start timer
  timerInterval = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--;
    } else {
      clearInterval(timerInterval!);
      toast.error('Time is up! Submitting exam...');
      examResult();
    }
  }, 1000) as unknown as number;
});

onBeforeUnmount(() => {
  if (timerInterval) clearInterval(timerInterval);
});

// ✅ Unanswered tracking
const unansweredCount = computed(() => {
  return questions.value.filter((q) => !answers.value[q.id]).length;
});

const questionRefs = ref<Record<number, HTMLElement | null>>({});
const setQuestionRef = (id: number) => (el: HTMLElement | null) => {
  if (el) questionRefs.value[id] = el;
};

const scrollToNearestUnanswered = () => {
  const unanswered = questions.value
    .filter((q) => !answers.value[q.id])
    .map((q) => q.id);

  if (unanswered.length === 0) return;

  // pick the first unanswered
  const nearestId = unanswered[0];
  const target = questionRefs.value[nearestId];
  if (target) {
    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
};

const getChoices = (question: any) => ({
  ch1: question.ch1,
  ch2: question.ch2,
  ch3: question.ch3,
  ch4: question.ch4,
  ch5: question.ch5,
});

const examResult = async () => {
  if (submitted.value) return;

  try {
    const payload = {
      student_id: studentData.value?.id,
      school: schoolData.value?.school,
      answers: Object.entries(answers.value).map(([question_id, answer]) => ({
        question_id: Number(question_id),
        answer,
      })),
    };

    await api.post('examinee/result', payload);

    toast.success('Exam Finished! Redirecting...');
    submitted.value = true;

    setTimeout(() => {
      router.push({ name: 'exam-result' });
    }, 2000);
  } catch (error) {
    console.error('Error submitting exam:', error);
    toast.error('Failed to submit exam. Please try again.');
  }
};
</script>
