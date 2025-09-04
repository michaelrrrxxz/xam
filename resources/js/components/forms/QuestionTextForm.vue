<template>
  <div class="flex flex-col h-full max-h-[90vh]">
    <!-- Scrollable form content -->
    <div class="flex-1 overflow-y-auto p-4 space-y-6">
      <!-- Test Type -->
      <div class="grid gap-2">
        <Label for="test_type">Test Type</Label>
        <Select v-model="form.test_type">
          <SelectTrigger>
            <SelectValue placeholder="Select a test type" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="verbal">Verbal</SelectItem>
            <SelectItem value="non-verbal">Non-Verbal</SelectItem>
          </SelectContent>
        </Select>
        <p v-if="errors.test_type" class="text-sm text-red-500">{{ errors.test_type }}</p>
      </div>

      <!-- Question -->
      <div class="grid gap-2">
        <Label for="question">Question</Label>
        <Textarea
          id="question"
          v-model="form.question"
          placeholder="Enter your question here..."
          class="min-h-[100px]"
        />
        <p v-if="errors.question" class="text-sm text-red-500">{{ errors.question }}</p>
      </div>

      <!-- Choices -->
      <div v-for="choice in choices" :key="choice.key" class="grid gap-2">
        <Label :for="choice.key">Choice {{ choice.index }}</Label>
        <Input
          :id="choice.key"
          v-model="form[choice.key]"
          :placeholder="`Enter choice ${choice.index}`"
        />
      </div>

      <!-- Correct Answer -->
      <div class="mb-4">
        <label class="block mb-1 font-medium">Correct Answer</label>
        <Select v-model="form.answer">
          <SelectTrigger>
            <SelectValue placeholder="Select correct choice" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-for="choice in filledChoices" :key="choice.key" :value="choice.key">
              Choice {{ choice.index }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>

      <!-- Question Type -->
      <div class="grid gap-2">
        <Label for="qtype">Question Type</Label>
        <Select v-model="form.qtype">
          <SelectTrigger>
            <SelectValue placeholder="Select question type" />
          </SelectTrigger>
          <SelectContent>
            <template v-if="form.test_type === 'verbal'">
              <SelectItem value="verbal reasoning">Verbal Reasoning</SelectItem>
              <SelectItem value="verbal comprehension">Verbal Comprehension</SelectItem>
            </template>
            <template v-else-if="form.test_type === 'non-verbal'">
              <SelectItem value="quantitative reasoning">Quantitative Reasoning</SelectItem>
              <SelectItem value="figural reasoning">Figural Reasoning</SelectItem>
            </template>
          </SelectContent>
        </Select>
        <p v-if="errors.qtype" class="text-sm text-red-500">{{ errors.qtype }}</p>
      </div>
    </div>

    <input type="hidden" v-model="form.format" value="text" />

    <!-- Sticky footer button -->
    <div class="p-4 border-t bg-white">
      <Button type="submit" class="w-full" :disabled="isSaving" @click="$emit('save')">
        <span v-if="isSaving">Saving...</span>
        <span v-else>Save</span>
      </Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import {
  Select,
  SelectTrigger,
  SelectContent,
  SelectItem,
  SelectValue,
} from '@/components/ui/select';

// ✅ Reusable type for the form
interface QuestionForm {
  id: number | null;
  test_type: string;
  question: string;
  ch1: string;
  ch2: string;
  ch3: string;
  ch4: string;
  ch5: string;
  answer: string;
  qtype: string;
  [key: `ch${number}`]: string;
  format: 'text';
}

const props = defineProps<{
  form: QuestionForm;
  errors: Partial<Record<string, string>>;
  isSaving: boolean;
}>();

defineEmits(['save']);

// ✅ Build choices list
const choices = computed(() =>
  Array.from({ length: 5 }, (_, i) => {
    const key = `ch${i + 1}` as const;
    return { key, index: i + 1, value: props.form[key] };
  })
);

// ✅ Only keep non-empty for "Correct Answer"
const filledChoices = computed(() => choices.value.filter((c) => c.value?.trim() !== ''));
</script>
