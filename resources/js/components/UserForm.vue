<template>
  <form @submit.prevent="$emit('save')" class="grid gap-4 p-4">
    <div class="grid gap-2">
      <Label for="name">Name</Label>
      <Input id="name" v-model="form.name" placeholder="User name" />
      <span v-if="errors.name" class="text-sm text-red-500">{{ errors.name }}</span>
    </div>
    <div class="grid gap-2">
      <Label for="email">Email</Label>
      <Input id="email" v-model="form.email" placeholder="Email" />
    </div>
    <Button type="submit" :disabled="isSaving">Save</Button>
  </form>
</template>

<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';

defineProps<{
  form: { id: number | null; name: string; email: string };
  errors: { name?: string };
  isSaving: boolean;
}>();
defineEmits(['save']);
</script>
