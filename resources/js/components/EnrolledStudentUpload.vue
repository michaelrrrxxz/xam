<template>
  <form @submit.prevent="$emit('upload')" class="grid gap-4 p-4">
    <!-- Upload Field -->
    <div class="grid gap-2">
      <Label for="upload_file">Upload File</Label>
      <Input
        id="upload_file"
        type="file"
        accept=".csv, .xlsx"
        @change="onFileChange"
        class="w-full"
      />

      <!-- Show file name if selected -->
      <span v-if="form.upload_file" class="text-sm text-gray-600">
        Selected: {{ form.upload_file.name }}
      </span>
    </div>

    <!-- Save Button -->
    <Button type="submit" :disabled="isSaving">
      {{ isSaving ? 'Uploading...' : 'Save' }}
    </Button>
  </form>
</template>

<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';

type UploadForm = {
  upload_file: File | null;
};

const props = defineProps<{
  form: UploadForm;
  isSaving: boolean;
}>();

const emit = defineEmits<{
  (e: 'upload'): void;
  (e: 'update:form', value: UploadForm): void;
}>();

// Handle file selection and emit update instead of mutating prop directly
const onFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    emit('update:form', { ...props.form, upload_file: target.files[0] });
  }
};
</script>
