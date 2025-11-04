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
            <SelectItem value="Verbal">Verbal</SelectItem>
            <SelectItem value="non-verbal">Non-Verbal</SelectItem>
          </SelectContent>
        </Select>
        <p v-if="errors.test_type" class="text-sm text-red-500">{{ errors.test_type }}</p>
      </div>

      <div class="grid gap-2">
        <Label for="qtype">Question Type</Label>
        <Select v-model="form.qtype">
          <SelectTrigger>
            <SelectValue placeholder="Select question type" />
          </SelectTrigger>
          <SelectContent>
            <template v-if="form.test_type === 'Verbal'">
              <SelectItem value="Verbal Reasoning">Verbal Reasoning</SelectItem>
              <SelectItem value="Verbal Comprehension">Verbal Comprehension</SelectItem>
            </template>
            <template v-else-if="form.test_type === 'non-verbal'">
              <SelectItem value="Quantitative Reasoning">Quantitative Reasoning</SelectItem>
              <SelectItem value="Figural Reasoning">Figural Reasoning</SelectItem>
            </template>
          </SelectContent>
        </Select>
        <p v-if="errors.qtype" class="text-sm text-red-500">{{ errors.qtype }}</p>
      </div>

      <!-- Question -->
      <div class="grid gap-2">
        <Label for="question">Question</Label>
        <QuillEditor
          v-model:content="form.question"
          content-type="html"
          theme="snow"
          toolbar="bold italic underline"
          placeholder="Enter your question here..."
          class="min-h-[120px] border rounded"
        />
        <p v-if="errors.question" class="text-sm text-red-500">{{ errors.question }}</p>
      </div>

      <!-- Choices -->
      <div v-for="choice in choices" :key="choice.key" class="grid gap-2">
        <Label :for="choice.key">Choice {{ choice.index }}</Label>
        <QuillEditor
          v-model:content="form[choice.key]"
          content-type="html"
          theme="snow"
          toolbar="bold italic underline"
          :placeholder="`Enter choice ${choice.index}`"
          class="min-h-[80px] border rounded"
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
    </div>

    <!-- Force text mode -->
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
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import {
  Select,
  SelectTrigger,
  SelectContent,
  SelectItem,
  SelectValue,
} from '@/components/ui/select';

import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

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
  format: 'text' | 'photo';
}

const props = defineProps<{
  form: QuestionForm;
  errors: Partial<Record<string, string>>;
  isSaving: boolean;
}>();

defineEmits(['save']);

const choices = computed(() =>
  Array.from({ length: 5 }, (_, i) => {
    const key = `ch${i + 1}` as const;
    return { key, index: i + 1, value: props.form[key] };
  })
);

const filledChoices = computed(() => choices.value.filter((c) => c.value?.trim() !== ''));
</script>
