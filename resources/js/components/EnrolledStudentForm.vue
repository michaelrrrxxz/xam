<template>
  <form @submit.prevent="$emit('save')" class="grid gap-4 p-4">
    <!-- ID Number -->
    <div class="grid gap-2">
      <Label for="id_number">ID Number</Label>
      <Input id="id_number" v-model="form.id_number" placeholder="Enter ID Number" />
      <span v-if="errors.id_number" class="text-sm text-red-500">{{ errors.id_number }}</span>
    </div>

    <!-- Last Name -->
    <div class="grid gap-2">
      <Label for="last_name">Last Name</Label>
      <Input id="last_name" v-model="form.last_name" placeholder="Enter Last Name" />
      <span v-if="errors.last_name" class="text-sm text-red-500">{{ errors.last_name }}</span>
    </div>

    <!-- First Name -->
    <div class="grid gap-2">
      <Label for="first_name">First Name</Label>
      <Input id="first_name" v-model="form.first_name" placeholder="Enter First Name" />
      <span v-if="errors.first_name" class="text-sm text-red-500">{{ errors.first_name }}</span>
    </div>

    <!-- Middle Name -->
    <div class="grid gap-2">
      <Label for="middle_name">Middle Name</Label>
      <Input id="middle_name" v-model="form.middle_name" placeholder="Enter Middle Name" />
    </div>

    <!-- Birth Date -->
    <div class="grid gap-2">
      <Label for="birth_day">Birth Date</Label>
      <Input id="birth_day" type="date" v-model="form.birth_day" />
      <span v-if="errors.birth_day" class="text-sm text-red-500">{{ errors.birth_day }}</span>
    </div>

    <!-- Course -->
    <div class="grid gap-2">
      <Label for="course">Course</Label>
      <Select v-model="form.course" class="w-full">
        <SelectTrigger class="w-full">
          <SelectValue placeholder="Select a Course" />
        </SelectTrigger>
        <SelectContent class="w-full">
          <SelectGroup v-for="(course, i) in courses" :key="i">
            <!-- <SelectLabel>{{ course }}</SelectLabel> -->
            <SelectItem :value="course">{{ course }}</SelectItem>
          </SelectGroup>
        </SelectContent>
      </Select>

      <span v-if="errors.course" class="text-sm text-red-500">{{ errors.course }}</span>
    </div>

    <!-- Gender -->
    <div class="grid gap-2">
      <Label for="gender">Gender</Label>
      <select id="gender" v-model="form.gender" class="border rounded-md p-2">
        <option disabled value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>
      <span v-if="errors.gender" class="text-sm text-red-500">{{ errors.gender }}</span>
    </div>

    <!-- Submit Button -->
    <Button type="submit" :disabled="isSaving">Save</Button>
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

const courses = ['BSIT', 'BSCS', 'BSIS', 'BSECE', 'BSME', 'BSA', 'BSBA'];

defineProps<{
  form: {
    id_number: string;
    last_name: string;
    first_name: string;
    middle_name: string;
    birth_day: string;
    course: string;
    gender: string;
  };
  errors: Partial<
    Record<'id_number' | 'last_name' | 'first_name' | 'birth_day' | 'course' | 'gender', string>
  >;
  isSaving: boolean;
}>();

defineEmits(['save']);
</script>
