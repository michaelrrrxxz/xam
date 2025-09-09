<script setup lang="ts">
import { ref } from "vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

const emit = defineEmits(["save"]);
const file = ref<File | null>(null);

const handleFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement;
  file.value = target.files?.[0] || null;
};

const submit = () => {
  if (file.value) {
    const formData = new FormData();
    formData.append("file", file.value);
    emit("save", formData);
  }
};
</script>

<template>
  <form @submit.prevent="submit" class="space-y-4">
    <div class="grid gap-2">
      <Label for="file-upload">Upload file</Label>
      <Input
        id="file-upload"
        type="file"
        accept=".csv,.xlsx"
        @change="handleFileChange"
      />
    </div>

    <Button type="submit" class="w-full">
      Upload
    </Button>
  </form>
</template>
