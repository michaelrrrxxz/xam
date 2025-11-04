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

      <!-- Question Image -->
      <div class="grid gap-2">
        <Label for="question_image">Question Image</Label>
        <input
          type="file"
          accept="image/*"
          @change="(e) => handleFileUpload(e, 'question_image')"
        />
        <div v-if="form.question_image?.preview" class="mt-2">
          <img
            :src="form.question_image.preview"
            alt="Question Preview"
            class="max-h-48 rounded border"
          />
        </div>
      </div>

      <!-- Choice Images -->
      <div v-for="choice in choices" :key="choice.key" class="grid gap-2">
        <Label :for="choice.key">Choice {{ choice.index }}</Label>
        <input
          type="file"
          accept="image/*"
          @change="(e) => handleFileUpload(e, `${choice.key}_image`)"
        />
        <div v-if="(form[`${choice.key}_image`] as UploadableField | null)?.preview" class="mt-2">
          <img
            :src="(form[`${choice.key}_image`] as UploadableField).preview"
            class="max-h-32 rounded border"
          />
        </div>
      </div>

      <!-- Correct Answer -->
      <div class="mb-4">
        <label class="block mb-1 font-medium">Correct Answer</label>
        <Select v-model="form.answer">
          <SelectTrigger>
            <SelectValue placeholder="Select correct choice" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-for="choice in choices" :key="choice.key" :value="choice.key">
              Choice {{ choice.index }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>

      <!-- Question Type -->
    </div>

    <!-- Force photo mode -->
    <input type="hidden" v-model="form.format" value="photo" />

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

interface UploadableField {
  file: File | null;
  preview: string | null;
}

interface QuestionForm {
  id: number | null;
  test_type: string;
  question: string;
  question_image: UploadableField | null;
  ch1: string;
  ch1_image: UploadableField | null;
  ch2: string;
  ch2_image: UploadableField | null;
  ch3: string;
  ch3_image: UploadableField | null;
  ch4: string;
  ch4_image: UploadableField | null;
  ch5: string;
  ch5_image: UploadableField | null;
  answer: string;
  qtype: string;
  format: 'text' | 'photo';
}

const props = defineProps<{
  form: QuestionForm;
  errors: Partial<Record<string, string>>;
  isSaving: boolean;
}>();

defineEmits(['save']);

// Generate choice fields dynamically
const choices = computed(() =>
  Array.from({ length: 5 }, (_, i) => {
    const key = `ch${i + 1}` as const;
    return { key, index: i + 1 };
  })
);

// Handle file uploads and set preview
const handleFileUpload = (e: Event, field: keyof QuestionForm | string) => {
  const target = e.target as HTMLInputElement;
  const files = target.files;
  if (files && files[0]) {
    (props.form as any)[field] = {
      file: files[0],
      preview: URL.createObjectURL(files[0]),
    } as UploadableField;
  }
};
</script>
