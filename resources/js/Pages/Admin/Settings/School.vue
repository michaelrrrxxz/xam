<template>
  <TabsContent value="school">
    <Card class="p-6 space-y-4">
      <h2 class="text-lg font-semibold">School Settings</h2>
      <form @submit.prevent="saveSchoolSettings" class="space-y-4">
        <div>
          <Label>School Name</Label>
          <Input v-model="schoolForm.school_name" placeholder="Enter school name" />
        </div>

        <div>
          <Label>School Logo</Label>
          <Input type="file" @change="onFileChange" />
          <div v-if="preview" class="mt-2">
            <img :src="preview" class="h-16 object-contain rounded" />
          </div>
        </div>

        <Button type="submit" class="mt-4 w-full">Save School Settings</Button>
      </form>
    </Card>
  </TabsContent>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import { TabsContent } from '@/components/ui/tabs';

import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { toast } from 'vue-sonner';
import api from '@/Api/Axios';

import { SchoolSettingsForm } from '@/src/types';

// School form state
const schoolForm = ref<SchoolSettingsForm>({
  school_name: '',
  school_logo: null,
});
const preview = ref<string | null>(null);

function onFileChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (file) {
    schoolForm.value.school_logo = file;
    preview.value = URL.createObjectURL(file);
  }
}

async function saveSchoolSettings() {
  const data = new FormData();
  data.append('school_name', schoolForm.value.school_name);
  if (schoolForm.value.school_logo) {
    data.append('school_logo', schoolForm.value.school_logo);
  }
  await api.post('/admin/settings', data);
  toast.success('School updated successfully!');
}
</script>
