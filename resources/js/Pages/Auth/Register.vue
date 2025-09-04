<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { toast } from 'vue-sonner';
import api from '@/Api/Axios';

const router = useRouter();

// Reactive form state
const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

// State for errors
const error = ref<string | null>(null);
const validationErrors = ref<Record<string, string[]>>({});

// Register handler
const handleRegister = async () => {
  error.value = null;
  validationErrors.value = {};

  try {
    const response = await api.post('/register', form.value);

    // Store token
    const token = response.data.token;
    localStorage.setItem('token', token);

    // Log user for debugging
    console.log('Registered user:', response.data.user);

    toast.success('Registration successful! Redirecting to dashboard...');

    setTimeout(() => {
      router.push('/dashboard');
    }, 2000);
  } catch (err: any) {
    // Detect error response
    if (err.response) {
      if (err.response.status === 422) {
        // Laravel validation errors
        validationErrors.value = err.response.data.errors;
        toast.error('Please fix the validation errors below.');
      } else {
        error.value = err.response.data.message || 'Something went wrong.';
        toast.error(err.value);
      }
    } else {
      error.value = 'Network error. Please try again.';
      toast.error(error.value);
    }
  }
};
</script>

<template>
  <AuthLayout>
    <form @submit.prevent="handleRegister" class="grid gap-4">
      <!-- General error -->
      <div v-if="error" class="text-red-600 text-sm">{{ error }}</div>

      <!-- Name-->
      <div class="grid gap-2">
        <Label>Name</Label>
        <Input type="text" v-model="form.name" placeholder="Your Name" />
        <span v-if="validationErrors.name" class="text-red-600 text-xs">
          {{ validationErrors.name[0] }}
        </span>
      </div>

      <!-- Email -->
      <div class="grid gap-2">
        <Label>Email</Label>
        <Input type="email" v-model="form.email" placeholder="you@example.com" />
        <span v-if="validationErrors.email" class="text-red-600 text-xs">
          {{ validationErrors.email[0] }}
        </span>
      </div>

      <!-- Password -->
      <div class="grid gap-2">
        <Label>Password</Label>
        <Input type="password" v-model="form.password" placeholder="********" />
        <span v-if="validationErrors.password" class="text-red-600 text-xs">
          {{ validationErrors.password[0] }}
        </span>
      </div>

      <!-- Confirm Password -->
      <div class="grid gap-2">
        <Label>Confirm Password</Label>
        <Input type="password" v-model="form.password_confirmation" placeholder="********" />
        <span v-if="validationErrors.password_confirmation" class="text-red-600 text-xs">
          {{ validationErrors.password_confirmation[0] }}
        </span>
      </div>

      <!-- Submit -->
      <Button type="submit" class="w-full">Register</Button>
    </form>
  </AuthLayout>
</template>
