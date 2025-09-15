<template>
  <div class="flex flex-col items-center justify-center h-screen">
    <Card class="w-full max-w-md p-6">
      <h1 class="text-xl font-bold mb-4">Setup Your School</h1>

      <form @submit.prevent="saveSettings">
        <div class="space-y-4">
          <div>
            <Label>School Name</Label>
            <Input v-model="form.school_name" placeholder="Enter school name" />
          </div>

          <div>
            <Label>School Logo</Label>
            <Input type="file" @change="onFileChange" />
            <div v-if="preview" class="mt-2">
              <img :src="preview" class="h-16 object-contain" />
            </div>
          </div>
        </div>

        <Button type="submit" class="mt-6 w-full">Save & Continue</Button>
      </form>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import api from '@/Api/Axios';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Card } from '@/components/ui/card';

const form = ref<{ school_name: string; school_logo: File | null }>({
  school_name: '',
  school_logo: null,
});
const preview = ref<string | null>(null);

function onFileChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (file) {
    form.value.school_logo = file;
    preview.value = URL.createObjectURL(file);
  }
}

async function saveSettings() {
  const data = new FormData();
  data.append('school_name', form.value.school_name);
  if (form.value.school_logo) {
    data.append('school_logo', form.value.school_logo);
  }

  await api.post('/admin/settings', data);

  window.location.href = '/dashboard';
}
</script>
